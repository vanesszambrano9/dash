<?php

namespace App\Models\Producto;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuItem extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'image',
        'is_available',
    ];

    protected $casts = [
        'price'        => 'decimal:2',
        'is_available' => 'boolean',
    ];

    // ── Relaciones ──────────────────────────────

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    // ── Scopes ──────────────────────────────────

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    // ── Accessors ───────────────────────────────

    /**
     * Precio formateado con símbolo de moneda.
     */
    public function getFormattedPriceAttribute(): string
    {
        return '$' . number_format($this->price, 2);
    }

    /**
     * Total de veces que se ha vendido este platillo.
     */
    public function getTotalSoldAttribute(): int
    {
        return $this->saleItems()->sum('quantity');
    }
}