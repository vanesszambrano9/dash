{!! '<?php' !!}

namespace {{ $data['controller']['namespace'] }};

use {{ $data['model']['full'] }};

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.collapsable')]
class {{ $data['controller']['className'] }} extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

     public function getFormSchema(): array
    {
        return [];
    }


    public function form(Schema $schema): Schema
    {
        return $schema
            ->components(
                $this->getFormSchema()
            )
            ->statePath('data')
            ->model({{ $data['model']['class'] }}::class);
    }

    public function create(): void
    {
        $data = $this->form->getState();

        $record = {{ $data['model']['class'] }}::create($data);

        $this->form->model($record)->saveRelationships();

         Notification::make()
            ->title('¡Éxito!')
            ->body('Aldea creada correctamente.')
            ->success()
            ->send();

        //$this->js('location.reload();');
        $this->data = [];
    }

    public function render(): View
    {
        return view('{{ $data['view']['viewName'] }}');
    }
}
