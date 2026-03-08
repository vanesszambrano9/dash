<div class="space-y-6" wire:poll.30s>

    <!-- Header -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <flux:subheading>Gestiona las ventas en progreso</flux:subheading>
        </div>
        <div class="flex items-center gap-3">
            <flux:badge color="yellow">{{ $sales->total() }} activas</flux:badge>
            <flux:button wire:click="openCreateModal" icon="plus" variant="primary">
                Nueva Venta
            </flux:button>
        </div>
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
                        <flux:label>Descuento</flux:label>
                        <flux:input
                            type="number"
                            wire:model="formData.discount"
                            step="0.01"
                            min="0"
                            placeholder="0.00"
                            prefix="L"
                        />
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
                                <span>Descuento:</span>
                                <span>- L {{ number_format($sale->discount, 2) }}</span>
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