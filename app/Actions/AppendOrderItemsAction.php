<?php

namespace App\Actions;

use App\Models\Order;
use App\Models\Product;
use Exception;
use Illuminate\Support\Facades\DB;

class AppendOrderItemsAction
{
    public function execute(Order $order, array $items): Order
    {
        if ($order->status !== 'pending') {
            throw new Exception("Hanya pesanan berstatus 'pending' yang dapat ditambahkan item.");
        }

        return DB::transaction(function () use ($order, $items) {
            $additionalSubtotal = 0;

            foreach ($items as $item) {
                $product = Product::where('id', $item['id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                if (!$product->is_active) {
                    throw new Exception("Menu {$product->name} sedang tidak aktif.");
                }

                if ($product->stock < $item['quantity']) {
                    throw new Exception("Stok untuk {$product->name} tidak mencukupi (Tersisa: {$product->stock})");
                }

                $itemSubtotal = $product->selling_price * $item['quantity'];
                $additionalSubtotal += $itemSubtotal;

                // Kurangi stok
                $product->decrement('stock', $item['quantity']);

                // Jika produk sudah ada di pesanan ini, tambahkan quantity-nya, jika belum buat baru
                $existingItem = $order->items()->where('product_id', $product->id)->first();
                if ($existingItem) {
                    $existingItem->quantity += $item['quantity'];
                    $existingItem->subtotal += $itemSubtotal;
                    $existingItem->save();
                } else {
                    $order->items()->create([
                        'product_id' => $product->id,
                        'price'      => $product->selling_price,
                        'cost_price' => $product->cost_price,
                        'quantity'   => $item['quantity'],
                        'subtotal'   => $itemSubtotal,
                    ]);
                }
            }

            // Hitung ulang subtotal dan grand total
            $newSubtotal = $order->subtotal + $additionalSubtotal;
            $taxable = max(0, $newSubtotal - $order->discount);
            $newTax = $order->tax > 0 ? (int) round($taxable * 0.11) : 0;
            $newGrandTotal = $taxable + $newTax;

            $order->update([
                'subtotal'    => $newSubtotal,
                'tax'         => $newTax,
                'grand_total' => $newGrandTotal,
            ]);

            return $order->load('items.product');
        });
    }
}