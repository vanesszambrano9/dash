<?php

namespace App\Livewire\Venta;

use App\Models\Venta\SaleItem as SaleItemModel;
use App\Models\Producto\MenuItem;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;

#[Layout('components.layouts.collapsable')]
class SaleItem extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithTable;
    use InteractsWithSchemas;

    #[Locked]
    public ?int $saleId = null;

    public function mount(?int $sale = null): void
    {
        $this->saleId = $sale 
            ?? request()->route('sale') 
            ?? request()->query('sale')
            ?? null;

        if (!$this->saleId) {
            session()->flash('error', 'No se especificó la venta para agregar ítems.');
        }
    }

    public function createOrEditForm(): array
    {
        return [
            TextInput::make('sale_id')
                ->default(fn() => $this->saleId)
                ->hidden()
                ->dehydrated(true),

            Select::make('menu_item_id')
                ->label('Platillo / Producto')
                ->required()
                ->options(fn() => MenuItem::available()
                    ->with('product.category') 
                    ->get()
                    ->mapWithKeys(fn($item) => [
                        $item->id => $item->name . ' (' . ($item->product?->category?->name ?? 'Sin categoría') . ') - L ' . $item->price
                    ])
                )
                ->searchable()
                ->preload()
                ->placeholder('Busca y selecciona un ítem del menú')
                ->live()
                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                    if ($state) {
                        $price = MenuItem::find($state)?->price ?? 0;
                        $set('unit_price', $price);
                        $quantity = $get('quantity') ?? 1;
                        $this->recalculateSubtotal($quantity, $price, $set);
                    }
                }),

            TextInput::make('quantity')
                ->label('Cantidad')
                ->required()
                ->numeric()
                ->minValue(1)
                ->default(1)
                ->step(1)
                ->live(onBlur: true)
                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                    $price = $get('unit_price') ?? 0;
                    $this->recalculateSubtotal($state, $price, $set);
                }),

            TextInput::make('unit_price')
                ->label('Precio Unitario')
                ->required()
                ->numeric()
                ->prefix('L')
                ->step(0.01)
                ->minValue(0)
                ->live(onBlur: true)
                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                    $quantity = $get('quantity') ?? 1;
                    $this->recalculateSubtotal($quantity, $state, $set);
                })
                ->helperText('Se llena automáticamente, pero puedes ajustar si hay promoción'),

            TextInput::make('subtotal')
                ->label('Subtotal')
                ->numeric()
                ->prefix('L')
                ->step(0.01)
                ->minValue(0)
                ->readOnly()
                ->dehydrated(false)
                ->helperText('Se calcula automáticamente: Cantidad × Precio'),

            Textarea::make('notes')
                ->label('Notas del Ítem')
                ->nullable()
                ->maxLength(255)
                ->rows(2)
                ->placeholder('Ej: Sin cebolla, extra limón, término medio...'),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn(): Builder => SaleItemModel::query()
                ->where('sale_id', $this->saleId)
                ->with('menuItem.product.category')  
            )
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('menuItem.name')
                    ->label('Producto')
                    ->searchable()
                    ->description(fn($record) => $record->menuItem?->product?->category?->name)
                    ->limit(30),

                TextColumn::make('quantity')
                    ->label('Cant.')
                    ->numeric()
                    ->badge()
                    ->color('primary')
                    ->sortable(),

                TextColumn::make('unit_price')
                    ->label('Precio Unit.')
                    ->formatStateUsing(fn($state) => 'L ' . number_format($state, 2, '.', ','))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('subtotal')
                    ->label('Subtotal')
                    ->formatStateUsing(fn($state) => 'L ' . number_format($state, 2, '.', ','))
                    ->sortable()
                    ->color('success')
                    ->weight('bold'),

                TextColumn::make('notes')
                    ->label('Notas')
                    ->limit(30)
                    ->tooltip(fn($record) => $record->notes)
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Agregado')
                    ->dateTime('H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->headerActions([
                CreateAction::make()
                    ->label('Agregar Ítem')
                    ->icon('heroicon-o-plus')
                    ->form($this->createOrEditForm())
                    ->action(function (array $data): void {
                        $data['sale_id'] = $this->saleId;
                        SaleItemModel::create($data);
                    })
                    ->successNotificationTitle('Ítem agregado a la venta')
                    ->after(function () {
                        $this->dispatch('sale-items-updated', saleId: $this->saleId);
                    }),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()
                        ->label('Editar')
                        ->icon('heroicon-o-pencil')
                        ->form($this->createOrEditForm())
                        ->successNotificationTitle('Ítem actualizado')
                        ->after(function ($record) {
                            $this->dispatch('sale-items-updated', saleId: $record->sale_id);
                        }),

                    DeleteAction::make()
                        ->label('Eliminar')
                        ->icon('heroicon-o-trash')
                        ->requiresConfirmation()
                        ->modalHeading('¿Eliminar ítem?')
                        ->modalDescription(fn($record) => "¿Quitar **{$record->menuItem?->name}** de esta venta?")
                        ->successNotificationTitle('Ítem eliminado')
                        ->after(function ($record) {
                            $this->dispatch('sale-items-updated', saleId: $record->sale_id);
                        }),
                ])->button()->label('Acciones'),
            ])
            ->emptyStateHeading('No hay ítems en esta venta')
            ->emptyStateDescription('Agrega platillos y productos para continuar con la venta.')
            ->emptyStateActions([
                CreateAction::make()
                    ->label('Agregar Primer Ítem')
                    ->form($this->createOrEditForm()),
            ])
            ->paginated([10, 25, 50, 'all'])
            ->defaultSort('created_at', 'asc');
    }

    public function render(): View
    {
        return view('livewire.Venta.venta-item');
    }

    private function recalculateSubtotal($quantity, $unitPrice, callable $set): void
    {
        $qty = floatval($quantity ?? 1);
        $price = floatval($unitPrice ?? 0);
        $subtotal = $qty * $price;
        $set('subtotal', number_format($subtotal, 2, '.', ''));
    }
}