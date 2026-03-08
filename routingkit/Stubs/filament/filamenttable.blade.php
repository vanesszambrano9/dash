{!! '<?php' !!}

namespace {{ $data['controller']['namespace'] }};

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

use {{ $data['model']['full'] }};

use Filament\Forms\Components\Repeater;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.collapsable')]
class {{ $data['controller']['className'] }} extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithTable;
    use InteractsWithSchemas;


    public function createOrEditForm(): array
    {
        return [];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn(): Builder => {{ $data['model']['class'] }}::query())
            ->columns([
                TextColumn::make('id'),
            ])
            ->filters([], layout: FiltersLayout::AboveContent)
            ->headerActions([
                CreateAction::make()
                    ->label('Crear {{ $data['model']['class'] }}')
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
      return view('{{ $data['view']['viewName'] }}');
    }
}
