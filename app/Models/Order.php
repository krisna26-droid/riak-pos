<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'invoice_number',
        'user_id',
        'cashier_shift_id',
        'table_number',
        'subtotal',
        'tax',
        'discount',
        'grand_total',
        'payment_method',
        'cash_received',
        'cash_change',
        'status',
        'cancellation_reason',
        'cancelled_by',
        'cancelled_at',
    ];

    protected $casts = [
        'subtotal'      => 'integer',
        'tax'           => 'integer',
        'discount'      => 'integer',
        'grand_total'   => 'integer',
        'cash_received' => 'integer',
        'cash_change'   => 'integer',
    ];

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(CashierShift::class, 'cashier_shift_id');
    }

    public function cancelledByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }
}