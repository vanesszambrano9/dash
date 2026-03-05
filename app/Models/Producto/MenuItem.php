<?php

namespace App\Models\Producto;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\Producto\Product;
use App\Models\Categoria\Category;
use App\Models\Venta\SaleItem;

class MenuItem extends Model
{
    protected $fillable = [
        'product_id', 
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

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class, 'menu_item_id');
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
        return 'L ' . number_format($this->price, 2, '.', ',');
    }

    public function getCategoryAttribute(): ?Category
    {
        return $this->product?->category;
    }

    /**
     * Total de veces que se ha vendido este platillo.
     */
    public function getTotalSoldAttribute(): int
    {
        return $this->saleItems()->sum('quantity');
    }

    public function getMarginAttribute(): float
    {
        return $this->price - ($this->product?->purchase_price ?? 0);
    }
}