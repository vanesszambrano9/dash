<div class="space-y-5">

    <!-- Header -->
    <div class="flex items-start justify-between gap-4">
        <div>
            <flux:heading size="lg">Venta #{{ $sale->folio }}</flux:heading>
            <flux:subheading>
                {{ $sale->created_at->format('d/m/Y H:i') }}
                @if($sale->table_number)
                    &bull; Mesa: {{ $sale->table_number }}
                @endif
            </flux:subheading>
        </div>
        <flux:badge color="{{ $sale->status === 'closed' ? 'green' : 'red' }}" size="sm">
            {{ $sale->status === 'closed' ? 'Cerrada' : 'Cancelada' }}
        </flux:badge>
    </div>

    <flux:separator />

    <!-- Ítems -->
    @if($sale->items->isNotEmpty())
    <div>
        <div class="flex items-center gap-2 mb-3">
            <flux:heading size="sm">Ítems</flux:heading>
            <flux:badge color="zinc" size="sm">{{ $sale->items->count() }}</flux:badge>
        </div>
        <div class="space-y-1 max-h-60 overflow-y-auto pr-1">
            @foreach($sale->items as $item)
            <div class="flex justify-between items-start text-sm py-2 border-b border-zinc-100 dark:border-zinc-800 last:border-0">
                <div>
                    <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $item->menuItem?->name }}</span>
                    @if($item->notes)
                        <p class="text-xs text-zinc-400 italic mt-0.5">{{ $item->notes }}</p>
                    @endif
                </div>
                <div class="text-right shrink-0 ml-4">
                    <span class="text-zinc-400">{{ $item->quantity }}×</span>
                    <span class="ml-1 font-medium text-zinc-900 dark:text-zinc-100">L {{ number_format($item->subtotal, 2) }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <flux:separator />
    @endif

    <!-- Totales -->
    <div class="bg-zinc-50 dark:bg-zinc-800/50 rounded-xl p-4 space-y-2">
        <div class="flex justify-between text-sm text-zinc-600 dark:text-zinc-400">
            <span>Subtotal:</span>
            <span>L {{ number_format($sale->subtotal, 2) }}</span>
        </div>
        @if($sale->discount > 0)
        <div class="flex justify-between text-sm text-amber-600 dark:text-amber-400">
            <span>Descuento:</span>
            <span>- L {{ number_format($sale->discount, 2) }}</span>
        </div>
        @endif
        <div class="flex justify-between font-bold text-base text-cyan-600 dark:text-cyan-400 pt-2 border-t border-zinc-200 dark:border-zinc-700">
            <span>Total:</span>
            <span>L {{ number_format($sale->total, 2) }}</span>
        </div>
        <div class="flex items-center gap-2 pt-2">
            <flux:badge color="zinc" size="sm" icon="credit-card">
                {{ match($sale->payment_method) {
                    'cash'     => 'Efectivo',
                    'card'     => 'Tarjeta',
                    'transfer' => 'Transferencia',
                    default    => $sale->payment_method,
                } }}
            </flux:badge>
            @if($sale->closed_at)
                <flux:badge color="zinc" size="sm" icon="clock">
                    {{ $sale->closed_at->format('d/m H:i') }}
                </flux:badge>
            @endif
        </div>
    </div>

    <!-- Notas -->
    @if($sale->notes)
    <flux:separator />
    <div>
        <flux:heading size="sm" class="mb-1">Notas</flux:heading>
        <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $sale->notes }}</p>
    </div>
    @endif

</div>