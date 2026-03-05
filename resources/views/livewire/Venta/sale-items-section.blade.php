<div>
    @if($saleId)
        <!-- Panel de totales con polling para actualizarse -->
        <div class="flex justify-end gap-6 mb-4 p-4 bg-gray-50 rounded-lg border">
            <div class="text-right">
                <p class="text-sm text-gray-500">Subtotal</p>
                <p class="font-semibold" wire:poll.30s>
                    L {{ number_format(\App\Models\Venta\Sale::find($saleId)?->subtotal ?? 0, 2) }}
                </p>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-500">Descuento</p>
                <p class="font-semibold text-warning">
                    - L {{ number_format(\App\Models\Venta\Sale::find($saleId)?->discount ?? 0, 2) }}
                </p>
            </div>
            <div class="text-right border-l pl-4">
                <p class="text-sm text-gray-500">TOTAL</p>
                <p class="text-xl font-bold text-success" wire:poll.30s>
                    L {{ number_format(\App\Models\Venta\Sale::find($saleId)?->total ?? 0, 2) }}
                </p>
            </div>
        </div>

        <!-- Tabla de ítems -->
        @livewire('venta.sale-item-table', ['saleId' => $saleId])
        
        <!-- Script para refrescar totales cuando cambian los ítems -->
        <script>
            document.addEventListener('sale-items-updated', (event) => {
                if (event.detail.saleId === {{ $saleId }}) {
                    setTimeout(() => {
                        window.livewire.find('{{ $this->getId() }}')?.$refresh();
                    }, 100);
                }
            });
        </script>
    @else
        <div class="p-4 text-center text-gray-500 bg-gray-50 rounded border">
            💡 Guarda la venta primero para poder agregar ítems.
        </div>
    @endif
</div>