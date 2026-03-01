<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    protected $fillable = [
        'product_id',
        'supplier_id',
        'type',
        'quantity',
        'stock_before',
        'stock_after',
        'reason',
    ];

    protected $casts = [
        'quantity'     => 'decimal:2',
        'stock_before' => 'decimal:2',
        'stock_after'  => 'decimal:2',
    ];

    // ── Relaciones ──────────────────────────────

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    // ── Scopes ──────────────────────────────────

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopePurchases($query)
    {
        return $query->where('type', 'purchase');
    }

    public function scopeAdjustments($query)
    {
        return $query->where('type', 'adjustment');
    }

    public function scopeWastes($query)
    {
        return $query->where('type', 'waste');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    // ── Accessors ───────────────────────────────

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'purchase'   => 'Compra',
            'adjustment' => 'Ajuste',
            'waste'      => 'Merma',
            default      => $this->type,
        };
    }

    /**
     * Indica si fue una entrada (cantidad positiva).
     */
    public function getIsEntryAttribute(): bool
    {
        return $this->quantity > 0;
    }

    /**
     * Indica si fue una salida (cantidad negativa).
     */
    public function getIsExitAttribute(): bool
    {
        return $this->quantity < 0;
    }
}