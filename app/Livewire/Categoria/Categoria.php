<?php

namespace App\Livewire\Categoria;

use App\Models\Categoria\Category;
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
class Categoria extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithTable;
    use InteractsWithSchemas;

    public function createOrEditForm(): array
    {
        return [
            TextInput::make('name')
                ->label('Nombre')
                ->required()
                ->maxLength(255),

            Select::make('type')
                ->label('Tipo')
                ->required()
                ->options([
                    'food'     => 'Comida',
                    'beverage' => 'Bebida',
                    'other'    => 'Otro',
                ])
                ->default('food'),

            Textarea::make('description')
                ->label('Descripción')
                ->nullable()
                ->maxLength(500),

            Toggle::make('is_active')
                ->label('Activo')
                ->default(true),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn(): Builder => Category::query())
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn($state) => match ($state) {
                        'food'     => 'Comida',
                        'beverage' => 'Bebida',
                        'other'    => 'Otro',
                        default    => $state,
                    })
                    ->color(fn($state) => match ($state) {
                        'food'     => 'success',
                        'beverage' => 'info',
                        'other'    => 'warning',
                        default    => 'gray',
                    }),

                TextColumn::make('description')
                    ->label('Descripción')
                    ->limit(50)
                    ->toggleable(),

                IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Creado El')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Actualizado El')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                
            ], layout: FiltersLayout::AboveContent)
            ->headerActions([
                CreateAction::make()
                    ->label('Crear Categoría')
                    ->form($this->createOrEditForm()),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->label('Ver Detalles')
                        ->icon('heroicon-o-eye'),
                    EditAction::make()
                        ->label('Editar')
                        ->form($this->createOrEditForm()),
                    DeleteAction::make()
                        ->label('Eliminar'),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([]),
            ]);
    }

    public function render(): View
    {
        return view('livewire.Categoria.categoria');
    }
}
