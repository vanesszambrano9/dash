<div class="p-4 space-y-4">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-lg font-bold">Venta #{{ $sale->folio }}</h3>
            <p class="text-sm text-gray-500">
                {{ $sale->created_at->format('d/m/Y H:i') }} 
                @if($sale->table_number) • Mesa: {{ $sale->table_number }} @endif
            </p>
        </div>
        <span class="px-3 py-1 rounded-full text-xs font-medium {{ $sale->status === 'closed' ? 'bg-success-100 text-success-800' : 'bg-danger-100 text-danger-800' }}">
            {{ $sale->status === 'closed' ? 'Cerrada' : 'Cancelada' }}
        </span>
    </div>

    <!-- Totales -->
    <div class="bg-gray-50 rounded-lg p-4 space-y-2">
        <div class="flex justify-between text-sm">
            <span class="text-gray-500">Subtotal:</span>
            <span>L {{ number_format($sale->subtotal, 2) }}</span>
        </div>
        @if($sale->discount > 0)
        <div class="flex justify-between text-sm text-warning-600">
            <span>Descuento:</span>
            <span>- L {{ number_format($sale->discount, 2) }}</span>
        </div>
        @endif
        <div class="flex justify-between font-bold text-lg pt-2 border-t">
            <span>Total:</span>
            <span class="text-success-600">L {{ number_format($sale->total, 2) }}</span>
        </div>
        <div class="text-xs text-gray-500 pt-1">
            Pago: {{ match($sale->payment_method) {
                'cash' => 'Efectivo',
                'card' => 'Tarjeta',
                'transfer' => 'Transferencia',
                default => $sale->payment_method,
            } }}
            @if($sale->closed_at) • Cerrada: {{ $sale->closed_at->format('d/m H:i') }} @endif
        </div>
    </div>

    <!-- Ítems -->
    @if($sale->items->isNotEmpty())
    <div>
        <h4 class="font-medium mb-2">Ítems ({{ $sale->items->count() }})</h4>
        <div class="space-y-2 max-h-60 overflow-y-auto">
            @foreach($sale->items as $item)
            <div class="flex justify-between text-sm py-2 border-b last:border-0">
                <div>
                    <span class="font-medium">{{ $item->menuItem?->name }}</span>
                    @if($item->notes)
                    <p class="text-xs text-gray-500 italic">{{ $item->notes }}</p>
                    @endif
                </div>
                <div class="text-right">
                    <span class="text-gray-500">{{ $item->quantity }}x</span>
                    <span class="ml-2 font-medium">L {{ number_format($item->subtotal, 2) }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Notas -->
    @if($sale->notes)
    <div class="text-sm">
        <span class="font-medium text-gray-700">Notas:</span>
        <p class="text-gray-600 mt-1">{{ $sale->notes }}</p>
    </div>
    @endif
</div>