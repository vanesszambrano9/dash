<?php

namespace App\Livewire\Venta;

use App\Models\Venta\Sale as SaleModel;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;

#[Layout('components.layouts.collapsable')]
class Sale extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use WithPagination;

    protected $listeners = ['$refresh'];

    // Propiedades para el formulario de nueva venta
    public bool $showCreateModal = false;
    public array $formData = [
        'table_number' => null,
        'payment_method' => 'cash',
        'discount' => 0,
        'notes' => null,
    ];

    public function createOrEditForm(): array
    {
        return [
            TextInput::make('folio')
                ->label('Folio / Ticket')
                ->disabled()
                ->helperText('Se genera automáticamente al guardar'),

            TextInput::make('table_number')
                ->label('Mesa / Ubicación')
                ->nullable()
                ->maxLength(50)
                ->placeholder('Ej: Mesa 5, Barra, Domicilio'),

            Select::make('payment_method')
                ->label('Método de Pago')
                ->required()
                ->options([
                    'cash'     => 'Efectivo',
                    'card'     => 'Tarjeta',
                    'transfer' => 'Transferencia',
                ])
                ->default('cash')
                ->native(false),

            Select::make('status')
                ->label('Estado')
                ->required()
                ->options([
                    'open'      => 'Abierta',
                    'closed'    => 'Cerrada',
                    'cancelled' => 'Cancelada',
                ])
                ->default('open')
                ->disabled(fn($record) => $record?->status === 'closed' || $record?->status === 'cancelled')
                ->helperText('No se puede modificar si ya está cerrada o cancelada'),

            TextInput::make('subtotal')
                ->label('Subtotal')
                ->numeric()
                ->prefix('L')
                ->step(0.01)
                ->minValue(0)
                ->default(0)
                ->placeholder('0.00')
                ->readOnly()
                ->helperText('Se calcula automáticamente desde los ítems'),

            TextInput::make('discount')
                ->label('Descuento')
                ->nullable()
                ->numeric()
                ->prefix('L')
                ->step(0.01)
                ->minValue(0)
                ->default(0)
                ->placeholder('0.00'),

            TextInput::make('total')
                ->label('Total')
                ->numeric()
                ->prefix('L')
                ->step(0.01)
                ->minValue(0)
                ->default(0)
                ->placeholder('0.00')
                ->readOnly()
                ->helperText('Subtotal - Descuento'),

            Textarea::make('notes')
                ->label('Notas / Observaciones')
                ->nullable()
                ->maxLength(1000)
                ->rows(3)
                ->placeholder('Ej: Cliente frecuente, sin cebolla, entregar en mesa...'),

            DateTimePicker::make('closed_at')
                ->label('Fecha de Cierre')
                ->nullable()
                ->disabled()
                ->helperText('Se establece automáticamente al cerrar la venta'),
        ];
    }

    protected function getActiveSalesQuery(): Builder
    {
        return SaleModel::query()
            ->where('status', 'open')
            ->withCount('items')
            ->latest('created_at');
    }

    public function render(): View
    {
        $sales = $this->getActiveSalesQuery()
            ->paginate(12)
            ->withQueryString();

        return view('livewire.Venta.sale', [
            'sales' => $sales,
        ]);
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function resetForm(): void
    {
        $this->formData = [
            'table_number' => null,
            'payment_method' => 'cash',
            'discount' => 0,
            'notes' => null,
        ];
        $this->showCreateModal = false;
        $this->resetErrorBag();
    }

    public function createSale(): void
    {
        $validated = $this->validate([
            'formData.table_number' => 'nullable|string|max:50',
            'formData.payment_method' => 'required|in:cash,card,transfer',
            'formData.discount' => 'nullable|numeric|min:0|max:999999.99',
            'formData.notes' => 'nullable|string|max:1000',
        ]);

        // Generar folio automático: VTA-00001
        $lastId = SaleModel::max('id') ?? 0;
        $folio = 'VTA-' . str_pad($lastId + 1, 5, '0', STR_PAD_LEFT);

        SaleModel::create([
            'folio' => $folio,
            'table_number' => $validated['formData']['table_number'] ?? null,
            'payment_method' => $validated['formData']['payment_method'],
            'status' => 'open',
            'subtotal' => 0,
            'discount' => $validated['formData']['discount'] ?? 0,
            'total' => 0,
            'notes' => $validated['formData']['notes'] ?? null,
        ]);

        session()->flash('success', 'Venta creada exitosamente');
        $this->resetForm();
        $this->dispatch('$refresh');
    }

    public function closeSale(int $saleId, string $paymentMethod = 'cash'): void
    {
        $sale = SaleModel::findOrFail($saleId);
        if ($sale->status === 'open') {
            $sale->close($paymentMethod);
            $this->dispatch('$refresh');
        }
    }

    public function cancelSale(int $saleId): void
    {
        $sale = SaleModel::findOrFail($saleId);
        if ($sale->status === 'open') {
            $sale->cancel();
            $this->dispatch('$refresh');
        }
    }

    public function deleteSale(int $saleId): void
    {
        $sale = SaleModel::findOrFail($saleId);
        if ($sale->status === 'open') {
            $sale->delete();
            $this->dispatch('$refresh');
        }
    }
}