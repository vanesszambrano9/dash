<?php

namespace App\Livewire\AdminGeneral\UsuariosYPermisos;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\TextInput;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Select;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Tables\Columns\ImageColumn;

use Spatie\Permission\Models\Role;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Illuminate\Support\Facades\Auth;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.collapsable')]
class RolesList extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithTable;
    use InteractsWithSchemas;


    public function createOrEditForm(): array
    {
        return [
            TextInput::make('name')
                ->required()
                ->unique(Role::class, 'name', ignoreRecord: true)
                ->maxLength(255)
                ->label('Nombre del Rol'),

            Hidden::make('guard_name')
                ->default('web')
                ->label('Guard Name'),


            CheckboxList::make('permissions')
                ->label('Permisos')
                ->bulkToggleable()
                ->relationship(name: 'permissions', titleAttribute: 'name')
                ->columns(3)
                ->searchable(),


        ];
    }


    public function table(Table $table): Table
    {
        return $table
            ->query(fn(): Builder => Role::query())
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('guard_name')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Crear Nuevo Rol')
                    ->form($this->createOrEditForm())
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->label('Ver Detalles')
                        ->icon('heroicon-o-eye'),
                    EditAction::make()
                        ->form($this->createOrEditForm()),
                    DeleteAction::make(),
                ])
                    ->visible(function ($record) {
                        $rolesRestringidos = array_values(config('routingkit.roles'));
                        return !in_array($record->name, $rolesRestringidos);
                    })
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }

    public function render(): View
    {
        return view('livewire.admin-general.usuarios-ypermisos.roles-list');
    }
}
