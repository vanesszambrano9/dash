<?php

namespace App\Livewire\Inventario;

use App\Models\Inventario\StockMovement as StockMovementModel;
use App\Models\Producto\Product;
use App\Models\Proveedor\Supplier;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Actions\Action as TableAction;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.collapsable')]
class StockMovement extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithTable;
    use InteractsWithSchemas;

    // ── Propiedades computadas para los widgets ──────────────────────

    #[Computed]
    public function totalEntradas(): float
    {
        return (float) StockMovementModel::query()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('quantity', '>', 0)
            ->sum('quantity');
    }

    #[Computed]
    public function totalSalidas(): float
    {
        return (float) abs(StockMovementModel::query()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('quantity', '<', 0)
            ->sum('quantity'));
    }

    #[Computed]
    public function valorInventario(): float
    {
        return (float) Product::active()
            ->selectRaw('SUM(stock * purchase_price) as total')
            ->value('total') ?? 0;
    }

    #[Computed]
    public function productosBajoMinimo(): int
    {
        return Product::active()
            ->whereNotNull('min_stock')
            ->whereColumn('stock', '<', 'min_stock')
            ->count();
    }

    #[Computed]
    public function mermasMes(): float
    {
        return (float) abs(StockMovementModel::query()
            ->where('type', 'waste')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('quantity'));
    }


    public function createOrEditForm(): array
    {
        return [
            Grid::make(2)->schema([
                Select::make('product_id')
                    ->label('Producto')
                    ->required()
                    ->options(fn() => Product::active()->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->placeholder('Selecciona un producto')
                    ->disabled(fn($record) => $record?->id !== null)
                    ->helperText('Producto al que aplica este movimiento')
                    ->live()
                    ->afterStateUpdated(fn($set, $state) => $set(
                        '_stock_actual',
                        $state ? (Product::find($state)?->stock ?? 0) : null
                    ))
                    ->columnSpan(1),

                Select::make('type')
                    ->label('Tipo de Movimiento')
                    ->required()
                    ->options(StockMovementModel::TYPES)
                    ->default('adjustment')
                    ->disabled(fn($record) => $record?->id !== null)
                    ->helperText('Define si es entrada, salida o corrección')
                    ->columnSpan(1),
            ]),

            Placeholder::make('_stock_actual')
                ->label('Stock actual del producto')
                ->content(fn($get) => $get('product_id')
                    ? 'Stock actual: ' . number_format(Product::find($get('product_id'))?->stock ?? 0, 2)
                      . ' ' . (Product::find($get('product_id'))?->unit ?? '')
                    : '— Selecciona un producto para ver el stock actual —'
                )
                ->visible(fn($get) => (bool) $get('product_id')),

            Grid::make(3)->schema([
                TextInput::make('quantity')
                    ->label('Cantidad')
                    ->required()
                    ->numeric()
                    ->step(0.001)
                    ->minValue(fn($get) => in_array($get('type'), StockMovementModel::EXIT_TYPES) ? null : 0.001)
                    ->placeholder('0.000')
                    ->prefix(fn($get) => in_array($get('type'), StockMovementModel::ENTRY_TYPES) ? '+' : (in_array($get('type'), StockMovementModel::EXIT_TYPES) ? '-' : '±'))
                    ->helperText(fn($get) => match(true) {
                        in_array($get('type'), StockMovementModel::ENTRY_TYPES) => 'Cantidad que ingresa al inventario',
                        in_array($get('type'), StockMovementModel::EXIT_TYPES)  => 'Cantidad que sale del inventario',
                        default => 'Usa positivo para agregar, negativo para quitar',
                    })
                    ->columnSpan(1),

                TextInput::make('unit_cost')
                    ->label('Costo Unitario')
                    ->numeric()
                    ->step(0.01)
                    ->minValue(0)
                    ->prefix('$')
                    ->placeholder('0.00')
                    ->helperText('Costo por unidad al momento del movimiento')
                    ->columnSpan(1),

                TextInput::make('reference')
                    ->label('Nº Referencia / Documento')
                    ->maxLength(100)
                    ->placeholder('Ej: FAC-001, OC-2026-05...')
                    ->helperText('Número de factura, orden de compra, etc.')
                    ->columnSpan(1),
            ]),

            Grid::make(2)->schema([
                TextInput::make('stock_before')
                    ->label('Stock Antes')
                    ->numeric()
                    ->step(0.001)
                    ->readOnly()
                    ->dehydrated(false)
                    ->helperText('Se calcula automáticamente')
                    ->columnSpan(1),

                TextInput::make('stock_after')
                    ->label('Stock Después')
                    ->numeric()
                    ->step(0.001)
                    ->readOnly()
                    ->dehydrated(false)
                    ->helperText('Se calcula automáticamente')
                    ->columnSpan(1),
            ]),

            Select::make('supplier_id')
                ->label('Proveedor')
                ->nullable()
                ->options(fn() => Supplier::active()->pluck('name', 'id'))
                ->searchable()
                ->preload()
                ->placeholder('Selecciona un proveedor')
                ->visible(fn($get) => in_array($get('type'), ['purchase', 'return_supplier']))
                ->helperText('Proveedor relacionado a este movimiento'),

            Grid::make(2)->schema([
                Textarea::make('reason')
                    ->label('Motivo / Observaciones')
                    ->nullable()
                    ->maxLength(500)
                    ->rows(2)
                    ->placeholder('Ej: Compra semanal, producto dañado...')
                    ->columnSpan(1),

                DatePicker::make('created_at')
                    ->label('Fecha del Movimiento')
                    ->nullable()
                    ->default(now())
                    ->maxDate(now())
                    ->helperText('Fecha en que ocurrió el movimiento')
                    ->columnSpan(1),
            ]),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn(): Builder => StockMovementModel::query()
                ->with(['product', 'supplier', 'user'])
                ->latest()
            )
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('product.name')
                    ->label('Producto')
                    ->searchable()
                    ->sortable()
                    ->limit(25)
                    ->tooltip(fn($record) => $record->product?->name),

                BadgeColumn::make('type')
                    ->label('Tipo')
                    ->formatStateUsing(fn($state) => StockMovementModel::TYPES[$state] ?? $state)
                    ->colors([
                        'success' => fn($state) => in_array($state, ['purchase', 'return', 'initial']),
                        'warning' => fn($state) => in_array($state, ['adjustment', 'transfer']),
                        'danger'  => fn($state) => in_array($state, ['waste', 'return_supplier']),
                        'info'    => 'sale',
                    ])
                    ->icons([
                        'heroicon-o-arrow-down-circle'  => fn($state) => in_array($state, ['purchase', 'return', 'initial']),
                        'heroicon-o-arrow-up-circle'    => fn($state) => in_array($state, ['sale', 'waste', 'return_supplier']),
                        'heroicon-o-wrench-screwdriver' => 'adjustment',
                        'heroicon-o-arrows-right-left'  => 'transfer',
                    ]),

                TextColumn::make('quantity')
                    ->label('Cantidad')
                    ->formatStateUsing(fn($record) =>
                        ($record->quantity > 0 ? '+' : '') . number_format($record->quantity, 2)
                        . ($record->product?->unit ? ' ' . $record->product->unit : '')
                    )
                    ->color(fn($record) => $record->quantity > 0 ? 'success' : 'danger')
                    ->weight('bold')
                    ->sortable(),

                TextColumn::make('unit_cost')
                    ->label('Costo Unit.')
                    ->money('HNL')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('total_cost')
                    ->label('Total')
                    ->getStateUsing(fn($record) => $record->total_cost)
                    ->formatStateUsing(fn($state) => $state !== null ? 'L ' . number_format($state, 2) : '—')
                    ->toggleable(),

                TextColumn::make('stock_snapshot')
                    ->label('Stock')
                    ->formatStateUsing(fn($record) =>
                        number_format($record->stock_before, 2) . ' → ' .
                        number_format($record->stock_after, 2)
                    )
                    ->color(fn($record) => $record->below_min_stock ? 'danger' : null)
                    ->tooltip(fn($record) => $record->below_min_stock ? '⚠ Stock por debajo del mínimo' : null)
                    ->toggleable(),

                TextColumn::make('reference')
                    ->label('Referencia')
                    ->default('—')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('supplier.name')
                    ->label('Proveedor')
                    ->default('—')
                    ->toggleable()
                    ->limit(20),

                TextColumn::make('user.name')
                    ->label('Registrado por')
                    ->default('—')
                    ->toggleable()
                    ->limit(20),

                TextColumn::make('reason')
                    ->label('Motivo')
                    ->limit(30)
                    ->tooltip(fn($record) => $record->reason)
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                IconColumn::make('is_entry')
                    ->label('E/S')
                    ->boolean()
                    ->trueIcon('heroicon-o-arrow-down-circle')
                    ->falseIcon('heroicon-o-arrow-up-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('date_range')
                    ->label('Rango de Fechas')
                    ->form([
                        DatePicker::make('created_from')->label('Desde')->columnSpan(1),
                        DatePicker::make('created_until')->label('Hasta')->columnSpan(1),
                    ])
                    ->columns(2)
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['created_from'] ?? null, fn($q, $d) => $q->whereDate('created_at', '>=', $d))
                            ->when($data['created_until'] ?? null, fn($q, $d) => $q->whereDate('created_at', '<=', $d));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['created_from'] ?? null) {
                            $indicators['from'] = 'Desde: ' . \Carbon\Carbon::parse($data['created_from'])->format('d/m/Y');
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators['until'] = 'Hasta: ' . \Carbon\Carbon::parse($data['created_until'])->format('d/m/Y');
                        }
                        return $indicators;
                    }),

                SelectFilter::make('type')
                    ->label('Tipo de Movimiento')
                    ->options(StockMovementModel::TYPES)
                    ->placeholder('Todos los tipos'),

                SelectFilter::make('product_id')
                    ->label('Producto')
                    ->options(fn() => Product::active()->pluck('name', 'id'))
                    ->searchable()
                    ->placeholder('Todos los productos'),

                SelectFilter::make('supplier_id')
                    ->label('Proveedor')
                    ->options(fn() => Supplier::active()->pluck('name', 'id'))
                    ->searchable()
                    ->placeholder('Todos los proveedores'),

                Filter::make('below_min_stock')
                    ->label('⚠ Stock bajo mínimo')
                    ->query(fn(Builder $q) => $q->whereRaw(
                        'stock_after < (SELECT min_stock FROM products WHERE products.id = stock_movements.product_id AND products.min_stock IS NOT NULL)'
                    )),
            ], layout: FiltersLayout::AboveContent)
            ->headerActions([
                TableAction::make('export')
                    ->label('Exportar CSV')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->url(function () {
                        $f = $this->tableFilters ?? [];
                        return route('inventario.export.csv', array_filter([
                            'desde'    => $f['date_range']['created_from'] ?? null,
                            'hasta'    => $f['date_range']['created_until'] ?? null,
                            'tipo'     => $f['type']['value'] ?? null,
                            'producto' => $f['product_id']['value'] ?? null,
                            'proveedor'=> $f['supplier_id']['value'] ?? null,
                            'bajo_min' => !empty($f['below_min_stock']['isActive']) ? 1 : null,
                            'q'        => $this->tableSearch ?? null,
                        ]));
                    })
                    ->openUrlInNewTab(false),

                CreateAction::make()
                    ->label('Registrar Movimiento')
                    ->icon('heroicon-o-plus')
                    ->form($this->createOrEditForm())
                    ->successNotificationTitle('Movimiento registrado exitosamente')
                    ->action(function (array $data): void {
                        $product  = Product::findOrFail($data['product_id']);
                        $quantity = floatval($data['quantity']);

                        // Para tipos de salida, forzar negativo
                        if (in_array($data['type'], StockMovementModel::EXIT_TYPES)) {
                            $quantity = -abs($quantity);
                        }
                        // Para ajuste, respetar el signo del usuario
                        if ($data['type'] === 'adjustment') {
                            $quantity = $quantity; // ya viene con signo del input
                        }

                        $stockBefore = (float) $product->stock;
                        $stockAfter  = $stockBefore + $quantity;

                        StockMovementModel::create([
                            'product_id'   => $data['product_id'],
                            'type'         => $data['type'],
                            'quantity'     => $quantity,
                            'unit_cost'    => $data['unit_cost'] ?? null,
                            'stock_before' => $stockBefore,
                            'stock_after'  => $stockAfter,
                            'supplier_id'  => $data['supplier_id'] ?? null,
                            'reason'       => $data['reason'] ?? null,
                            'reference'    => $data['reference'] ?? null,
                            'created_at'   => $data['created_at'] ?? now(),
                        ]);

                        $product->update(['stock' => $stockAfter]);

                        // Notificación si el stock queda bajo el mínimo
                        if ($product->min_stock !== null && $stockAfter < (float) $product->min_stock) {
                            Notification::make()
                                ->warning()
                                ->title('⚠ Stock bajo mínimo')
                                ->body("**{$product->name}** quedó en {$stockAfter} {$product->unit} (mínimo: {$product->min_stock}).")
                                ->persistent()
                                ->send();
                        }
                    }),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->label('Ver Detalles')
                        ->icon('heroicon-o-eye')
                        ->form($this->createOrEditForm())
                        ->disabledForm(),

                    EditAction::make()
                        ->label('Editar')
                        ->icon('heroicon-o-pencil')
                        ->form($this->createOrEditForm())
                        ->visible(fn($record) => in_array($record->type, ['adjustment', 'waste', 'initial']))
                        ->action(function (StockMovementModel $record, array $data): void {
                            $product    = $record->product;
                            $oldQty     = (float) $record->quantity;
                            $newQty     = floatval($data['quantity']);

                            if (in_array($record->type, StockMovementModel::EXIT_TYPES)) {
                                $newQty = -abs($newQty);
                            }

                            $newStock = (float) $product->stock - $oldQty + $newQty;

                            $record->update([
                                'quantity'     => $newQty,
                                'unit_cost'    => $data['unit_cost'] ?? $record->unit_cost,
                                'stock_before' => (float) $product->stock - $oldQty,
                                'stock_after'  => $newStock,
                                'reason'       => $data['reason'] ?? $record->reason,
                                'reference'    => $data['reference'] ?? $record->reference,
                            ]);

                            $product->update(['stock' => $newStock]);
                        })
                        ->successNotificationTitle('Movimiento actualizado'),

                    DeleteAction::make()
                        ->label('Eliminar')
                        ->icon('heroicon-o-trash')
                        ->visible(fn($record) =>
                            in_array($record->type, ['adjustment', 'waste', 'initial']) &&
                            $record->created_at->gt(now()->subHours(24))
                        )
                        ->requiresConfirmation()
                        ->modalHeading('¿Eliminar movimiento?')
                        ->modalDescription(fn($record) =>
                            "¿Estás seguro de eliminar este registro de **{$record->product?->name}**? El stock se revertirá."
                        )
                        ->action(function (StockMovementModel $record): void {
                            $product  = $record->product;
                            $newStock = (float) $product->stock - (float) $record->quantity;
                            $product->update(['stock' => $newStock]);
                            $record->delete();
                        })
                        ->successNotificationTitle('Movimiento eliminado'),
                ])->button()->label('Acciones'),
            ])
            ->bulkActions([
                BulkActionGroup::make([]),
            ])
            ->emptyStateHeading('No hay movimientos de stock')
            ->emptyStateDescription('Los movimientos se generan automáticamente al comprar o vender.')
            ->emptyStateActions([
                CreateAction::make()
                    ->label('Registrar Movimiento Manual')
                    ->form($this->createOrEditForm()),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([25, 50, 100, 'all'])
            ->searchable();
    }

    public function render(): View
    {
        return view('livewire.Inventario.inventario', [
            'totalEntradas'       => $this->totalEntradas,
            'totalSalidas'        => $this->totalSalidas,
            'valorInventario'     => $this->valorInventario,
            'productosBajoMinimo' => $this->productosBajoMinimo,
            'mermasMes'           => $this->mermasMes,
        ]);
    }
}