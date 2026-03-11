<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Models\Producto\Product;
use App\Models\Proveedor\Supplier;
use App\Models\User;

class StockMovement extends Model
{
    protected $fillable = [
        'product_id',
        'supplier_id',
        'user_id',
        'type',
        'quantity',
        'unit_cost',
        'stock_before',
        'stock_after',
        'reason',
        'reference',
    ];

    protected $casts = [
        'quantity'     => 'decimal:3',
        'unit_cost'    => 'decimal:2',
        'stock_before' => 'decimal:3',
        'stock_after'  => 'decimal:3',
    ];

    // ── Tipos disponibles ────────────────────────
    const TYPES = [
        'purchase'        => 'Compra (Entrada)',
        'sale'            => 'Venta (Salida)',
        'adjustment'      => 'Ajuste Manual',
        'waste'           => 'Merma/Pérdida',
        'return'          => 'Devolución de Cliente',
        'return_supplier' => 'Devolución a Proveedor',
        'initial'         => 'Inventario Inicial',
        'transfer'        => 'Transferencia',
    ];

    // Tipos que generan entrada (cantidad positiva)
    const ENTRY_TYPES = ['purchase', 'return', 'initial'];

    // Tipos que generan salida (cantidad negativa)
    const EXIT_TYPES = ['sale', 'waste', 'return_supplier'];

    // ── Relaciones ──────────────────────────────

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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

    public function scopeEntries($query)
    {
        return $query->where('quantity', '>', 0);
    }

    public function scopeExits($query)
    {
        return $query->where('quantity', '<', 0);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    // ── Accessors ───────────────────────────────

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function getIsEntryAttribute(): bool
    {
        return $this->quantity > 0;
    }

    public function getIsExitAttribute(): bool
    {
        return $this->quantity < 0;
    }

    /**
     * Valor total del movimiento (cantidad * costo unitario).
     */
    public function getTotalCostAttribute(): ?float
    {
        if ($this->unit_cost === null) {
            return null;
        }
        return abs((float) $this->quantity) * (float) $this->unit_cost;
    }

    /**
     * Indica si el stock_after está por debajo del mínimo del producto.
     */
    public function getBelowMinStockAttribute(): bool
    {
        if (!$this->product || $this->product->min_stock === null) {
            return false;
        }
        return (float) $this->stock_after < (float) $this->product->min_stock;
    }

    // ── Boot ─────────────────────────────────────

    protected static function boot(): void
    {
        parent::boot();

        // Registrar automáticamente el usuario autenticado
        static::creating(function (self $movement) {
            if (auth()->check() && empty($movement->user_id)) {
                $movement->user_id = auth()->id();
            }
        });
    }
}