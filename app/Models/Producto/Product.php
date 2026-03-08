<?php

namespace App\Models\Producto;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\Categoria\Category;
use App\Models\Proveedor\Supplier;
use App\Models\Inventario\StockMovement;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'supplier_id',
        'name',
        'sku',
        'unit',
        'purchase_price',
        'stock',
        'min_stock',
        'description',
        'is_active',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'stock'          => 'decimal:2',
        'min_stock'      => 'decimal:2',
        'is_active'      => 'boolean',
    ];

    // ── Relaciones ──────────────────────────────

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
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

    /**
     * Productos con stock por debajo del mínimo.
     */
    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock', '<=', 'min_stock');
    }

    // ── Accessors ───────────────────────────────

    /**
     * Indica si el producto tiene stock bajo.
     */
    public function getIsLowStockAttribute(): bool
    {
        return $this->stock <= $this->min_stock;
    }

    // ── Métodos ─────────────────────────────────

    /**
     * Incrementa el stock y registra el movimiento.
     */
    public function increaseStock(float $quantity, string $type = 'purchase', ?int $supplierId = null, ?string $reason = null): StockMovement
    {
        $before = $this->stock;
        $this->increment('stock', $quantity);

        return $this->stockMovements()->create([
            'supplier_id'  => $supplierId,
            'type'         => $type,
            'quantity'     => $quantity,
            'stock_before' => $before,
            'stock_after'  => $this->fresh()->stock,
            'reason'       => $reason,
        ]);
    }

    /**
     * Decrementa el stock y registra el movimiento.
     */
    public function decreaseStock(float $quantity, string $type = 'adjustment', ?string $reason = null): StockMovement
    {
        $before = $this->stock;
        $this->decrement('stock', $quantity);

        return $this->stockMovements()->create([
            'type'         => $type,
            'quantity'     => -$quantity,
            'stock_before' => $before,
            'stock_after'  => $this->fresh()->stock,
            'reason'       => $reason,
        ]);
    }

    /**
     * Ajusta el stock con cantidad positiva o negativa.
     * 
     * @param float $quantity Cantidad a ajustar (positiva = agregar, negativa = quitar)
     * @param string $type Tipo de movimiento: 'sale_adjustment', 'inventory_correction', etc.
     * @param string|null $reason Motivo del ajuste
     */
    public function adjustStock(float $quantity, string $type = 'adjustment', ?string $reason = null): StockMovement
    {
        $before = $this->stock;
        $after = $before + $quantity;
        
        // Evitar stock negativo
        if ($after < 0) {
            throw new \Exception("No se puede ajustar el stock de '{$this->name}' a negativo. Stock actual: {$before}, ajuste: {$quantity}");
        }
        
        $this->update(['stock' => $after]);
        
        return $this->stockMovements()->create([
            'type'         => $type,
            'quantity'     => $quantity,
            'stock_before' => $before,
            'stock_after'  => $after,
            'reason'       => $reason,
        ]);
    }
}