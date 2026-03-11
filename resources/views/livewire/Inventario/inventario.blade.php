<div class="space-y-6">

    {{-- ── Widgets de resumen ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        {{-- Entradas del mes --}}
        <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Entradas (mes)</span>
                <div class="p-2 bg-emerald-50 dark:bg-emerald-950 rounded-lg">
                    <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">+{{ number_format($totalEntradas, 2) }}</p>
            <p class="text-xs text-zinc-400 mt-1">Unidades ingresadas</p>
        </div>

        {{-- Salidas del mes --}}
        <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Salidas (mes)</span>
                <div class="p-2 bg-red-50 dark:bg-red-950 rounded-lg">
                    <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">-{{ number_format($totalSalidas, 2) }}</p>
            <p class="text-xs text-zinc-400 mt-1">Unidades egresadas</p>
        </div>

        {{-- Productos bajo mínimo --}}
        <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 p-5 {{ $productosBajoMinimo > 0 ? 'border-red-300 dark:border-red-800' : '' }}">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Bajo mínimo</span>
                <div class="p-2 {{ $productosBajoMinimo > 0 ? 'bg-red-50 dark:bg-red-950' : 'bg-zinc-50 dark:bg-zinc-800' }} rounded-lg">
                    <svg class="w-4 h-4 {{ $productosBajoMinimo > 0 ? 'text-red-600 dark:text-red-400' : 'text-zinc-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold {{ $productosBajoMinimo > 0 ? 'text-red-600 dark:text-red-400' : 'text-zinc-900 dark:text-zinc-100' }}">
                {{ $productosBajoMinimo }}
            </p>
            <p class="text-xs text-zinc-400 mt-1">productos con alerta</p>
        </div>

        {{-- Mermas del mes --}}
        <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 p-5 {{ $mermasMes > 0 ? 'border-amber-300 dark:border-amber-800' : '' }}">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Mermas (mes)</span>
                <div class="p-2 {{ $mermasMes > 0 ? 'bg-amber-50 dark:bg-amber-950' : 'bg-zinc-50 dark:bg-zinc-800' }} rounded-lg">
                    <svg class="w-4 h-4 {{ $mermasMes > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-zinc-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold {{ $mermasMes > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-zinc-900 dark:text-zinc-100' }}">
                {{ number_format($mermasMes, 2) }}
            </p>
            <p class="text-xs text-zinc-400 mt-1">unidades perdidas</p>
        </div>

         {{-- Valor del inventario --}}
        <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Valor inventario</span>
                <div class="p-2 bg-cyan-50 dark:bg-cyan-950 rounded-lg">
                    <svg class="w-4 h-4 text-cyan-600 dark:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">L {{ number_format($valorInventario, 2) }}</p>
            <p class="text-xs text-zinc-400 mt-1">Stock × costo de compra</p>
        </div>

    </div>

    {{-- ── Tabla ── --}}
    {{ $this->table }}

</div>