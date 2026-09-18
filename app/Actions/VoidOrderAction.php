<?php

namespace App\Actions;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Exception;

class VoidOrderAction
{
    public function execute(Order $order, int $adminId, string $reason): Order
    {
        return DB::transaction(function () use ($order, $adminId, $reason) {
            // Kunci baris order
            $order = Order::where('id', $order->id)->lockForUpdate()->firstOrFail();

            if ($order->status === 'cancelled') {
                throw new Exception('Pesanan ini sudah dibatalkan sebelumnya.');
            }

            // Kembalikan stok untuk setiap item yang ada di order
            foreach ($order->items as $item) {
                Product::where('id', $item->product_id)
                    ->lockForUpdate()
                    ->increment('stock', $item->quantity);
            }

            // Ubah status dan simpan log audit pembatalan
            $order->update([
                'status'              => 'cancelled',
                'cancellation_reason' => $reason,
                'cancelled_by'        => $adminId,
                'cancelled_at'        => now(),
            ]);

            return $order;
        });
    }
}