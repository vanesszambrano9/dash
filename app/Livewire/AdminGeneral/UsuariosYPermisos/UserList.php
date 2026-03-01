<?php

namespace App\Livewire\AdminGeneral\UsuariosYPermisos;

use App\Models\Tenants\Tenant;
use App\Models\Contabilidad\Transaccion;
use Dom\Text;
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
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\ToggleButtons;

use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Livewire\Component;

use App\Models\User;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Repeater;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.collapsable')]
class UserList extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithTable;
    use InteractsWithSchemas;


    public static function createOrEditForm(): array
    {
        return [
            TextInput::make('name')
                ->label('Nombre')
                ->required()
                ->maxLength(255),

            TextInput::make('email')
                ->label('Correo Electrónico')
                ->required()
                ->unique(User::class, 'email', ignoreRecord: true)
                ->email()
                ->maxLength(255),

            TextInput::make('password')
                ->label('Contraseña')
                ->password()
                ->minLength(8)
                ->maxLength(255)
                ->dehydrated(fn($state) => filled($state)),
            
            CheckboxList::make('roles')
                ->label('Roles')
                ->bulkToggleable()
                ->relationship(name: 'roles', titleAttribute: 'name')
                ->columns(3)
                ->searchable(),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn(): Builder => User::query())
            ->columns([
                TextColumn::make('id'),
                TextColumn::make('name')->label('Nombre'),
                TextColumn::make('email')->label('Correo Electrónico'),
                
                
                
                TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Creado El')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->label('Actualizado El')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([], layout: FiltersLayout::AboveContent)
            ->headerActions([
                CreateAction::make()
                    ->label('Crear Nuevo Usuario')
                    ->form($this->createOrEditForm())
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->label('Ver Detalles')
                        ->form($this->createOrEditForm())
                        ->icon('heroicon-o-eye'),
                    EditAction::make()
                        ->form($this->createOrEditForm()),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }

    public function render(): View
    {
        return view('livewire.admin-general.usuarios-ypermisos.user-list');
    }
}
