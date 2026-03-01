{!! '<?php' !!}

namespace {{ $data['controller']['namespace'] }};
use Livewire\Component;
use App\Traits\HasPaginaActual;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.collapsable')]
class {{ $data['controller']['className'] }} extends Component
{
    use HasPaginaActual;
    
    public $pagina;

    public function mount()
    {
        $this->pagina = $this->cargarPagina();
    }

    public function render()
    {
       return view('{{ $data['view']['viewName'] }}');
    }
}