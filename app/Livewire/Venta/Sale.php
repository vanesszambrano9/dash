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
use Livewire\Attributes\Computed;
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
    public string $search = '';
    public array $formData = [
        'table_number'   => null,
        'payment_method' => 'cash',
        'discount'       => 0,
        'discount_type'  => 'amount',
        'notes'          => null,
    ];

    // Propiedades para el modal de cierre
    public bool $showCloseModal = false;
    public ?int $closingSaleId = null;
    public string $closePaymentMethod = 'cash';
    public ?string $closeTransferReference = null;

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
            ->when($this->search, fn($q) => $q
                ->where('folio', 'like', "%{$this->search}%")
                ->orWhere('table_number', 'like', "%{$this->search}%")
            )
            ->latest('created_at');
    }

    #[Computed]
    public function ventasHoy(): int
    {
        return SaleModel::closed()->whereDate('closed_at', today())->count();
    }

    #[Computed]
    public function totalHoy(): float
    {
        return (float) SaleModel::closed()->whereDate('closed_at', today())->sum('total');
    }

    #[Computed]
    public function ticketPromedio(): float
    {
        $count = SaleModel::closed()->whereDate('closed_at', today())->count();
        $total = (float) SaleModel::closed()->whereDate('closed_at', today())->sum('total');
        return $count > 0 ? round($total / $count, 2) : 0;
    }

    #[Computed]
    public function ventasActivas(): int
    {
        return SaleModel::open()->count();
    }

    public function render(): View
    {
        $sales = $this->getActiveSalesQuery()
            ->paginate(12)
            ->withQueryString();

        return view('livewire.Venta.sale', [
            'sales'          => $sales,
            'ventasHoy'      => $this->ventasHoy,
            'totalHoy'       => $this->totalHoy,
            'ticketPromedio' => $this->ticketPromedio,
            'ventasActivas'  => $this->ventasActivas,
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
            'table_number'   => null,
            'payment_method' => 'cash',
            'discount'       => 0,
            'discount_type'  => 'amount',
            'notes'          => null,
        ];
        $this->showCreateModal = false;
        $this->resetErrorBag();
    }

    public function createSale(): void
    {
        $validated = $this->validate([
            'formData.table_number'   => 'nullable|string|max:50',
            'formData.payment_method' => 'required|in:cash,card,transfer',
            'formData.discount'       => 'nullable|numeric|min:0|max:999999.99',
            'formData.discount_type'  => 'required|in:amount,percentage',
            'formData.notes'          => 'nullable|string|max:1000',
        ]);

        SaleModel::create([
            'table_number'   => $validated['formData']['table_number'] ?? null,
            'payment_method' => $validated['formData']['payment_method'],
            'status'         => 'open',
            'subtotal'       => 0,
            'discount'       => $validated['formData']['discount'] ?? 0,
            'discount_type'  => $validated['formData']['discount_type'] ?? 'amount',
            'total'          => 0,
            'notes'          => $validated['formData']['notes'] ?? null,
        ]);

        session()->flash('success', 'Venta creada exitosamente');
        $this->resetForm();
        $this->dispatch('$refresh');
    }

    public function openCloseModal(int $saleId): void
    {
        $this->closingSaleId = $saleId;
        $this->closePaymentMethod = 'cash';
        $this->closeTransferReference = null;
        $this->showCloseModal = true;
    }

    public function resetCloseModal(): void
    {
        $this->showCloseModal = false;
        $this->closingSaleId = null;
        $this->closePaymentMethod = 'cash';
        $this->closeTransferReference = null;
    }

    public function confirmCloseSale(): void
    {
        $this->validate([
            'closePaymentMethod'     => 'required|in:cash,card,transfer',
            'closeTransferReference' => [
                $this->closePaymentMethod === 'transfer' ? 'required' : 'nullable',
                'string',
                'max:100',
            ],
        ], [
            'closeTransferReference.required' => 'El ID de transferencia es obligatorio.',
        ]);

        $sale = SaleModel::findOrFail($this->closingSaleId);
        if ($sale->status === 'open') {
            $sale->close($this->closePaymentMethod, $this->closeTransferReference);
            session()->flash('success', 'Venta cerrada exitosamente');
        }

        $this->resetCloseModal();
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
        $sale = SaleModel::with('items.menuItem.product')->findOrFail($saleId);
        if ($sale->status === 'open') {
            foreach ($sale->items as $item) {
                $product = $item->menuItem?->product;
                if ($product && $item->quantity > 0) {
                    $product->increaseStock(
                        quantity: (float) $item->quantity,
                        type: 'adjustment',
                        reason: 'Cancelación venta ' . $sale->folio
                    );
                }
            }
            $sale->cancel();
            $this->dispatch('$refresh');
        }
    }

    public function deleteSale(int $saleId): void
    {
        $sale = SaleModel::with('items.menuItem.product')->findOrFail($saleId);
        if ($sale->status === 'open') {
            foreach ($sale->items as $item) {
                $product = $item->menuItem?->product;
                if ($product && $item->quantity > 0) {
                    $product->increaseStock(
                        quantity: (float) $item->quantity,
                        type: 'adjustment',
                        reason: 'Eliminación venta ' . $sale->folio
                    );
                }
            }
            $sale->delete();
            $this->dispatch('$refresh');
        }
    }
}