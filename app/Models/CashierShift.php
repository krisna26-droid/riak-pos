<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashierShift extends Model
{
    protected $fillable = [
        'user_id',
        'starting_cash',
        'expected_ending_cash',
        'actual_ending_cash',
        'difference',
        'opened_at',
        'closed_at',
    ];

    protected $casts = [
        'starting_cash'        => 'integer',
        'expected_ending_cash' => 'integer',
        'actual_ending_cash'   => 'integer',
        'difference'           => 'integer',
        'opened_at'            => 'datetime',
        'closed_at'            => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
