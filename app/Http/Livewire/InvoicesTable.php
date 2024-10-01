<?php

namespace App\Http\Livewire;

use App\Exports\InvoicesExport;
use App\Models\Invoices\Invoice;
use App\Models\Invoices\InvoiceStatus;
use App\Models\Users\User;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class InvoicesTable extends Component
{
    use WithPagination;

    public $gestores;
    public $estados;
    public $buscar;
    public $selectedYear;
    public $maxImporte;
    public $minImporte;
    public $startDate;
    public $endDate;
    public $selectedGestor = '';
    public $selectedEstados = '';
    public $perPage = 10;
    public $sortColumn = 'created_at'; // Columna por defecto
    public $sortDirection = 'desc'; // Dirección por defecto
    protected $budgets; // Propiedad protegida para los usuarios

    public function mount(){
        $this->gestores = User::where('access_level_id', 4)->get();
        $this->estados = InvoiceStatus::all();
        $this->selectedYear = Carbon::now()->year;

    }

    public function render()
    {

        $this->actualizarPresupuestos(); // Inicializa los usuarios
        return view('livewire.invoices-table', [
            'invoices' => $this->getBudgets() // Utiliza un método para obtener los usuarios
        ]);
    }

    protected function actualizarPresupuestos()
    {
        $query = Invoice::query()
            ->when($this->buscar, function ($query) {
                // Búsqueda por referencia y en relaciones
                $query->where(function ($query) {
                    $query->where('reference', 'like', '%' . $this->buscar . '%')
                        ->orWhereHas('cliente', function ($subQuery) {
                            $subQuery->where('name', 'like', '%' . $this->buscar . '%')
                                    ->orWhere('email', 'like', '%' . $this->buscar . '%');
                        })
                        ->orWhereHas('project', function ($subQuery) {
                            $subQuery->where('name', 'like', '%' . $this->buscar . '%');
                        });
                });
            })
            ->when($this->selectedGestor, function ($query) {
                $query->where('admin_user_id', $this->selectedGestor);
            })
            ->when($this->selectedEstados, function ($query) {
                $query->where('invoice_status_id', $this->selectedEstados);
            })
            ->when($this->selectedYear, function ($query) {
                $query->whereYear('created_at', $this->selectedYear);
            })
            ->when($this->minImporte, function ($query) {
                $query->where('total', '>=', $this->minImporte);
            })
            ->when($this->maxImporte, function ($query) {
                $query->where('total', '<=', $this->maxImporte);
            })
            ->when($this->startDate, function ($query) {
                $query->whereDate('created_at', '>=', Carbon::parse($this->startDate));
            })
            ->when($this->endDate, function ($query) {
                $query->whereDate('created_at', '<=', Carbon::parse($this->endDate));
            });


       // Aplica la ordenación
       $query->orderBy($this->sortColumn, $this->sortDirection);

       // Verifica si se seleccionó 'all' para mostrar todos los registros
       $this->budgets = $this->perPage === 'all' ? $query->get() : $query->paginate(is_numeric($this->perPage) ? $this->perPage : 10);
    }

    public function getBudgets()
    {
        // Si es necesario, puedes incluir lógica adicional aquí antes de devolver los usuarios
        return $this->budgets;
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

    // Supongamos que tienes un método para actualizar los filtros
    public function aplicarFiltro()
    {
        // Aquí aplicarías los filtros
        // Por ejemplo: $this->filtroEspecifico = 'valor';

        $this->actualizarPresupuestos(); // Luego actualizas la lista de usuarios basada en los filtros
    }

    public function updating($propertyName)
    {
        if ($propertyName === 'buscar' || $propertyName === 'selectedGestor' || $propertyName === 'selectedEstados' || $propertyName === 'perPage') {
            if($propertyName !== 'perPage' || $this->perPage !== 'all') {
                $this->resetPage();
            }
            // $this->actualizarPresupuestos();
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
        $this->actualizarPresupuestos();
        // Genera las facturas basadas en los filtros actuales
        $invoices = $this->getBudgets();
        $this->perPage = $paginate;
        // Exporta los datos a Excel
        return Excel::download(new InvoicesExport($invoices), 'facturas.xlsx');
    }

    public function downloadFilteredInvoicesZip()
    {
        $paginate = $this->perPage ;
        $this->perPage = 'all';
        $this->actualizarPresupuestos();
        // Genera las facturas basadas en los filtros actuales
        $invoices = $this->getBudgets();
        $this->perPage = $paginate;

        // Convertir los IDs de las facturas filtradas en un array
        $invoiceIds = $invoices->pluck('id')->toArray();

        // Redirigir a la ruta que genera el ZIP con los PDFs
        return redirect()->route('factura.generateMultiplePDFs', ['invoice_ids' => $invoiceIds]);
    }
}
