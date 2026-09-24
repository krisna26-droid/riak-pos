<?php

namespace App\Http\Controllers;

use App\Actions\AppendOrderItemsAction;
use App\Actions\ProcessOrderAction;
use App\Http\Requests\Pos\CheckoutRequest;
use App\Models\CashierShift;
use App\Models\Category;
use App\Models\Order;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PosController extends Controller
{
    public function index(): View
    {
        $userId = auth()->id();

        $activeShift = CashierShift::where('user_id', $userId)
            ->whereNull('closed_at')
            ->first();

        // Jika ada shift aktif dari sesi atau hari kemarin yang belum ditutup, tutup paksa
        if ($activeShift && !$activeShift->opened_at->isToday()) {
            $cashSales = $activeShift->orders()
                ->where('payment_method', 'cash')
                ->where('status', 'paid')
                ->sum('grand_total');

            $expectedEnding = $activeShift->starting_cash + $cashSales;

            $activeShift->update([
                'expected_ending_cash' => $expectedEnding,
                'actual_ending_cash'   => $expectedEnding,
                'difference'           => 0,
                'closed_at'            => now(),
            ]);

            $activeShift = null;
        }

        $categories = Category::with(['products' => function ($query) {
            $query->where('is_active', true)->where('stock', '>', 0);
        }])->get();

        // Transformasi produk agar varian Hot & Ice digabung jadi satu kartu
        $categories->each(function ($category) {
            $grouped = [];

            foreach ($category->products as $product) {
                $name = trim($product->name);

                // Deteksi apakah nama produk mengandung (Hot) atau (Ice)
                if (preg_match('/^(.*?)\s*\((Hot|Ice)\)$/i', $name, $matches)) {
                    $baseName = trim($matches[1]);
                    $variantType = strtolower($matches[2]); // 'hot' atau 'ice'

                    if (!isset($grouped[$baseName])) {
                        $grouped[$baseName] = [
                            'id'            => $product->id,
                            'name'          => $baseName,
                            'image'         => $product->image,
                            'selling_price' => $product->selling_price,
                            'stock'         => $product->stock,
                            'has_variants'  => true,
                            'variants'      => [],
                        ];
                    }

                    $grouped[$baseName]['variants'][$variantType] = [
                        'id'            => $product->id,
                        'name'          => $product->name,
                        'selling_price' => $product->selling_price,
                        'stock'         => $product->stock,
                    ];

                    // Hitung total stok gabungan untuk kartu menu
                    $grouped[$baseName]['stock'] = array_sum(array_column($grouped[$baseName]['variants'], 'stock'));
                } else {
                    // Produk reguler tanpa varian (contoh: makanan atau signature)
                    $grouped[$name] = [
                        'id'            => $product->id,
                        'name'          => $name,
                        'image'         => $product->image,
                        'selling_price' => $product->selling_price,
                        'stock'         => $product->stock,
                        'has_variants'  => false,
                        'variants'      => [],
                    ];
                }
            }

            $category->grouped_products = array_values($grouped);
        });

        return view('pos.index', compact('categories', 'activeShift'));
    }

    public function checkout(CheckoutRequest $request, ProcessOrderAction $action): JsonResponse
    {
        try {
            $order = $action->execute(auth()->id(), $request->validated());
            $order->load('items.product:id,name');

            return response()->json([
                'message' => 'Transaksi berhasil diproses',
                'order'   => $order,
            ], 201);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /**
     * Mengambil daftar pesanan aktif (status pending / bayar nanti)
     */
    public function getPendingOrders(): JsonResponse
    {
        $orders = Order::where('status', 'pending')
            ->with(['items.product:id,name'])
            ->latest()
            ->get();

        return response()->json($orders);
    }

    /**
     * Menambahkan item menu baru ke pesanan pending (meja yang sama)
     */
    public function appendItems(Request $request, Order $order, AppendOrderItemsAction $action): JsonResponse
    {
        $request->validate([
            'items'               => ['required', 'array', 'min:1'],
            'items.*.id'          => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity'    => ['required', 'integer', 'min:1'],
            'items.*.temperature' => ['nullable', 'in:hot,ice'],
            'items.*.note'        => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $updatedOrder = $action->execute($order, $request->items);
            return response()->json([
                'message' => 'Menu tambahan berhasil dimasukkan ke pesanan.',
                'order'   => $updatedOrder,
            ]);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /**
     * Pelunasan transaksi Bayar Nanti (Open Bill)
     */
    public function settlePendingOrder(Request $request, Order $order): JsonResponse
    {
        $request->validate([
            'payment_method' => ['required', 'in:cash,qris,transfer'],
            'cash_received'  => ['required_if:payment_method,cash', 'nullable', 'integer', 'min:0'],
        ]);

        if ($order->status !== 'pending') {
            return response()->json(['message' => 'Order ini sudah lunas atau dibatalkan.'], 422);
        }

        $cashReceived = $request->input('cash_received', $order->grand_total);
        $cashChange = 0;

        if ($request->payment_method === 'cash') {
            if ($cashReceived < $order->grand_total) {
                return response()->json(['message' => 'Nominal tunai kurang dari total tagihan.'], 422);
            }
            $cashChange = $cashReceived - $order->grand_total;
        }

        $order->update([
            'payment_method' => $request->payment_method,
            'cash_received'  => $cashReceived,
            'cash_change'    => $cashChange,
            'status'         => 'paid',
        ]);

        return response()->json([
            'message' => 'Pesanan berhasil dilunasi.',
            'order'   => $order->load('items.product:id,name'),
        ]);
    }

    /**
     * Mengambil riwayat 20 nota/transaksi terakhir kasir saat ini
     */
    public function getRecentOrders(): JsonResponse
    {
        $orders = Order::where('user_id', auth()->id())
            ->with(['items.product:id,name'])
            ->latest()
            ->limit(20)
            ->get();

        return response()->json($orders);
    }

    public function openShift(Request $request): JsonResponse
    {
        $request->validate(['starting_cash' => ['required', 'integer', 'min:0']]);

        $existing = CashierShift::where('user_id', auth()->id())->whereNull('closed_at')->first();
        if ($existing) return response()->json(['message' => 'Shift aktif sudah ada.'], 422);

        $shift = CashierShift::create([
            'user_id'       => auth()->id(),
            'starting_cash' => $request->starting_cash,
            'opened_at'     => now(),
        ]);

        return response()->json(['message' => 'Shift kasir berhasil dibuka', 'shift' => $shift], 201);
    }

    public function closeShift(Request $request): JsonResponse
    {
        $request->validate(['actual_ending_cash' => ['required', 'integer', 'min:0']]);

        $shift = CashierShift::where('user_id', auth()->id())->whereNull('closed_at')->firstOrFail();
        $cashSales = $shift->orders()->where('payment_method', 'cash')->where('status', 'paid')->sum('grand_total');
        $expectedEnding = $shift->starting_cash + $cashSales;
        $actualEnding = $request->actual_ending_cash;

        $shift->update([
            'expected_ending_cash' => $expectedEnding,
            'actual_ending_cash'   => $actualEnding,
            'difference'           => $actualEnding - $expectedEnding,
            'closed_at'            => now(),
        ]);

        return response()->json(['message' => 'Shift kasir berhasil ditutup', 'shift' => $shift]);
    }
}