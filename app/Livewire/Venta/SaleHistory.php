<?php

namespace App\Livewire\Venta;

use App\Models\Venta\Sale as SaleModel;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Actions\Action;  
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.collapsable')]
class SaleHistory extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithTable;
    use InteractsWithSchemas;

    public function table(Table $table): Table
    {
        return $table
            ->query(fn(): Builder => SaleModel::query()
                ->whereIn('status', ['closed', 'cancelled'])
                ->withCount('items')
                ->latest('closed_at')
            )
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('folio')
                    ->label('Folio')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->badge()
                    ->color('primary'),

                TextColumn::make('table_number')
                    ->label('Mesa')
                    ->searchable()
                    ->sortable()
                    ->default('—')
                    ->badge()
                    ->color('gray')
                    ->toggleable(),

                TextColumn::make('items_count')
                    ->label('Ítems')
                    ->counts('items')
                    ->badge()
                    ->color('info'),

                TextColumn::make('subtotal')
                    ->label('Subtotal')
                    ->formatStateUsing(fn($state) => 'L ' . number_format($state, 2, '.', ','))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('discount')
                    ->label('Descuento')
                    ->formatStateUsing(fn($state) => $state > 0 ? 'L ' . number_format($state, 2, '.', ',') : '—')
                    ->color(fn($state) => $state > 0 ? 'warning' : 'gray')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('total')
                    ->label('Total')
                    ->formatStateUsing(fn($state) => 'L ' . number_format($state, 2, '.', ','))
                    ->sortable()
                    ->color('success')
                    ->weight('bold'),

                TextColumn::make('payment_method')
                    ->label('Pago')
                    ->badge()
                    ->formatStateUsing(fn($state) => match($state) {
                        'cash' => 'Efectivo',
                        'card' => 'Tarjeta',
                        'transfer' => 'Transferencia',
                        default => $state,
                    })
                    ->color(fn($state) => match($state) {
                        'cash'     => 'success',
                        'card'     => 'info',
                        'transfer' => 'warning',
                        default    => 'gray',
                    }),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn($state) => match($state) {
                        'closed'    => 'Cerrada',
                        'cancelled' => 'Cancelada',
                        default     => $state,
                    })
                    ->color(fn($state) => match($state) {
                        'closed'    => 'success',
                        'cancelled' => 'danger',
                        default     => 'gray',
                    }),

                TextColumn::make('closed_at')
                    ->label('Cerrada El')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Creada El')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
           ->filters([
                Filter::make('date_range')
                    ->label('Rango de Fechas')
                    ->form([
                        DatePicker::make('created_from')
                            ->label('Desde')
                            ->columnSpan(1),
                        DatePicker::make('created_until')
                            ->label('Hasta')
                            ->columnSpan(1),
                    ])
                    ->columns(2) 
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['created_from'] ?? null, fn($q, $d) => $q->whereDate('closed_at', '>=', $d))
                            ->when($data['created_until'] ?? null, fn($q, $d) => $q->whereDate('closed_at', '<=', $d));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['created_from'] ?? null) {
                            $indicators['from'] = 'Desde: ' . \Illuminate\Support\Carbon::parse($data['created_from'])->format('d/m/Y');
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators['until'] = 'Hasta: ' . \Illuminate\Support\Carbon::parse($data['created_until'])->format('d/m/Y');
                        }
                        return $indicators;
                    }),

                SelectFilter::make('payment_method')
                    ->label('Método de Pago')
                    ->options([
                        'cash'     => 'Efectivo',
                        'card'     => 'Tarjeta',
                        'transfer' => 'Transferencia',
                    ])
                    ->placeholder('Todos'),

                SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'closed'    => 'Cerrada',
                        'cancelled' => 'Cancelada',
                    ])
                    ->placeholder('Todos'),
            ], layout: FiltersLayout::AboveContent) 
            ->headerActions([
                Action::make('export_csv')
                    ->label('Exportar CSV')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->url(function () {
                        $f = $this->tableFilters ?? [];
                        return route('ventas.historial.export.csv', array_filter([
                            'desde'  => $f['date_range']['created_from'] ?? null,
                            'hasta'  => $f['date_range']['created_until'] ?? null,
                            'metodo' => $f['payment_method']['value'] ?? null,
                            'estado' => $f['status']['value'] ?? null,
                        ]));
                    })
                    ->openUrlInNewTab(false),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->label('Ver Detalles')
                        ->icon('heroicon-o-eye')
                        ->modalHeading('Detalles de Venta')
                        ->modalContent(function (SaleModel $record): View {
                            return view('livewire.Venta.sale-history-view', ['sale' => $record]);
                        }),

                    Action::make('reopen')
                        ->label('Reabrir')
                        ->icon('heroicon-o-arrow-path')
                        ->color('warning')
                        ->visible(fn($record) => $record->status === 'closed')
                        ->requiresConfirmation()
                        ->modalHeading('¿Reabrir venta?')
                        ->modalDescription('La venta volverá a estado "Abierta" y podrá modificarse.')
                        ->action(function (SaleModel $record): void {
                            $record->update(['status' => 'open', 'closed_at' => null]);
                        })
                        ->successNotificationTitle('Venta reabierta'),

                    DeleteAction::make()
                        ->label('Eliminar')
                        ->icon('heroicon-o-trash')
                        ->requiresConfirmation()
                        ->modalHeading('¿Eliminar venta del historial?')
                        ->modalDescription('Esta acción no se puede deshacer.')
                        ->successNotificationTitle('Venta eliminada'),
                ])->button()->label('Acciones'),
            ])
            ->bulkActions([
                BulkActionGroup::make([]),
            ])
            ->emptyStateHeading('No hay ventas en el historial')
            ->emptyStateDescription('Las ventas cerradas o canceladas aparecerán aquí.')
            ->defaultSort('closed_at', 'desc')
            ->paginated([25, 50, 100, 'all']);
    }

    public function render(): View
    {
        return view('livewire.Venta.sale-history');
    }
}