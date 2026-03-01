<?php

namespace App\Models\Proveedor;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Producto\Product;
use Illuminate\Database\Inventario\StockMovement;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    protected $fillable = [
        'name',
        'contact_name',
        'phone',
        'email',
        'address',
        'rfc',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ── Relaciones ──────────────────────────────

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    // ── Scopes ──────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ── Accessors ───────────────────────────────

    /**
     * Nombre para mostrar: preferir contact_name si existe, si no el name del negocio.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->contact_name ?? $this->name;
    }
}