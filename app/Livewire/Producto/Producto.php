<?php

namespace App\Livewire\Producto;

use App\Models\Producto\Product;
use App\Models\Categoria\Category;
use App\Models\Proveedor\Supplier;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.collapsable')]
class Producto extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithTable;
    use InteractsWithSchemas;

    public function createOrEditForm(): array
    {
        return [
            Select::make('category_id')
                ->label('Categoría')
                ->required()
                ->options(fn() => Category::active()->pluck('name', 'id'))
                ->searchable()
                ->preload()
                ->placeholder('Selecciona una categoría'),

            Select::make('supplier_id')
                ->label('Proveedor')
                ->nullable()
                ->options(fn() => Supplier::active()->pluck('name', 'id'))
                ->searchable()
                ->preload()
                ->placeholder('Selecciona un proveedor (opcional)'),

            TextInput::make('name')
                ->label('Nombre del Producto')
                ->required()
                ->maxLength(255)
                ->placeholder('Ej: Camarón fresco, Cerveza Corona 355ml'),

            TextInput::make('sku')
                ->label('SKU / Código Interno')
                ->nullable()
                ->unique(ignoreRecord: true)
                ->maxLength(100)
                ->placeholder('Ej: CAM-FRE-001'),

            Select::make('unit')
                ->label('Unidad de Medida')
                ->required()
                ->options([
                    'pieza'   => 'Pieza',
                    'litro'   => 'Litro',
                    'libra'   => 'Libra',
                    'ml'      => 'Mililitro',
                    'kg'      => 'Kilogramo',
                    'g'       => 'Gramo',
                    'caja'    => 'Caja',
                    'bolsa'   => 'Bolsa',
                    'botella' => 'Botella',
                    'lata'    => 'Lata',
                    'paquete' => 'Paquete',
                    'otros'   => 'Otros',
                ])
                ->default('pieza')
                ->native(false),

            TextInput::make('purchase_price')
                ->label('Precio de Compra (Costo)')
                ->required()
                ->numeric()
                ->prefix('L')
                ->step(0.01)
                ->minValue(0)
                ->placeholder('0.00')
                ->helperText('Lo que pagas al proveedor por este producto'),

            TextInput::make('stock')
                ->label('Stock Actual')
                ->required()
                ->numeric()
                ->minValue(0)
                ->default(0)
                ->step(0.01)
                ->disabled(fn($record) => $record?->id === null) 
                ->helperText('Se gestiona mediante movimientos de inventario'),

            TextInput::make('min_stock')
                ->label('Stock Mínimo (Alerta)')
                ->required()
                ->numeric()
                ->minValue(0)
                ->default(5)
                ->step(0.01)
                ->helperText('Alerta cuando stock ≤ este valor'),

            Textarea::make('description')
                ->label('Descripción')
                ->nullable()
                ->maxLength(1000)
                ->rows(3)
                ->placeholder('Detalles internos del producto...'),

            Toggle::make('is_active')
                ->label('Producto Activo')
                ->default(true)
                ->helperText('Desactiva para ocultar en menús sin eliminar historial'),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn(): Builder => Product::query()->with(['category', 'supplier']))
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('name')
                    ->label('Producto')
                    ->searchable()
                    ->sortable()
                    ->description(fn($record) => $record->sku ? "SKU: {$record->sku}" : null),

                TextColumn::make('category.name')
                    ->label('Categoría')
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('supplier.name')
                    ->label('Proveedor')
                    ->searchable()
                    ->sortable()
                    ->default('—')
                    ->toggleable(),

                TextColumn::make('unit')
                    ->label('Unidad')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn($state) => ucfirst($state))
                    ->toggleable(),

                TextColumn::make('purchase_price')
                    ->label('Costo Unit.')
                    ->formatStateUsing(fn($state) => 'L ' . number_format($state, 2, '.', ','))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('stock')
                    ->label('Stock')
                    ->numeric()
                    ->sortable()
                    ->color(fn($record) => $record->is_low_stock ? 'danger' : 'success')
                    ->weight(fn($record) => $record->is_low_stock ? 'bold' : 'normal'),

                TextColumn::make('min_stock')
                    ->label('Mínimo')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_low_stock')
                    ->label('Alerta Stock')
                    ->boolean()
                    ->trueIcon('heroicon-o-exclamation-triangle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('warning')
                    ->falseColor('success')
                    ->tooltip(fn($record) => $record->is_low_stock ? 'Stock bajo' : 'Stock adecuado'),

                IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean()
                    ->sortable()
                    ->trueColor('success')
                    ->falseColor('danger'),

                TextColumn::make('created_at')
                    ->label('Registrado')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([], layout: FiltersLayout::AboveContent)
            ->headerActions([
                CreateAction::make()
                    ->label('Nuevo Producto')
                    ->form($this->createOrEditForm())
                    ->successNotificationTitle('Producto creado exitosamente'),
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
                        ->successNotificationTitle('Producto actualizado'),

                    DeleteAction::make()
                        ->label('Eliminar')
                        ->icon('heroicon-o-trash')
                        ->requiresConfirmation()
                        ->modalHeading('¿Eliminar producto?')
                        ->modalDescription(fn($record) => "Estás a punto de eliminar **{$record->name}**. Esta acción no se puede deshacer.")
                        ->successNotificationTitle('Producto eliminado'),
                ])->button()->label('Acciones'),
            ])
            ->bulkActions([
                BulkActionGroup::make([]),
            ])
            ->emptyStateHeading('No hay productos registrados')
            ->emptyStateDescription('Comienza agregando tu primer producto al inventario.')
            ->emptyStateActions([
                CreateAction::make()
                    ->label('Crear Producto')
                    ->form($this->createOrEditForm()),
            ])
            ->defaultSort('name', 'asc');
    }

    public function render(): View
    {
        return view('livewire.Producto.producto');
    }
}