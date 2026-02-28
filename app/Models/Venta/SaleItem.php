<?php

namespace App\Models\Venta;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleItem extends Model
{
    protected $fillable = [
        'sale_id',
        'menu_item_id',
        'quantity',
        'unit_price',
        'subtotal',
        'notes',
    ];

    protected $casts = [
        'quantity'   => 'integer',
        'unit_price' => 'decimal:2',
        'subtotal'   => 'decimal:2',
    ];

    // ── Boot ────────────────────────────────────

    protected static function booted(): void
    {
        /**
         * Al crear un item, copia el precio actual del platillo
         * y calcula el subtotal automáticamente.
         */
        static::creating(function (SaleItem $item) {
            if (empty($item->unit_price) && $item->menuItem) {
                $item->unit_price = $item->menuItem->price;
            }
            $item->subtotal = $item->unit_price * $item->quantity;
        });

        /**
         * Al actualizar cantidad o precio, recalcula el subtotal.
         */
        static::updating(function (SaleItem $item) {
            if ($item->isDirty(['quantity', 'unit_price'])) {
                $item->subtotal = $item->unit_price * $item->quantity;
            }
        });

        /**
         * Después de crear/actualizar/eliminar, recalcula el total de la venta.
         */
        static::saved(function (SaleItem $item) {
            $item->sale?->recalculate();
        });

        static::deleted(function (SaleItem $item) {
            $item->sale?->recalculate();
        });
    }

    // ── Relaciones ──────────────────────────────

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }
}