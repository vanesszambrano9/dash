<div class="space-y-6" wire:poll.60s>

    {{-- ── KPIs principales ─────────────────────────────────── --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">

        {{-- Ingresos hoy --}}
        <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Ingresos Hoy</span>
                <div class="p-2 bg-cyan-50 dark:bg-cyan-950 rounded-lg">
                    <svg class="w-4 h-4 text-cyan-600 dark:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">L {{ number_format($this->ingresosHoy, 2) }}</p>
            <p class="text-xs text-zinc-400 mt-1">{{ $this->ventasHoy }} ventas cerradas</p>
        </div>

        {{-- Ingresos del mes --}}
        <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Ingresos del Mes</span>
                <div class="p-2 bg-emerald-50 dark:bg-emerald-950 rounded-lg">
                    <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">L {{ number_format($this->ingresosMes, 2) }}</p>
            <p class="text-xs text-zinc-400 mt-1">{{ $this->ventasMes }} ventas este mes</p>
        </div>

        {{-- Margen del mes --}}
        <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Margen Mes</span>
                <div class="p-2 bg-violet-50 dark:bg-violet-950 rounded-lg">
                    <svg class="w-4 h-4 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">L {{ number_format($this->margenMes, 2) }}</p>
            <p class="text-xs mt-1">
                <span class="{{ $this->margenPorcentaje >= 30 ? 'text-emerald-500' : ($this->margenPorcentaje >= 15 ? 'text-amber-500' : 'text-red-500') }}">
                    {{ $this->margenPorcentaje }}% del ingreso
                </span>
            </p>
        </div>

        {{-- Ventas activas --}}
        <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Ventas Activas</span>
                <div class="p-2 bg-amber-50 dark:bg-amber-950 rounded-lg">
                    <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $this->ventasActivas }}</p>
            <p class="text-xs text-zinc-400 mt-1">En progreso ahora</p>
        </div>

        {{-- Alertas de stock --}}
        <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 p-5 {{ $this->productosAlerta->count() > 0 ? 'border-red-300 dark:border-red-800' : '' }}">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Alertas Stock</span>
                <div class="p-2 {{ $this->productosAlerta->count() > 0 ? 'bg-red-50 dark:bg-red-950' : 'bg-zinc-50 dark:bg-zinc-800' }} rounded-lg">
                    <svg class="w-4 h-4 {{ $this->productosAlerta->count() > 0 ? 'text-red-600 dark:text-red-400' : 'text-zinc-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold {{ $this->productosAlerta->count() > 0 ? 'text-red-600 dark:text-red-400' : 'text-zinc-900 dark:text-zinc-100' }}">
                {{ $this->productosAlerta->count() }}
            </p>
            <p class="text-xs text-zinc-400 mt-1">de {{ $this->totalProductos }} productos</p>
        </div>
    </div>

    {{-- ── Gráfico + Top productos ────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Gráfico de ventas --}}
        <div class="lg:col-span-2 bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 p-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <flux:heading size="sm">Ventas por Día</flux:heading>
                    <flux:subheading>Ingresos de los últimos días</flux:subheading>
                </div>
                <div class="flex gap-2">
                    <flux:button
                        size="sm"
                        variant="{{ $periodoVentas === '7' ? 'primary' : 'ghost' }}"
                        wire:click="$set('periodoVentas', '7')"
                    >7d</flux:button>
                    <flux:button
                        size="sm"
                        variant="{{ $periodoVentas === '14' ? 'primary' : 'ghost' }}"
                        wire:click="$set('periodoVentas', '14')"
                    >14d</flux:button>
                    <flux:button
                        size="sm"
                        variant="{{ $periodoVentas === '30' ? 'primary' : 'ghost' }}"
                        wire:click="$set('periodoVentas', '30')"
                    >30d</flux:button>
                </div>
            </div>
            <div wire:ignore>
                <canvas id="ventasChart" height="120"></canvas>
            </div>
        </div>

        {{-- Top 5 productos --}}
        <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 p-5">
            <div class="mb-4">
                <flux:heading size="sm">Top Productos</flux:heading>
                <flux:subheading>Más vendidos este mes</flux:subheading>
            </div>
            @forelse($this->topProductos as $i => $producto)
            <div class="flex items-center justify-between py-2 {{ !$loop->last ? 'border-b border-zinc-100 dark:border-zinc-800' : '' }}">
                <div class="flex items-center gap-3">
                    <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold
                        {{ $i === 0 ? 'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-400' :
                           ($i === 1 ? 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400' :
                           'bg-zinc-50 text-zinc-500 dark:bg-zinc-900 dark:text-zinc-500') }}">
                        {{ $i + 1 }}
                    </span>
                    <div>
                        <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100 leading-tight">{{ $producto->name }}</p>
                        <p class="text-xs text-zinc-400">{{ number_format($producto->total_vendido) }} uds.</p>
                    </div>
                </div>
                <span class="text-sm font-semibold text-cyan-600 dark:text-cyan-400">
                    L {{ number_format($producto->total_ingresos, 0) }}
                </span>
            </div>
            @empty
            <div class="text-center py-8">
                <p class="text-sm text-zinc-400">Sin datos este mes</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- ── Margen por producto ─────────────────────────────────── --}}
    <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 p-5">
        <div class="flex items-center justify-between mb-4">
            <div>
                <flux:heading size="sm">Margen por Producto</flux:heading>
                <flux:subheading>Ganancia neta por ítem — últimos 30 días</flux:subheading>
            </div>
            <div class="flex items-center gap-2 text-xs text-zinc-400">
                <span class="inline-flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-emerald-500"></span>≥30%</span>
                <span class="inline-flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-amber-500"></span>15-29%</span>
                <span class="inline-flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-red-500"></span>&lt;15%</span>
            </div>
        </div>
        @forelse($this->topProductosPorMargen as $producto)
        @php $pct = $producto->total_ingresos > 0 ? min(100, ($producto->margen_total / $producto->total_ingresos) * 100) : 0; @endphp
        <div class="py-3 {{ !$loop->last ? 'border-b border-zinc-100 dark:border-zinc-800' : '' }}">
            <div class="flex items-center justify-between mb-1.5">
                <div class="flex items-center gap-2 min-w-0">
                    <span class="w-2 h-2 rounded-full shrink-0 {{ $pct >= 30 ? 'bg-emerald-500' : ($pct >= 15 ? 'bg-amber-500' : 'bg-red-500') }}"></span>
                    <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100 truncate">{{ $producto->name }}</p>
                </div>
                <div class="flex items-center gap-4 shrink-0 ml-3">
                    <span class="text-xs text-zinc-400">{{ number_format($producto->total_vendido) }} uds.</span>
                    <span class="text-xs text-zinc-500">L{{ number_format($producto->total_ingresos, 2) }} ingresos</span>
                    <span class="text-sm font-bold text-violet-600 dark:text-violet-400">L {{ number_format($producto->margen_total, 2) }}</span>
                    <span class="text-xs font-semibold w-12 text-right {{ $pct >= 30 ? 'text-emerald-600' : ($pct >= 15 ? 'text-amber-600' : 'text-red-600') }}">{{ number_format($pct, 1) }}%</span>
                </div>
            </div>
            <div class="w-full h-1.5 bg-zinc-100 dark:bg-zinc-800 rounded-full">
                <div class="h-1.5 rounded-full transition-all {{ $pct >= 30 ? 'bg-emerald-500' : ($pct >= 15 ? 'bg-amber-500' : 'bg-red-500') }}"
                     style="width: {{ max(0, $pct) }}%"></div>
            </div>
        </div>
        @empty
        <div class="text-center py-8">
            <p class="text-sm text-zinc-400">Sin datos suficientes para calcular márgenes</p>
        </div>
        @endforelse
    </div>

    {{-- ── Stock en alerta + Últimas ventas ──────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        {{-- Productos en alerta --}}
        <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 p-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <flux:heading size="sm">Stock en Alerta</flux:heading>
                    <flux:subheading>Productos bajo el mínimo</flux:subheading>
                </div>
                @if($this->productosAlerta->count() > 0)
                    <flux:badge color="red">{{ $this->productosAlerta->count() }} alerta(s)</flux:badge>
                @else
                    <flux:badge color="green">Todo OK</flux:badge>
                @endif
            </div>

            @forelse($this->productosAlerta as $producto)
            <div class="flex items-center justify-between py-2.5 border-b border-zinc-100 dark:border-zinc-800 last:border-0">
                <div>
                    <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $producto->name }}</p>
                    <p class="text-xs text-zinc-400">{{ $producto->category?->name ?? 'Sin categoría' }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm font-bold text-red-600 dark:text-red-400">{{ number_format($producto->stock, 0) }} / {{ number_format($producto->min_stock, 0) }}</p>
                    <p class="text-xs text-zinc-400">stock / mínimo</p>
                </div>
            </div>
            @empty
            <div class="text-center py-8">
                <svg class="mx-auto w-10 h-10 text-emerald-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm text-zinc-400">Todos los productos tienen stock suficiente</p>
            </div>
            @endforelse
        </div>

        {{-- Últimas ventas cerradas --}}
        <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 p-5">
            <div class="mb-4">
                <flux:heading size="sm">Últimas Ventas</flux:heading>
                <flux:subheading>Ventas cerradas recientemente</flux:subheading>
            </div>

            @forelse($this->ultimasVentas as $venta)
            <div class="flex items-center justify-between py-2.5 border-b border-zinc-100 dark:border-zinc-800 last:border-0">
                <div class="flex items-center gap-3">
                    <flux:badge color="cyan" size="sm">{{ $venta->folio }}</flux:badge>
                    <div>
                        @if($venta->table_number)
                            <p class="text-xs text-zinc-500">Mesa: {{ $venta->table_number }}</p>
                        @endif
                        <p class="text-xs text-zinc-400">{{ $venta->closed_at?->format('d/m H:i') }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm font-bold text-zinc-900 dark:text-zinc-100">L {{ number_format($venta->total, 2) }}</p>
                    <p class="text-xs text-zinc-400">
                        {{ match($venta->payment_method) {
                            'cash' => 'Efectivo',
                            'card' => 'Tarjeta',
                            'transfer' => 'Transferencia',
                            default => $venta->payment_method ?? '-'
                        } }}
                    </p>
                </div>
            </div>
            @empty
            <div class="text-center py-8">
                <p class="text-sm text-zinc-400">No hay ventas cerradas aún</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- ── Valor del inventario ────────────────────────────────── --}}
    <div class="bg-gradient-to-r from-cyan-600 to-cyan-700 dark:from-cyan-800 dark:to-cyan-900 rounded-xl p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-cyan-100 text-sm font-medium uppercase tracking-wide">Valor Total del Inventario</p>
                <p class="text-white text-3xl font-bold mt-1">L {{ number_format($this->stockTotalValor, 2) }}</p>
                <p class="text-cyan-200 text-sm mt-1">{{ $this->totalProductos }} productos activos</p>
            </div>
            <div class="opacity-20">
                <svg class="w-20 h-20 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {
    let ventasChart = null;

    function getChartColors() {
        const isDark = document.documentElement.classList.contains('dark');
        return {
            grid: isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)',
            text: isDark ? '#a1a1aa' : '#71717a',
        };
    }

    function renderChart(data) {
        const canvas = document.getElementById('ventasChart');
        if (!canvas || !window.Chart) return;
        const colors = getChartColors();
        if (ventasChart) { ventasChart.destroy(); ventasChart = null; }

        ventasChart = new Chart(canvas, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [
                    {
                        label: 'Ingresos (L)',
                        data: data.ingresos,
                        backgroundColor: 'rgba(8,145,178,0.8)',
                        borderColor: 'rgba(8,145,178,1)',
                        borderWidth: 1,
                        borderRadius: 6,
                        yAxisID: 'y',
                    },
                    {
                        label: 'N° Ventas',
                        data: data.cantidades,
                        type: 'line',
                        borderColor: 'rgba(16,185,129,1)',
                        backgroundColor: 'rgba(16,185,129,0.1)',
                        borderWidth: 2,
                        pointBackgroundColor: 'rgba(16,185,129,1)',
                        pointRadius: 4,
                        tension: 0.4,
                        fill: true,
                        yAxisID: 'y1',
                    }
                ]
            },
            options: {
                responsive: true,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { labels: { color: colors.text, boxWidth: 12, padding: 16 } },
                    tooltip: {
                        callbacks: {
                            label: ctx => ctx.datasetIndex === 0
                                ? ' L ' + ctx.parsed.y.toLocaleString('es-HN', {minimumFractionDigits: 2})
                                : ' ' + ctx.parsed.y + ' ventas'
                        }
                    }
                },
                scales: {
                    x: { grid: { color: colors.grid }, ticks: { color: colors.text } },
                    y: {
                        type: 'linear', position: 'left',
                        grid: { color: colors.grid },
                        ticks: { color: colors.text, callback: v => 'L ' + v.toLocaleString() }
                    },
                    y1: {
                        type: 'linear', position: 'right',
                        grid: { drawOnChartArea: false },
                        ticks: { color: colors.text, stepSize: 1 }
                    }
                }
            }
        });
    }

    // Render inicial con datos del servidor
    const initialData = @json($this->ventasPorDia);
    document.addEventListener('DOMContentLoaded', () => renderChart(initialData));

    // Actualizar cuando el componente despacha nuevos datos (cambio de periodo)
    document.addEventListener('livewire:initialized', () => {
        renderChart(initialData);

        Livewire.on('chart-updated', (payload) => {
            renderChart(payload.data ?? payload[0] ?? payload);
        });
    });
})();
</script>
@endpush
