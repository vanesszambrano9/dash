<?php

namespace App\Livewire\Proveedor;

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
class Proveedor extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithTable;
    use InteractsWithSchemas;

    public function createOrEditForm(): array
    {
        return [
            TextInput::make('name')
                ->label('Nombre del Proveedor / Razón Social')
                ->required()
                ->maxLength(255)
                ->placeholder('Ej: Distribuidora ABC S.A. de C.V.'),

            TextInput::make('contact_name')
                ->label('Nombre de Contacto')
                ->nullable()
                ->maxLength(255)
                ->placeholder('Ej: Juan Pérez'),

            TextInput::make('phone')
                ->label('Teléfono')
                ->nullable()
                ->tel()
                ->maxLength(20)
                ->placeholder('Ej: +52 123 456 7890'),

            TextInput::make('email')
                ->label('Correo Electrónico')
                ->nullable()
                ->email()
                ->maxLength(255)
                ->placeholder('contacto@proveedor.com'),

            Textarea::make('address')
                ->label('Dirección')
                ->nullable()
                ->maxLength(1000)
                ->rows(3)
                ->placeholder('Calle, número, colonia, ciudad, estado, CP'),

            TextInput::make('rfc')
                ->label('RFC')
                ->nullable()
                ->maxLength(13)
                ->placeholder('Ej: ABC123456XYZ')
                ->helperText('Registro Federal de Contribuyentes para facturación'),

            Toggle::make('is_active')
                ->label('Proveedor Activo')
                ->default(true)
                ->helperText('Desactiva para ocultar en selecciones sin eliminar historial'),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn(): Builder => Supplier::query())
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('display_name')
                    ->label('Proveedor / Contacto')
                    ->searchable(['name', 'contact_name'])
                    ->sortable()
                    ->description(fn($record): string => $record->contact_name ? $record->name : 'Sin contacto asignado'),

                TextColumn::make('phone')
                    ->label('Teléfono')
                    ->searchable()
                    ->copyable()
                    ->toggleable(),

                TextColumn::make('email')
                    ->label('Correo')
                    ->searchable()
                    ->copyable()
                    ->limit(30)
                    ->toggleable(),

                TextColumn::make('rfc')
                    ->label('RFC')
                    ->searchable()
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('address')
                    ->label('Dirección')
                    ->limit(40)
                    ->tooltip(fn($record) => $record->address)
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean()
                    ->sortable()
                    ->trueColor('success')
                    ->falseColor('danger'),

                TextColumn::make('products_count')
                    ->label('Productos')
                    ->counts('products')
                    ->badge()
                    ->color('primary')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Registrado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
            ], layout: FiltersLayout::AboveContent)
            ->headerActions([
                CreateAction::make()
                    ->label('Nuevo Proveedor')
                    ->form($this->createOrEditForm())
                    ->successNotificationTitle('Proveedor creado exitosamente'),
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
                        ->successNotificationTitle('Proveedor actualizado'),
                        
                    DeleteAction::make()
                        ->label('Eliminar')
                        ->icon('heroicon-o-trash')
                        ->requiresConfirmation()
                        ->modalHeading('¿Eliminar proveedor?')
                        ->modalDescription('Esta acción no se puede deshacer. ¿Estás seguro de que deseas eliminar este proveedor?')
                        ->successNotificationTitle('Proveedor eliminado'),
                ])->button()->label('Acciones'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    // Acciones masivas futuras
                ]),
            ])
            ->emptyStateHeading('No hay proveedores registrados')
            ->emptyStateDescription('Crea tu primer proveedor para comenzar a gestionar tu cadena de suministro.')
            ->emptyStateActions([
                CreateAction::make()
                    ->label('Crear Proveedor')
                    ->form($this->createOrEditForm())
                    ->successNotificationTitle('Proveedor creado exitosamente'),
            ]);
    }

    public function render(): View
    {
        return view('livewire.Proveedor.proveedor');
    }
}