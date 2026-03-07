<?php

namespace App\Livewire\AdminGeneral\Dashboard;

use App\Models\Inventario\StockMovement;
use App\Models\Producto\Product;
use App\Models\Venta\Sale;
use App\Models\Venta\SaleItem;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.collapsable')]
class Dashboard extends Component
{
    public string $periodoVentas = '7'; // días

    // ── Métricas generales ───────────────────────

    public function getVentasActivasProperty(): int
    {
        return Sale::where('status', 'active')->count();
    }

    public function getIngresosHoyProperty(): float
    {
        return Sale::where('status', 'closed')
            ->whereDate('closed_at', today())
            ->sum('total');
    }

    public function getIngresosMesProperty(): float
    {
        return Sale::where('status', 'closed')
            ->whereMonth('closed_at', now()->month)
            ->whereYear('closed_at', now()->year)
            ->sum('total');
    }

    public function getVentasHoyProperty(): int
    {
        return Sale::where('status', 'closed')
            ->whereDate('closed_at', today())
            ->count();
    }

    public function getVentasMesProperty(): int
    {
        return Sale::where('status', 'closed')
            ->whereMonth('closed_at', now()->month)
            ->whereYear('closed_at', now()->year)
            ->count();
    }

    // ── Inventario ───────────────────────────────

    public function getTotalProductosProperty(): int
    {
        return Product::where('is_active', true)->count();
    }

    public function getProductosAlertaProperty()
    {
        return Product::where('is_active', true)
            ->whereColumn('stock', '<=', 'min_stock')
            ->with('category')
            ->get();
    }

    public function getStockTotalValorProperty(): float
    {
        return Product::where('is_active', true)
            ->selectRaw('SUM(stock * purchase_price) as valor')
            ->value('valor') ?? 0;
    }

    // ── Gráfico ventas por día ───────────────────

    public function getVentasPorDiaProperty(): array
    {
        $dias = (int) $this->periodoVentas;
        $data = Sale::where('status', 'closed')
            ->where('closed_at', '>=', now()->subDays($dias))
            ->selectRaw('DATE(closed_at) as fecha, COUNT(*) as total_ventas, SUM(total) as ingresos')
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        $labels = [];
        $ingresos = [];
        $cantidades = [];

        for ($i = $dias - 1; $i >= 0; $i--) {
            $fecha = now()->subDays($i)->format('Y-m-d');
            $fechaLabel = now()->subDays($i)->format('d/m');
            $registro = $data->firstWhere('fecha', $fecha);

            $labels[]    = $fechaLabel;
            $ingresos[]  = $registro ? (float) $registro->ingresos : 0;
            $cantidades[] = $registro ? (int) $registro->total_ventas : 0;
        }

        return compact('labels', 'ingresos', 'cantidades');
    }

    // ── Top productos más vendidos ───────────────

    public function getTopProductosProperty()
    {
        return SaleItem::query()
            ->join('menu_items', 'sale_items.menu_item_id', '=', 'menu_items.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.status', 'closed')
            ->where('sales.closed_at', '>=', now()->subDays(30))
            ->selectRaw('menu_items.name, SUM(sale_items.quantity) as total_vendido, SUM(sale_items.subtotal) as total_ingresos')
            ->groupBy('menu_items.id', 'menu_items.name')
            ->orderByDesc('total_vendido')
            ->limit(5)
            ->get();
    }
    // ── Actualizar gráfico al cambiar periodo ────

    public function updatedPeriodoVentas(): void
    {
        $this->dispatch('chart-updated', data: $this->getVentasPorDiaProperty());
    }

    // ── Margen ───────────────────────────────

    public function getMargenMesProperty(): float
    {
        return SaleItem::query()
            ->join('menu_items', 'sale_items.menu_item_id', '=', 'menu_items.id')
            ->join('products', 'menu_items.product_id', '=', 'products.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.status', 'closed')
            ->whereMonth('sales.closed_at', now()->month)
            ->whereYear('sales.closed_at', now()->year)
            ->selectRaw('SUM((sale_items.unit_price - products.purchase_price) * sale_items.quantity) as margen')
            ->value('margen') ?? 0;
    }

    public function getMargenPorcentajeProperty(): float
    {
        if ($this->ingresosMes <= 0) return 0;
        return round(($this->margenMes / $this->ingresosMes) * 100, 1);
    }

    public function getTopProductosPorMargenProperty()
    {
        return SaleItem::query()
            ->join('menu_items', 'sale_items.menu_item_id', '=', 'menu_items.id')
            ->join('products', 'menu_items.product_id', '=', 'products.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.status', 'closed')
            ->where('sales.closed_at', '>=', now()->subDays(30))
            ->selectRaw('
                menu_items.name,
                SUM(sale_items.quantity) as total_vendido,
                SUM(sale_items.subtotal) as total_ingresos,
                SUM((sale_items.unit_price - products.purchase_price) * sale_items.quantity) as margen_total,
                AVG(sale_items.unit_price - products.purchase_price) as margen_unitario
            ')
            ->groupBy('menu_items.id', 'menu_items.name')
            ->orderByDesc('margen_total')
            ->limit(5)
            ->get();
    }
    // ── Últimas ventas ───────────────────────────

    public function getUltimasVentasProperty()
    {
        return Sale::where('status', 'closed')
            ->orderByDesc('closed_at')
            ->limit(5)
            ->get();
    }

    public function render()
    {
       return view('livewire.admin-general.dashboard.dashboard');
    }
}
