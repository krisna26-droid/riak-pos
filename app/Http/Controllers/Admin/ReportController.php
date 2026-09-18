<?php

namespace App\Http\Controllers\Admin;

use App\Actions\VoidOrderAction;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /**
     * Menampilkan dashboard laporan keuangan dan riwayat transaksi kasir.
     */
    public function index(Request $request): View
    {
        $startDate = $request->query('start_date', now()->startOfMonth()->toDateString());
        $endDate   = $request->query('end_date', now()->toDateString());

        // Ambil data pesanan berstatus 'paid' dalam rentang tanggal
        $ordersQuery = Order::whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->where('status', 'paid');

        // Metrik Finansial
        $totalRevenue = (clone $ordersQuery)->sum('grand_total');
        $totalOrders  = (clone $ordersQuery)->count();

        // Hitung Total HPP (Cost of Goods Sold)
        $cogsData = OrderItem::whereHas('order', function ($q) use ($startDate, $endDate) {
            $q->whereDate('created_at', '>=', $startDate)
              ->whereDate('created_at', '<=', $endDate)
              ->where('status', 'paid');
        })
        ->select(
            DB::raw('SUM(price * quantity) as gross_sales'),
            DB::raw('SUM(cost_price * quantity) as total_cogs')
        )
        ->first();

        $totalCogs   = $cogsData?->getAttribute('total_cogs') ?? 0;
        $grossProfit = $totalRevenue - $totalCogs;

        // Distribusi Metode Pembayaran
        $paymentMethods = (clone $ordersQuery)
            ->select('payment_method', DB::raw('count(*) as count'), DB::raw('sum(grand_total) as total'))
            ->groupBy('payment_method')
            ->get();

        // 5 Menu Terlaris (Top Selling Items)
        $topProducts = OrderItem::whereHas('order', function ($q) use ($startDate, $endDate) {
            $q->whereDate('created_at', '>=', $startDate)
              ->whereDate('created_at', '<=', $endDate)
              ->where('status', 'paid');
        })
        ->select('product_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_amount'))
        ->with(['product:id,name,category_id', 'product.category:id,name'])
        ->groupBy('product_id')
        ->orderByDesc('total_qty')
        ->limit(5)
        ->get();

        // Riwayat Transaksi Terbaru
        $orders = (clone $ordersQuery)
            ->with(['cashier:id,name', 'items.product:id,name'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.reports.index', compact(
            'startDate',
            'endDate',
            'totalRevenue',
            'totalOrders',
            'totalCogs',
            'grossProfit',
            'paymentMethods',
            'topProducts',
            'orders'
        ));
    }

    /**
     * Mengunduh berkas laporan transaksi dalam format CSV.
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $startDate = $request->query('start_date', now()->startOfMonth()->toDateString());
        $endDate   = $request->query('end_date', now()->toDateString());

        $fileName = "laporan-penjualan-riak-coffee-{$startDate}-sd-{$endDate}.csv";

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($startDate, $endDate) {
            $file = fopen('php://output', 'w');

            // Baris header kolom CSV
            fputcsv($file, ['No. Invoice', 'Tanggal & Waktu', 'Nama Kasir', 'Metode Bayar', 'Subtotal', 'Pajak (PPN)', 'Diskon', 'Grand Total', 'Item Terjual']);

            Order::whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate)
                ->where('status', 'paid')
                ->with(['cashier:id,name', 'items.product:id,name'])
                ->chunk(100, function ($orders) use ($file) {
                    foreach ($orders as $order) {
                        $itemDescriptions = $order->items->map(fn ($item) =>
                            ($item->product->name ?? 'Produk') . ' (' . $item->quantity . 'x)'
                        )->implode('; ');

                        fputcsv($file, [
                            $order->invoice_number,
                            $order->created_at->format('Y-m-d H:i:s'),
                            $order->cashier->name ?? 'Kasir',
                            strtoupper($order->payment_method),
                            $order->subtotal,
                            $order->tax,
                            $order->discount,
                            $order->grand_total,
                            $itemDescriptions,
                        ]);
                    }
                });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Membatalkan transaksi (Void) dan mengembalikan stok produk.
     */
    public function voidOrder(Request $request, Order $order, VoidOrderAction $action): RedirectResponse
    {
        $request->validate([
            'reason' => ['required', 'string', 'max:255'],
        ]);

        try {
            $reason = $request->input('reason');
            $invoiceNumber = $order->getAttribute('invoice_number');
            $action->execute($order, auth()->id(), $reason);

            return redirect()->back()->with(
                'success',
                "Transaksi {$invoiceNumber} berhasil dibatalkan dan stok dikembalikan."
            );
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Mengambil detail transaksi dan item untuk modal tinjauan / cetak ulang struk.
     */
    public function showJson(Order $order): JsonResponse
    {
        $order->load(['cashier:id,name', 'items.product:id,name']);

        return response()->json([
            'invoice_number' => $order->invoice_number,
            'created_at'     => $order->created_at->format('d/m/Y H:i'),
            'cashier_name'   => $order->cashier->name ?? 'Kasir',
            'payment_method' => strtoupper($order->payment_method),
            'subtotal'       => $order->subtotal,
            'discount'       => $order->discount,
            'tax'            => $order->tax,
            'grand_total'    => $order->grand_total,
            'cash_received'  => $order->cash_received,
            'cash_change'    => $order->cash_change,
            'status'         => $order->status,
            'items'          => $order->items->map(function ($item) {
                return [
                    'name'     => $item->product->name ?? 'Menu',
                    'quantity' => $item->quantity,
                    'price'    => $item->price,
                    'subtotal' => $item->subtotal,
                ];
            }),
        ]);
    }
}