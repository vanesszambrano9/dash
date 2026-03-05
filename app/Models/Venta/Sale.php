<?php

namespace App\Models\Venta;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use App\Models\Venta\SaleItem;

class Sale extends Model
{
    protected $fillable = [
        'folio',
        'table_number',
        'subtotal',
        'discount',
        'total',
        'payment_method',
        'status',
        'notes',
        'closed_at',
    ];

    protected $casts = [
        'subtotal'   => 'decimal:2',
        'discount'   => 'decimal:2',
        'total'      => 'decimal:2',
        'closed_at'  => 'datetime',
    ];

    // ── Boot ────────────────────────────────────

    protected static function booted(): void
    {
        /**
         * Genera el folio automáticamente al crear una venta.
         * Formato: VTA-00001
         */
        static::creating(function (Sale $sale) {
            if (empty($sale->folio)) {
                $last = static::max('id') ?? 0;
                $sale->folio = 'VTA-' . str_pad($last + 1, 5, '0', STR_PAD_LEFT);
            }
        });
    }

    // ── Relaciones ──────────────────────────────

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    // ── Scopes ──────────────────────────────────

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    // ── Accessors ───────────────────────────────

    public function getIsOpenAttribute(): bool
    {
        return $this->status === 'open';
    }

    public function getIsClosedAttribute(): bool
    {
        return $this->status === 'closed';
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return match($this->payment_method) {
            'cash'     => 'Efectivo',
            'card'     => 'Tarjeta',
            'transfer' => 'Transferencia',
            default    => $this->payment_method,
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'open'      => 'Abierta',
            'closed'    => 'Cerrada',
            'cancelled' => 'Cancelada',
            default     => $this->status,
        };
    }

    // ── Métodos ─────────────────────────────────

    /**
     * Recalcula subtotal y total a partir de los items.
     */
    public function recalculate(): void
    {
        $subtotal = $this->items()->sum('subtotal');

        $this->update([
            'subtotal' => $subtotal,
            'total'    => $subtotal - $this->discount,
        ]);
    }

    /**
     * Cierra la venta.
     */
    public function close(string $paymentMethod = 'cash'): void
    {
        $this->recalculate();

        $this->update([
            'status'         => 'closed',
            'payment_method' => $paymentMethod,
            'closed_at'      => now(),
        ]);
    }

    /**
     * Cancela la venta.
     */
    public function cancel(): void
    {
        $this->update(['status' => 'cancelled']);
    }
}