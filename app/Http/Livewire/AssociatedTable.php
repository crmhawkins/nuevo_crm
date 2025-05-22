<?php

namespace App\Http\Livewire;

use App\Exports\AsociadosExport;
use App\Models\Accounting\AssociatedExpenses;
use App\Models\Clients\Client;
use App\Models\Other\BankAccounts;
use App\Models\Suppliers\Supplier;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;


class AssociatedTable extends Component
{
    use WithPagination;

    public $buscar;
    public $selectedYear;
    public $startDate;
    public $selectedBanco;
    public $Bancos;
    public $selectedSupplier;
    public $Suppliers;
    public $endDate;
    public $startDateRecived;
    public $endDateRecived;
    public $clientes;
    public $estados;
    public $perPage = 10;
    public $sortColumn = 'created_at'; // Columna por defecto
    public $sortDirection = 'desc'; // Dirección por defecto

    protected $gastos; // Propiedad protegida para los gastosbusqueda


    public function mount(){
        $this->selectedYear = Carbon::now()->year;
        $this->Bancos = BankAccounts::all();
        $this->Suppliers = Supplier::all();

    }
    public function render()
    {
        $this->actualizargastos(); // Ahora se llama directamente en render para refrescar los gastos.
        return view('livewire.associated-table', [
            'gastos' => $this->gastos
        ]);
    }

    protected function actualizargastos()
    {
        // Comprueba si se ha seleccionado "Todos" para la paginación
        $query = AssociatedExpenses::select('associated_expenses.*', 'suppliers.name as supplier_name', DB::raw('associated_expenses.quantity * (associated_expenses.iva / 100) as iva_amount'), DB::raw('associated_expenses.quantity + (COALESCE(associated_expenses.quantity, 0) * (COALESCE(associated_expenses.iva, 0) / 100)) as total_with_iva'))
        ->when($this->buscar, function ($query) {
            $query->where(function ($subQuery) {
                $subQuery->where('associated_expenses.title', 'like', '%' . $this->buscar . '%')
                    ->orwhere('associated_expenses.quantity', 'like', '%' . $this->buscar . '%')
                    ->orwhere('associated_expenses.date', 'like', '%' . $this->buscar . '%')
                    ->orwhere('associated_expenses.received_date', 'like', '%' . $this->buscar . '%')
                    ->orwhere('associated_expenses.purchase_order_id', 'like', '%' . $this->buscar . '%')
                    ->orwhere('associated_expenses.reference', 'like', '%' . $this->buscar . '%')
                    ->orwhere('suppliers.name', 'like', '%' . $this->buscar . '%')
                    ->orWhereRaw('associated_expenses.quantity * (associated_expenses.iva / 100) like ?', ['%' . $this->buscar . '%']) // for iva_amount
                    ->orWhereRaw('associated_expenses.quantity + (COALESCE(associated_expenses.quantity, 0) * (COALESCE(associated_expenses.iva, 0) / 100)) like ?', ['%' . $this->buscar . '%']) // for total_with_iva
                    ->orWhereHas('OrdenCompra.Proveedor', function ($subQuery) {
                            $subQuery->where('suppliers.name', 'like', '%' . $this->buscar . '%');
                        })
                    ->orWhereHas('OrdenCompra.cliente', function ($subQuery) {
                            $subQuery->where('clients.name', 'like', '%' . $this->buscar . '%');
                });
            });
        })
        ->when($this->selectedBanco, function ($query) {
            $query->where('associated_expenses.bank_id', $this->selectedBanco);
        })
        ->when($this->selectedSupplier, function ($query) {
            $query->where('suppliers.id', $this->selectedSupplier);
        })
        ->when($this->selectedYear, function ($query) {
            $query->whereYear('associated_expenses.created_at', $this->selectedYear);
        })
        ->when($this->startDate, function ($query) {
            $query->whereDate('associated_expenses.date', '>=', Carbon::parse($this->startDate));
        })
        ->when($this->endDate, function ($query) {
            $query->whereDate('associated_expenses.date', '<=', Carbon::parse($this->endDate));
        })
        ->when($this->startDateRecived, function ($query) {
            $query->whereDate('associated_expenses.received_date', '>=', Carbon::parse($this->startDateRecived));
        })
        ->when($this->endDateRecived, function ($query) {
            $query->whereDate('associated_expenses.received_date', '<=', Carbon::parse($this->endDateRecived));
        })
        ->join('purchase_order', 'associated_expenses.purchase_order_id', '=', 'purchase_order.id') // Join con la tabla purchase_order
        ->join('suppliers', 'purchase_order.supplier_id', '=', 'suppliers.id'); // Join con la tabla suppliers

        // Aplica la ordenación
        $query->orderBy($this->sortColumn, $this->sortDirection);

        // Verifica si se seleccionó 'all' para mostrar todos los registros
        $this->gastos = $this->perPage === 'all' ? $query->get() : $query->paginate(is_numeric($this->perPage) ? $this->perPage : 10);
    }

    public function getGastos()
    {
        // Si es necesario, puedes incluir lógica adicional aquí antes de devolver los gastos
        return $this->gastos;
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
        if ($propertyName === 'buscar' || $propertyName === 'selectedBanco' || $propertyName === 'selectedSupplier' || $propertyName === 'endDate'|| $propertyName === 'startDate'|| $propertyName === 'selectedYear') {
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
        return Excel::download(new AsociadosExport($gastos), 'gastos_asociados.xlsx');
    }

}
