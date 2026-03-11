<?php

namespace App\Models\Venta;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'discount_type',
        'total',
        'payment_method',
        'transfer_reference',
        'status',
        'notes',
        'user_id',
        'closed_at',
    ];

    protected $casts = [
        'subtotal'      => 'decimal:2',
        'discount'      => 'decimal:2',
        'total'         => 'decimal:2',
        'discount_type' => 'string',
        'closed_at'     => 'datetime',
    ];

    // ── Boot ────────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function (Sale $sale) {
            if (empty($sale->folio)) {
                $next = (static::max('id') ?? 0) + 1;
                $sale->folio = 'VTA-' . str_pad($next, 5, '0', STR_PAD_LEFT);
            }
            if (empty($sale->user_id) && auth()->check()) {
                $sale->user_id = auth()->id();
            }
        });
    }

    // ── Relaciones ──────────────────────────────

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
        $subtotal = (float) $this->items()->sum('subtotal');

        $discountAmount = $this->discount_type === 'percentage'
            ? round($subtotal * ((float) $this->discount / 100), 2)
            : (float) $this->discount;

        $this->update([
            'subtotal' => $subtotal,
            'total'    => max(0, $subtotal - $discountAmount),
        ]);
    }

    public function getDiscountAmountAttribute(): float
    {
        if ($this->discount_type === 'percentage') {
            return round((float) $this->subtotal * ((float) $this->discount / 100), 2);
        }
        return (float) $this->discount;
    }

    /**
     * Cierra la venta.
     */
    public function close(string $paymentMethod = 'cash', ?string $transferReference = null): void
    {
        $this->recalculate();

        $this->update([
            'status'             => 'closed',
            'payment_method'     => $paymentMethod,
            'transfer_reference' => $paymentMethod === 'transfer' ? $transferReference : null,
            'closed_at'          => now(),
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