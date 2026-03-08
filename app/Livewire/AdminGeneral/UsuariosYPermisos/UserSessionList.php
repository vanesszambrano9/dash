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
use Carbon\Carbon;
use App\Models\Session;
use App\Models\UserSession;
use Filament\Forms\Components\Repeater;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.collapsable')]
class UserSessionList extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithTable;
    use InteractsWithSchemas;


    public function createOrEditForm(): array
    {
        return [
            TextInput::make('user.name')
                ->label('Usuario')
                ->disabled(),
            TextInput::make('ip_address')
                ->label('Dirección IP')
                ->disabled(),
            TextInput::make('user_agent')
                ->label('Agente de Usuario')
                ->disabled(),
            TextInput::make('last_activity')
                ->label('Última Actividad')
                ->disabled(),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn(): Builder => UserSession::query())
            ->columns([
                TextColumn::make('user.name')->label('Usuario'),
                TextColumn::make('ip_address')->label('Dirección IP'),
                TextColumn::make('user_agent')->label('Agente de Usuario')
                    ->limit(50),

                // formatear last_activity como fecha y hora legible
                TextColumn::make('last_activity')
                    ->dateTime('d/m/Y H:i:s')
                    ->label('Última Actividad'),

                // columna virtual para ver cuando vence la sesión (30 minutos después de la última actividad)


                TextColumn::make('expires_at')
                    ->label('Expira En')
                    ->getStateUsing(function (UserSession $record): ?string {
                        if (! $record->last_activity) {
                            return null;
                        }

                        $expiresAt = Carbon::createFromTimestamp(
                            $record->last_activity
                        )->addMinutes(config('session.lifetime'));

                        return $expiresAt->isPast()
                            ? 'Vencida'
                            : $expiresAt->format('d/m/Y H:i:s');
                    }),

            ])
            ->filters([
                // filtrar por usuarios que tienen sesiones activas
                Filter::make('active_sessions')
                    ->label('Sesiones Activas')
                    ->query(fn(Builder $query): Builder => $query->where('last_activity', '>=', now()->subMinutes(30)->timestamp)),

                // join para obtener sesiones de un usuario específico
                SelectFilter::make('user_id')
                    ->label('Usuario')
                    ->searchable()
                    ->relationship('user', 'name'),



            ], layout: FiltersLayout::AboveContent)
            ->headerActions([])
            ->recordActions([
                DeleteAction::make()
                    ->label('Terminar Sesión'),

                ViewAction::make()
                    ->label('Ver Detalles')
                    ->form($this->createOrEditForm()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }

    public function render(): View
    {
        return view('livewire.admin-general.usuarios-ypermisos.session-list');
    }
}
