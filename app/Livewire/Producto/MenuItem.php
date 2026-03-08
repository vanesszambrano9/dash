<?php

namespace App\Livewire\Producto;

use App\Models\Producto\MenuItem as MenuItemModel;
use App\Models\Producto\Product;
use Illuminate\Support\Str; 
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
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
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
class MenuItem extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithTable;
    use InteractsWithSchemas;

    public function createOrEditForm(): array
    {
        return [
            Select::make('product_id')
                ->label('Vincular con Inventario (Opcional)')
                ->nullable()
                ->options(fn() => Product::active()
                    ->with('category')
                    ->get()
                    ->mapWithKeys(fn($p) => [
                        $p->id => "{$p->name} ({$p->category?->name}) - Stock: {$p->stock} {$p->unit}"
                    ])
                )
                ->searchable()
                ->preload()
                ->placeholder('Selecciona solo si es producto directo (ej: cerveza, camarón)')
                ->live()
                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                    if ($state) {
                        $product = Product::find($state);
                        if ($product) {
                            $currentName = $get('name');
                            $currentPrice = $get('price');
                            
                            if (empty($currentName)) {
                                $set('name', $product->name);
                            }
                            if (empty($currentPrice) || $currentPrice == 0) {
                                $set('price', round($product->purchase_price * 1.30, 2));
                            }
                        }
                    }
                })
                ->helperText('Déjalo vacío para platillos con múltiples ingredientes (sopas, ceviches, combos)'),

            TextInput::make('name')
                ->label('Nombre para el Menú')
                ->required()
                ->maxLength(255)
                ->placeholder('Ej: Sopa de Camarón, Caguama XX, Ceviche Mixto')
                ->helperText('Nombre que verá el cliente'),

            Textarea::make('description')
                ->label('Descripción para el Cliente')
                ->nullable()
                ->maxLength(1000)
                ->rows(3)
                ->placeholder('Ingredientes, preparación o detalles atractivos...'),

            TextInput::make('price')
                ->label('Precio de Venta en Menú')
                ->required()
                ->numeric()
                ->prefix('L')
                ->step(0.01)
                ->minValue(0)
                ->placeholder('0.00')
                ->helperText('Precio que pagará el cliente'),

            FileUpload::make('image')
                ->label('Imagen para el Menú')
                ->nullable()
                ->image()
                ->directory('menu-items')
                ->visibility('public')
                ->maxSize(2048)
                ->imageResizeMode('cover')
                ->imageCropAspectRatio('1:1')
                ->helperText('Foto atractiva para menú digital'),

            Toggle::make('is_available')
                ->label('Disponible en Menú')
                ->default(true)
                ->helperText('Desactiva temporalmente sin eliminar (ej: sin ingredientes)'),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn(): Builder => MenuItemModel::query()->with('product.category'))
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                ImageColumn::make('image')
                    ->label('Foto')
                    ->square()
                    ->size(40)
                    ->toggleable(),

                TextColumn::make('name')
                    ->label('Nombre en Menú')
                    ->searchable()
                    ->sortable()
                    ->description(fn($record) => Str::limit($record->description, 40)),

                TextColumn::make('category_display')
                    ->label('Categoría')
                    ->badge()
                    ->color('primary')
                    ->formatStateUsing(fn($record) => 
                        $record->product?->category?->name ?? 'Manual'
                    )
                    ->searchable()
                    ->sortable(),

                TextColumn::make('inventory_status')
                    ->label('Inventario')
                    ->formatStateUsing(fn($record) => 
                        $record->product_id 
                            ? "Stock: {$record->product?->stock} {$record->product?->unit}" 
                            : 'Manual'
                    )
                    ->color(fn($record) => 
                        $record->product_id 
                            ? ($record->product?->stock <= $record->product?->min_stock ? 'danger' : 'success')
                            : 'gray'
                    )
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('price')
                    ->label('Precio')
                    ->formatStateUsing(fn($state) => 'L ' . number_format($state, 2, '.', ','))
                    ->sortable()
                    ->color('success')
                    ->weight('bold'),

                TextColumn::make('estimated_margin')
                    ->label('Margen Est.')
                    ->formatStateUsing(fn($record) => 
                        $record->product_id && $record->product 
                            ? 'L ' . number_format($record->price - $record->product->purchase_price, 2)
                            : '—'
                    )
                    ->color(fn($record) => 
                        $record->product_id && $record->product && ($record->price - $record->product->purchase_price) >= 0
                            ? 'success'
                            : 'gray'
                    )
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('total_sold')
                    ->label('Vendidos')
                    ->counts('saleItems')
                    ->badge()
                    ->color('info')
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_available')
                    ->label('Disponible')
                    ->boolean()
                    ->sortable()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-pause-circle')
                    ->trueColor('success')
                    ->falseColor('warning')
                    ->tooltip(fn($record) => $record->is_available ? 'En menú' : 'No disponible'),

                TextColumn::make('created_at')
                    ->label('Agregado')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
               
            ], layout: FiltersLayout::AboveContent)
            ->headerActions([
                CreateAction::make()
                    ->label('Nuevo Ítem de Menú')
                    ->form($this->createOrEditForm())
                    ->successNotificationTitle('Ítem agregado al menú exitosamente'),
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
                        ->successNotificationTitle('Ítem actualizado'),

                    DeleteAction::make()
                        ->label('Eliminar')
                        ->icon('heroicon-o-trash')
                        ->requiresConfirmation()
                        ->modalHeading('¿Eliminar del menú?')
                        ->modalDescription(fn($record) => "Estás a punto de eliminar {$record->name}. Esta acción no se puede deshacer.")
                        ->successNotificationTitle('Ítem eliminado del menú'),
                ])->button()->label('Acciones'),
            ])
            ->bulkActions([
                BulkActionGroup::make([]),
            ])
            ->emptyStateHeading('No hay ítems en el menú')
            ->emptyStateDescription('Agrega platillos y productos para comenzar a vender.')
            ->emptyStateActions([
                CreateAction::make()
                    ->label('Crear Ítem de Menú')
                    ->form($this->createOrEditForm()),
            ])
            ->defaultSort('name', 'asc');
    }

    public function render(): View
    {
        return view('livewire.Producto.menu-item');
    }
}