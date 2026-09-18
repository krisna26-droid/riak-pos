<?php

namespace App\Actions;

use App\Models\CashierShift;
use App\Models\Order;
use App\Models\Product;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProcessOrderAction
{
    public function execute(int $userId, array $data): Order
    {
        return DB::transaction(function () use ($userId, $data) {
            $activeShift = CashierShift::where('user_id', $userId)
                ->whereNull('closed_at')
                ->lockForUpdate()
                ->first();

            if (!$activeShift) {
                throw new Exception("Shift kasir belum dibuka atau sudah ditutup.");
            }

            $subtotal = 0;
            $itemsData = [];

            foreach ($data['items'] as $item) {
                $product = Product::where('id', $item['id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                if (!$product->is_active) {
                    throw new Exception("Produk {$product->name} sedang tidak aktif.");
                }

                if ($product->stock < $item['quantity']) {
                    throw new Exception("Stok untuk {$product->name} tidak mencukupi (Tersisa: {$product->stock})");
                }

                $itemSubtotal = $product->selling_price * $item['quantity'];
                $subtotal += $itemSubtotal;

                $product->decrement('stock', $item['quantity']);

                $itemsData[] = [
                    'product_id' => $product->id,
                    'price'      => $product->selling_price,
                    'cost_price' => $product->cost_price,
                    'quantity'   => $item['quantity'],
                    'subtotal'   => $itemSubtotal,
                ];
            }

            $discount = $data['discount'] ?? 0;
            $taxable = max(0, $subtotal - $discount);
            $includeTax = $data['include_tax'] ?? false;
            $tax = $includeTax ? (int) round($taxable * 0.11) : 0;
            $grandTotal = $taxable + $tax;

            $status = ($data['payment_method'] === 'later') ? 'pending' : 'paid';

            $cashReceived = 0;
            $cashChange = 0;

            if ($status === 'paid') {
                $cashReceived = $data['cash_received'] ?? $grandTotal;
                if ($data['payment_method'] === 'cash') {
                    if ($cashReceived < $grandTotal) {
                        throw new Exception("Nominal uang yang diterima kurang dari total tagihan.");
                    }
                    $cashChange = $cashReceived - $grandTotal;
                }
            }

            $order = Order::create([
                'invoice_number'   => 'RC-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(4)),
                'user_id'          => $userId,
                'cashier_shift_id' => $activeShift->id,
                'table_number'     => $data['table_number'] ?? null,
                'subtotal'         => $subtotal,
                'discount'         => $discount,
                'tax'              => $tax,
                'grand_total'      => $grandTotal,
                'payment_method'   => $data['payment_method'],
                'cash_received'    => $cashReceived,
                'cash_change'      => $cashChange,
                'status'           => $status,
            ]);

            $order->items()->createMany($itemsData);

            return $order->load('items.product');
        });
    }
}