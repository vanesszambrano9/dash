<?php

namespace App\Filament\Exports;

use App\Models\Inventario\StockMovement;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class StockMovementExporter extends Exporter
{
    protected static ?string $model = StockMovement::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('ID'),
            ExportColumn::make('created_at')->label('Fecha')->formatStateUsing(fn($state) => $state?->format('d/m/Y H:i')),
            ExportColumn::make('product.name')->label('Producto'),
            ExportColumn::make('type')
                ->label('Tipo')
                ->formatStateUsing(fn($state) => StockMovement::TYPES[$state] ?? $state),
            ExportColumn::make('quantity')->label('Cantidad'),
            ExportColumn::make('unit_cost')->label('Costo Unitario'),
            ExportColumn::make('stock_before')->label('Stock Antes'),
            ExportColumn::make('stock_after')->label('Stock Después'),
            ExportColumn::make('reference')->label('Referencia'),
            ExportColumn::make('supplier.name')->label('Proveedor'),
            ExportColumn::make('user.name')->label('Registrado por'),
            ExportColumn::make('reason')->label('Motivo'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $count = $export->successful_rows;
        return "Se exportaron {$count} movimientos exitosamente.";
    }
}
