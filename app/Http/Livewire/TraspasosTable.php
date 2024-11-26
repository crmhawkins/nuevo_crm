<?php

namespace App\Http\Livewire;

use App\Exports\GastosExport;
use App\Models\Accounting\Gasto;
use App\Models\Accounting\Traspaso;
use App\Models\Clients\Client;
use App\Models\Other\BankAccounts;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;


class TraspasosTable extends Component
{
    use WithPagination;

    public $buscar;
    public $selectedYear;
    public $startDate;
    public $endDate;
    public $selectedBanco;
    public $Bancos;
    public $clientes;
    public $estados;
    public $perPage = 10;
    public $sortColumn = 'created_at'; // Columna por defecto
    public $sortDirection = 'desc'; // Dirección por defecto
    protected $traspasos; // Propiedad protegida para los gastosbusqueda


    public function mount(){
        $this->selectedYear = Carbon::now()->year;
        $this->Bancos = BankAccounts::all();
    }
    public function render()
    {
        $this->actualizargastos(); // Ahora se llama directamente en render para refrescar los gastos.
        return view('livewire.traspasos-table', [
            'traspasos' => $this->traspasos
        ]);
    }

    protected function actualizargastos()
    {
        // Comprueba si se ha seleccionado "Todos" para la paginación

        $query = Traspaso::when($this->buscar, function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->where('amount', 'like', '%' . $this->buscar . '%')
                        ->orWhere('fecha', 'like', '%' . $this->buscar . '%')
                        ->orWhereHas('from', function ($subQuery) {
                            $subQuery->where('bank_accounts.name', 'like', '%' . $this->buscar . '%');
                        })
                        ->orWhereHas('to', function ($subQuery) {
                            $subQuery->where('bank_accounts.name', 'like', '%' . $this->buscar . '%');
                        });
                });
            })
            ->when($this->selectedYear, function ($query) {
                $query->whereYear('fecha', $this->selectedYear);
            })
            ->when($this->startDate, function ($query) {
                $query->whereDate('fecha', '>=', Carbon::parse($this->startDate));
            })
            ->when($this->endDate, function ($query) {
                $query->whereDate('fecha', '<=', Carbon::parse($this->endDate));
            });

         // Aplica la ordenación
         $query->orderBy($this->sortColumn, $this->sortDirection);

         // Verifica si se seleccionó 'all' para mostrar todos los registros
         $this->traspasos = $this->perPage === 'all' ? $query->get() : $query->paginate(is_numeric($this->perPage) ? $this->perPage : 10);
    }

    public function getGastos()
    {
        // Si es necesario, puedes incluir lógica adicional aquí antes de devolver los gastos
        return $this->traspasos;
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
    public function aplicarFiltro()
    {
        // Aquí aplicarías los filtros
        // Por ejemplo: $this->filtroEspecifico = 'valor';

        $this->actualizargastos(); // Luego actualizas la lista de gastos basada en los filtros
    }

    public function updating($propertyName)
    {
        if ($propertyName === 'buscar' || $propertyName === 'selectedBanco' || $propertyName === 'endDate'|| $propertyName === 'startDate'|| $propertyName === 'selectedYear') {
            $this->resetPage(); // Resetear la paginación solo cuando estos filtros cambien.
        }
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function exportToExcel()
    {
        $paginate = $this->perPage ;
        $this->perPage = 'all';
        $this->actualizargastos();
        // Genera las facturas basadas en los filtros actuales
        $gastos = $this->getGastos();
        $this->perPage = $paginate;
        // Exporta los datos a Excel
        return Excel::download(new GastosExport($gastos), 'gastos.xlsx');
    }

}
