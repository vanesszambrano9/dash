<div class="space-y-6" wire:poll.30s>

 <!-- Header -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <flux:subheading>Gestiona las ventas en progreso</flux:subheading>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <flux:badge color="yellow">{{ $sales->total() }} activa(s)</flux:badge>
            <flux:button wire:click="openCreateModal" icon="plus" variant="primary">
                Nueva Venta
            </flux:button>
        </div>
    </div>

    <!-- Widgets del día -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Ventas Hoy -->
        <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="p-2 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h1.5m-1.5 0h-1.5m-9 0H6m-1.5 0H3" /></svg>
                </div>
                <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Ventas Hoy</p>
            </div>
            <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $ventasHoy }}</p>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">cerradas hoy</p>
        </div>

        <!-- Total Hoy -->
        <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="p-2 bg-cyan-100 dark:bg-cyan-900/30 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-cyan-600 dark:text-cyan-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                </div>
                <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Total Hoy</p>
            </div>
            <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">L {{ number_format($totalHoy, 2) }}</p>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">recaudado hoy</p>
        </div>
    
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Folio o mesa..." icon="magnifying-glass" class="w-44" />
    </div>
    
    <!-- Modal de Nueva Venta -->
    @if($showCreateModal)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="fixed inset-0 bg-zinc-900/75 dark:bg-zinc-950/75" wire:click="resetForm"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white dark:bg-zinc-900 rounded-xl shadow-xl max-w-lg w-full p-6 border border-zinc-200 dark:border-zinc-700">

                <!-- Header del modal -->
                <div class="flex items-center justify-between mb-6">
                    <flux:heading size="lg">Nueva Venta</flux:heading>
                    <flux:button wire:click="resetForm" variant="ghost" icon="x-mark" size="sm" />
                </div>

                <!-- Formulario -->
                <form wire:submit.prevent="createSale" class="space-y-4">

                    <flux:field>
                        <flux:label>Mesa / Ubicación</flux:label>
                        <flux:input
                            wire:model="formData.table_number"
                            maxlength="50"
                            placeholder="Ej: Mesa 5, Barra"
                        />
                    </flux:field>

                    <flux:field>
                        <flux:label>Método de Pago</flux:label>
                        <flux:select wire:model="formData.payment_method">
                            <option value="cash">Efectivo</option>
                            <option value="card">Tarjeta</option>
                            <option value="transfer">Transferencia</option>
                        </flux:select>
                    </flux:field>

                    <flux:field>
                        <flux:label>Tipo de Descuento</flux:label>
                        <div class="flex gap-4 mt-1">
                            <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" wire:model.live="formData.discount_type" value="amount" class="accent-cyan-600 dark:accent-cyan-400">
                        <span class="text-sm text-zinc-700 dark:text-zinc-300">Monto fijo (L)</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" wire:model.live="formData.discount_type" value="percentage" class="accent-cyan-600 dark:accent-cyan-400">
                        <span class="text-sm text-zinc-700 dark:text-zinc-300">Porcentaje (%)</span>
                    </label>
                        </div>
                    </flux:field>

                    <flux:field>
                        <flux:label>Descuento</flux:label>
                        @if(($formData['discount_type'] ?? 'amount') === 'percentage')
                            <flux:input
                                type="number"
                                wire:model="formData.discount"
                                step="1"
                                min="0"
                                max="100"
                                placeholder="0 - 100"
                                suffix="%"
                            />
                        @else
                            <flux:input
                                type="number"
                                wire:model="formData.discount"
                                step="0.01"
                                min="0"
                                placeholder="0.00"
                                prefix="L"
                            />
                        @endif
                    </flux:field>

                    <flux:field>
                        <flux:label>Notas</flux:label>
                        <flux:textarea
                            wire:model="formData.notes"
                            rows="3"
                            maxlength="1000"
                            placeholder="Observaciones..."
                        />
                    </flux:field>

                    @if($errors->any())
                    <div class="rounded-lg bg-red-50 dark:bg-red-950 border border-red-200 dark:border-red-800 p-3">
                        <ul class="list-disc list-inside text-sm text-red-700 dark:text-red-400">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <div class="flex justify-end gap-3 pt-2">
                        <flux:button type="button" wire:click="resetForm" variant="ghost">
                            Cancelar
                        </flux:button>
                        <flux:button type="submit" variant="primary">
                            Crear Venta
                        </flux:button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Empty state -->
    @if($sales->isEmpty())
        <div class="text-center py-16 bg-zinc-50 dark:bg-zinc-900 rounded-xl border-2 border-dashed border-zinc-200 dark:border-zinc-700">
            <x-heroicon-o-shopping-bag class="mx-auto h-12 w-12 text-zinc-400" />
            <flux:heading class="mt-4">No hay ventas activas</flux:heading>
            <flux:subheading class="mt-1">Crea una nueva venta para comenzar</flux:subheading>
            <div class="mt-4">
                <flux:button wire:click="openCreateModal" icon="plus" variant="primary">
                    Nueva Venta
                </flux:button>
            </div>
        </div>

    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($sales as $sale)
                <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm hover:shadow-md transition-shadow">

                    <!-- Card Header -->
                    <div class="p-4 border-b border-zinc-100 dark:border-zinc-800">
                        <div class="flex items-start justify-between">
                            <div>
                                <flux:badge color="cyan">{{ $sale->folio }}</flux:badge>
                                <p class="mt-1 text-xs text-zinc-500">{{ $sale->created_at->format('d/m H:i') }}</p>
                            </div>
                            @if($sale->table_number)
                                <flux:badge color="zinc" icon="map-pin">{{ $sale->table_number }}</flux:badge>
                            @endif
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="p-4 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-zinc-500 dark:text-zinc-400">Ítems:</span>
                            <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $sale->items_count }}</span>
                        </div>
                        <div class="space-y-1 text-sm">
                            <div class="flex justify-between text-zinc-600 dark:text-zinc-400">
                                <span>Subtotal:</span>
                                <span>L {{ number_format($sale->subtotal, 2) }}</span>
                            </div>
                            @if($sale->discount > 0)
                            <div class="flex justify-between text-amber-600 dark:text-amber-400">
                                <span>Dcto.{{ ($sale->discount_type ?? 'amount') === 'percentage' ? ' ('.$sale->discount.'%)' : '' }}:</span>
                                <span>- L {{ number_format($sale->discount_amount, 2) }}</span>
                            </div>
                            @endif
                            <div class="flex justify-between font-bold text-base text-cyan-600 dark:text-cyan-400 border-t border-zinc-100 dark:border-zinc-800 pt-2 mt-1">
                                <span>Total:</span>
                                <span>L {{ number_format($sale->total, 2) }}</span>
                            </div>
                        </div>
                        @if($sale->notes)
                        <p class="text-xs text-zinc-400 italic border-t border-zinc-100 dark:border-zinc-800 pt-2">
                            {{ \Illuminate\Support\Str::limit($sale->notes, 60) }}
                        </p>
                        @endif
                    </div>

                    <!-- Card Footer -->
                    <div class="p-3 border-t border-zinc-100 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800/50 rounded-b-xl">
                        <div class="flex items-center gap-2">
                            <flux:button
                                :href="route('venta-item', ['sale' => $sale->id])"
                                icon="plus"
                                variant="primary"
                                size="sm"
                                class="flex-1"
                            >
                                Ítems
                            </flux:button>

                            <div class="relative" x-data="{ open: false }">
                                <flux:button
                                    @click="open = !open"
                                    @click.away="open = false"
                                    variant="ghost"
                                    icon="ellipsis-vertical"
                                    size="sm"
                                />
                                <div x-show="open" x-cloak
                                    class="absolute right-0 mt-1 w-48 bg-white dark:bg-zinc-900 rounded-lg shadow-lg border border-zinc-200 dark:border-zinc-700 py-1 z-10">
                                    <button wire:click="openCloseModal({{ $sale->id }})"
                                        class="w-full text-left px-4 py-2 text-sm text-emerald-700 dark:text-emerald-400 hover:bg-zinc-50 dark:hover:bg-zinc-800 flex items-center gap-2">
                                        <x-heroicon-o-check-circle class="w-4 h-4" /> Cerrar
                                    </button>
                                    <button wire:click="cancelSale({{ $sale->id }})" wire:confirm="¿Cancelar?"
                                        class="w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-zinc-50 dark:hover:bg-zinc-800 flex items-center gap-2">
                                        <x-heroicon-o-x-circle class="w-4 h-4" /> Cancelar
                                    </button>
                                    <div class="border-t border-zinc-100 dark:border-zinc-800 my-1"></div>
                                    <button wire:click="deleteSale({{ $sale->id }})" wire:confirm="¿Eliminar?"
                                        class="w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-zinc-50 dark:hover:bg-zinc-800 flex items-center gap-2">
                                        <x-heroicon-o-trash class="w-4 h-4" /> Eliminar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Paginación -->
        <div class="mt-6">{{ $sales->links() }}</div>
    @endif

    <!-- Modal Cerrar Venta -->
    @if($showCloseModal)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="fixed inset-0 bg-zinc-900/75 dark:bg-zinc-950/75" wire:click="resetCloseModal"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white dark:bg-zinc-900 rounded-xl shadow-xl max-w-md w-full p-6 border border-zinc-200 dark:border-zinc-700">

                <div class="flex items-center justify-between mb-6">
                    <flux:heading size="lg">Cerrar Venta</flux:heading>
                    <flux:button wire:click="resetCloseModal" variant="ghost" icon="x-mark" size="sm" />
                </div>

                <form wire:submit.prevent="confirmCloseSale" class="space-y-4">

                    <flux:field>
                        <flux:label>Método de Pago</flux:label>
                        <flux:select wire:model.live="closePaymentMethod">
                            <option value="cash">Efectivo</option>
                            <option value="card">Tarjeta</option>
                            <option value="transfer">Transferencia</option>
                        </flux:select>
                        @error('closePaymentMethod')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </flux:field>

                    @if($closePaymentMethod === 'transfer')
                    <flux:field>
                        <flux:label>ID / Referencia de Transferencia</flux:label>
                        <flux:input
                            wire:model="closeTransferReference"
                            maxlength="100"
                            placeholder="Ej: TRF-2026-001234"
                            autofocus
                        />
                        @error('closeTransferReference')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                        <flux:description>Ingresa el número de comprobante o referencia.</flux:description>
                    </flux:field>
                    @endif

                    @if($errors->has('closePaymentMethod') || $errors->has('closeTransferReference'))
                    <div class="rounded-lg bg-red-50 dark:bg-red-950 border border-red-200 dark:border-red-800 p-3">
                        <ul class="list-disc list-inside text-sm text-red-700 dark:text-red-400">
                            @foreach($errors->only(['closePaymentMethod','closeTransferReference']) as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <div class="flex justify-end gap-3 pt-2">
                        <flux:button type="button" wire:click="resetCloseModal" variant="ghost">
                            Cancelar
                        </flux:button>
                        <flux:button type="submit" variant="primary" icon="check-circle">
                            Confirmar Cierre
                        </flux:button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

</div>