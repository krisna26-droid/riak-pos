<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'sku',
        'name',
        'cost_price',
        'selling_price',
        'image',
        'stock',
        'is_active',
    ];

    protected $casts = [
        'cost_price'    => 'integer',
        'selling_price' => 'integer',
        'stock'         => 'integer',
        'is_active'     => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // Scope untuk kemudahan query kasir
    public function scopeAvailable(Builder $query): void
    {
        $query->where('is_active', true)->where('stock', '>', 0);
    }
}
