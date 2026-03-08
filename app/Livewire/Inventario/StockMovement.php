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
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.collapsable')]
class StockMovement extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithTable;
    use InteractsWithSchemas;

    public function createOrEditForm(): array
    {
        return [
            Select::make('product_id')
                ->label('Producto')
                ->required()
                ->options(fn() => Product::active()->pluck('name', 'id'))
                ->searchable()
                ->preload()
                ->placeholder('Selecciona un producto')
                ->disabled(fn($record) => $record?->id !== null)
                ->helperText('Producto al que aplica este movimiento'),

            Select::make('type')
                ->label('Tipo de Movimiento')
                ->required()
                ->options([
                    'purchase'   => 'Compra (Entrada)',
                    'adjustment' => 'Ajuste Manual',
                    'waste'      => 'Merma/Pérdida',
                ])
                ->default('adjustment')
                ->disabled(fn($record) => $record?->id !== null)
                ->helperText('Compra = entrada | Ajuste = corrección | Merma = pérdida'),

            TextInput::make('quantity')
                ->label('Cantidad')
                ->required()
                ->numeric()
                ->step(0.001)
                ->minValue(0.001)
                ->placeholder('0.000')
                ->prefix(fn($get) => match($get('type')) {
                    'purchase' => '+',
                    'waste' => '-',
                    default => '±',
                })
                ->helperText(fn($get) => match($get('type')) {
                    'purchase' => 'Cantidad que ingresa al inventario',
                    'waste' => 'Cantidad que se pierde o daña',
                    'adjustment' => 'Usa positivo para agregar, negativo para quitar',
                    default => 'Cantidad del movimiento',
                }),

            TextInput::make('stock_before')
                ->label('Stock Antes')
                ->numeric()
                ->step(0.001)
                ->readOnly()
                ->dehydrated(false)
                ->helperText('Se calcula automáticamente'),

            TextInput::make('stock_after')
                ->label('Stock Después')
                ->numeric()
                ->step(0.001)
                ->readOnly()
                ->dehydrated(false)
                ->helperText('Se calcula automáticamente'),

            Select::make('supplier_id')
                ->label('Proveedor (solo para compras)')
                ->nullable()
                ->options(fn() => Supplier::active()->pluck('name', 'id'))
                ->searchable()
                ->preload()
                ->placeholder('Selecciona un proveedor')
                ->visible(fn($get) => $get('type') === 'purchase')
                ->helperText('Proveedor de esta compra'),

            Textarea::make('reason')
                ->label('Motivo / Observaciones')
                ->nullable()
                ->maxLength(500)
                ->rows(3)
                ->placeholder('Ej: Compra semanal, producto dañado, corrección de inventario...'),

            DatePicker::make('created_at')
                ->label('Fecha del Movimiento')
                ->nullable()
                ->default(now())
                ->maxDate(now())
                ->helperText('Fecha en que ocurrió el movimiento'),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn(): Builder => StockMovementModel::query()
                ->with(['product', 'supplier'])
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
                    ->formatStateUsing(fn($state) => match($state) {
                        'purchase' => 'Compra',
                        'adjustment' => 'Ajuste',
                        'waste' => 'Merma',
                        'sale' => 'Venta',
                        default => $state,
                    })
                    ->colors([
                        'success' => 'purchase',
                        'warning' => 'adjustment',
                        'danger' => 'waste',
                        'info' => 'sale',
                    ])
                    ->icons([
                        'heroicon-o-arrow-down-circle' => 'purchase',
                        'heroicon-o-wrench-screwdriver' => 'adjustment',
                        'heroicon-o-trash' => 'waste',
                        'heroicon-o-shopping-bag' => 'sale',
                    ]),

                TextColumn::make('quantity')
                    ->label('Cantidad')
                    ->formatStateUsing(fn($record) => 
                        ($record->quantity > 0 ? '+' : '') . number_format($record->quantity, 0)
                    )
                    ->color(fn($record) => $record->quantity > 0 ? 'success' : 'danger')
                    ->weight(fn($record) => $record->quantity > 0 ? 'bold' : 'normal')
                    ->sortable(),

                TextColumn::make('stock_snapshot')
                    ->label('Stock')
                    ->formatStateUsing(fn($record) => 
                        number_format($record->stock_before, 2) . ' → ' . 
                        number_format($record->stock_after, 2)
                    )
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('supplier.name')
                    ->label('Proveedor')
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
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                IconColumn::make('is_entry')
                    ->label('Entrada/Salida')
                    ->boolean()
                    ->trueIcon('heroicon-o-arrow-up-circle')
                    ->falseIcon('heroicon-o-arrow-down-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
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
                            ->when($data['created_from'] ?? null, fn($q, $d) => $q->whereDate('created_at', '>=', $d))
                            ->when($data['created_until'] ?? null, fn($q, $d) => $q->whereDate('created_at', '<=', $d));
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
            ], layout: FiltersLayout::AboveContent)  
            ->headerActions([
                CreateAction::make()
                    ->label('Registrar Movimiento Manual')
                    ->icon('heroicon-o-plus')
                    ->form($this->createOrEditForm())
                    ->successNotificationTitle('Movimiento registrado exitosamente')
                    ->action(function (array $data): void {
                        $product = Product::findOrFail($data['product_id']);
                        $quantity = floatval($data['quantity']);
                        
                        if ($data['type'] === 'adjustment' && str_starts_with($data['quantity'], '-')) {
                            $quantity = -abs($quantity);
                        } elseif ($data['type'] === 'waste') {
                            $quantity = -abs($quantity);
                        }
                        
                        $stockBefore = $product->stock;
                        $stockAfter = $stockBefore + $quantity;
                        
                        StockMovementModel::create([
                            'product_id'   => $data['product_id'],
                            'type'         => $data['type'],
                            'quantity'     => $quantity,
                            'stock_before' => $stockBefore,
                            'stock_after'  => $stockAfter,
                            'supplier_id'  => $data['supplier_id'] ?? null,
                            'reason'       => $data['reason'] ?? null,
                            'created_at'   => $data['created_at'] ?? now(),
                        ]);
                        
                        $product->update(['stock' => $stockAfter]);
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
                        ->visible(fn($record) => in_array($record->type, ['adjustment', 'waste']))
                        ->action(function (StockMovementModel $record, array $data): void {
                            if ($record->quantity != $data['quantity']) {
                                $product = $record->product;
                                $oldQty = $record->quantity;
                                $newQty = floatval($data['quantity']);
                                $newStock = $product->stock - $oldQty + $newQty;
                                
                                $record->update([
                                    'quantity'     => $newQty,
                                    'stock_before' => $product->stock - $oldQty,
                                    'stock_after'  => $newStock,
                                    'reason'       => $data['reason'] ?? $record->reason,
                                ]);
                                
                                $product->update(['stock' => $newStock]);
                            } else {
                                $record->update(['reason' => $data['reason'] ?? $record->reason]);
                            }
                        })
                        ->successNotificationTitle('Movimiento actualizado'),

                    DeleteAction::make()
                        ->label('Eliminar')
                        ->icon('heroicon-o-trash')
                        ->visible(fn($record) => 
                            in_array($record->type, ['adjustment', 'waste']) && 
                            $record->created_at->gt(now()->subHours(24))
                        )
                        ->requiresConfirmation()
                        ->modalHeading('¿Eliminar movimiento?')
                        ->modalDescription(fn($record) => 
                            "¿Estás seguro de eliminar este registro de **{$record->product?->name}**?"
                        )
                        ->action(function (StockMovementModel $record): void {
                            $product = $record->product;
                            $newStock = $product->stock - $record->quantity;
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
            ->paginated([25, 50, 100, 'all']);
    }

    public function render(): View
    {
        return view('livewire.Inventario.inventario');
    }
}