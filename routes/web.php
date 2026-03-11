<?php

use App\Models\Inventario\StockMovement as StockMovementModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Rk\RoutingKit\Entities\RkRoute;

RkRoute::registerRoutes();

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/inventario/exportar-csv', function (Request $request) {
    $query = StockMovementModel::query()
        ->with(['product', 'supplier', 'user'])
        ->latest();

    // Aplicar filtros
    if ($request->filled('desde')) {
        $query->whereDate('created_at', '>=', $request->desde);
    }
    if ($request->filled('hasta')) {
        $query->whereDate('created_at', '<=', $request->hasta);
    }
    if ($request->filled('tipo')) {
        $query->where('type', $request->tipo);
    }
    if ($request->filled('producto')) {
        $query->where('product_id', $request->producto);
    }
    if ($request->filled('proveedor')) {
        $query->where('supplier_id', $request->proveedor);
    }
    if ($request->filled('bajo_min')) {
        $query->whereRaw('stock_after < (SELECT min_stock FROM products WHERE products.id = stock_movements.product_id AND products.min_stock IS NOT NULL)');
    }
    if ($request->filled('q')) {
        $s = $request->q;
        $query->where(function ($q) use ($s) {
            $q->whereHas('product', fn($pq) => $pq->where('name', 'like', "%{$s}%"))
              ->orWhere('reference', 'like', "%{$s}%")
              ->orWhere('reason', 'like', "%{$s}%");
        });
    }

    $movements = $query->get();
    $filename  = 'Inventario-' . now()->format('Y-m-d') . '.csv';

    return response()->streamDownload(function () use ($movements) {
        $handle = fopen('php://output', 'w');
        fputs($handle, "\xEF\xBB\xBF"); 

        fputcsv($handle, [
            'ID', 'Fecha', 'Producto', 'Tipo', 'Cantidad', 'Unidad',
            'Costo Unit. (L)', 'Total (L)', 'Stock Antes', 'Stock Después',
            'Referencia', 'Proveedor', 'Registrado por', 'Motivo',
        ]);

        foreach ($movements as $m) {
            fputcsv($handle, [
                $m->id,
                $m->created_at->format('d/m/Y H:i'),
                $m->product?->name,
                StockMovementModel::TYPES[$m->type] ?? $m->type,
                $m->quantity,
                $m->product?->unit,
                $m->unit_cost,
                $m->total_cost,
                $m->stock_before,
                $m->stock_after,
                $m->reference,
                $m->supplier?->name,
                $m->user?->name,
                $m->reason,
            ]);
        }

        fclose($handle);
    }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
})->middleware('auth')->name('inventario.export.csv');

Route::get('/ventas/historial/exportar-csv', function (Request $request) {
    $query = \App\Models\Venta\Sale::query()
        ->with(['items.menuItem', 'user'])
        ->whereIn('status', ['closed', 'cancelled'])
        ->latest('closed_at');

    if ($request->filled('desde')) {
        $query->whereDate('closed_at', '>=', $request->desde);
    }
    if ($request->filled('hasta')) {
        $query->whereDate('closed_at', '<=', $request->hasta);
    }
    if ($request->filled('metodo')) {
        $query->where('payment_method', $request->metodo);
    }
    if ($request->filled('estado')) {
        $query->where('status', $request->estado);
    }

    $sales    = $query->get();
    $filename = 'ventas-historial-' . now()->format('Y-m-d') . '.csv';

    return response()->streamDownload(function () use ($sales) {
        $handle = fopen('php://output', 'w');
        fputs($handle, "\xEF\xBB\xBF");

        fputcsv($handle, [
            'Folio', 'Fecha Cierre', 'Mesa', 'Subtotal (L)', 'Descuento (L)',
            'Total (L)', 'Método Pago', 'Estado', 'Registrado por', 'Notas',
        ]);

        foreach ($sales as $s) {
            fputcsv($handle, [
                $s->folio,
                $s->closed_at?->format('d/m/Y H:i'),
                $s->table_number,
                $s->subtotal,
                $s->discount_amount,
                $s->total,
                match($s->payment_method) {
                    'cash'     => 'Efectivo',
                    'card'     => 'Tarjeta',
                    'transfer' => 'Transferencia',
                    default    => $s->payment_method,
                },
                match($s->status) {
                    'closed'    => 'Cerrada',
                    'cancelled' => 'Cancelada',
                    default     => $s->status,
                },
                $s->user?->name,
                $s->notes,
            ]);
        }

        fclose($handle);
    }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
})->middleware('auth')->name('ventas.historial.export.csv');

require __DIR__.'/settings.php';

require __DIR__.'/settings.php';
