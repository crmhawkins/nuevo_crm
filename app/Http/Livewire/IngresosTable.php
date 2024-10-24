<?php

namespace App\Http\Livewire;

use App\Models\Accounting\Ingreso;
use App\Models\Clients\Client;
use App\Models\Other\BankAccounts;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class IngresosTable extends Component
{
    use WithPagination;

    public $buscar;
    public $selectedCliente = '';
    public $selectedEstado;
    public $selectedYear;
    public $startDate;
    public $selectedBanco;
    public $Bancos;
    public $endDate;
    public $clientes;
    public $estados;
    public $perPage = 10;
    public $sortColumn = 'created_at'; // Columna por defecto
    public $sortDirection = 'desc'; // Dirección por defecto
    protected $ingresos; // Propiedad protegida para los gastosbusqueda

    public function mount(){
        $this->selectedYear = Carbon::now()->year;
        $this->Bancos = BankAccounts::all();
    }

    public function render()
    {
        $this->actualizargastos(); // Ahora se llama directamente en render para refrescar los gastos.
        return view('livewire.ingresos-table', [
            'ingresos' => $this->ingresos
        ]);
    }

    protected function actualizargastos()
    {
        $query = Ingreso::when($this->buscar, function ($query) {
                    $query->where('title', 'like', '%' . $this->buscar . '%');
                })
                ->when($this->selectedYear, function ($query) {
                    $query->whereYear('created_at', $this->selectedYear);
                })
                ->when($this->selectedBanco, function ($query) {
                    $query->where('bank_id', $this->selectedBanco);
                })
                ->when($this->startDate, function ($query) {
                    $query->whereDate('date', '>=', Carbon::parse($this->startDate));
                })
                ->when($this->endDate, function ($query) {
                    $query->whereDate('date', '<=', Carbon::parse($this->endDate));
                });

         // Aplica la ordenación
         $query->orderBy($this->sortColumn, $this->sortDirection);

         // Verifica si se seleccionó 'all' para mostrar todos los registros
         $this->ingresos = $this->perPage === 'all' ? $query->get() : $query->paginate(is_numeric($this->perPage) ? $this->perPage : 10);
    }

    public function aplicarFiltro()
    {
        // Aquí aplicarías los filtros
        // Por ejemplo: $this->filtroEspecifico = 'valor';

        $this->actualizargastos(); // Luego actualizas la lista de gastos basada en los filtros
    }
    public function sortBy($column)
    {
        if ($this->sortColumn === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortColumn = $column;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }
    public function updating($propertyName)
    {
        if ($propertyName === 'buscar'|| $propertyName === 'selectedBanco' || $propertyName === 'endDate'|| $propertyName === 'startDate'|| $propertyName === 'selectedYear') {
            $this->resetPage(); // Resetear la paginación solo cuando estos filtros cambien.
        }
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }
}
