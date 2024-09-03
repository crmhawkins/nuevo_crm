<?php

namespace App\Http\Livewire;

use App\Models\Budgets\Budget;
use App\Models\Budgets\BudgetStatu;
use App\Models\Invoices\Invoice;
use App\Models\Invoices\InvoiceStatus;
use App\Models\Users\User;
use App\Models\Users\UserAccessLevel;
use App\Models\Users\UserDepartament;
use Livewire\Component;
use Livewire\WithPagination;

class InvoicesTable extends Component
{
    use WithPagination;

    public $gestores;
    public $estados;
    public $buscar;
    public $selectedGestor = '';
    public $selectedEstados = '';
    public $perPage = 10;
    public $sortColumn = 'reference'; // Columna por defecto
    public $sortDirection = 'asc'; // Dirección por defecto
    protected $budgets; // Propiedad protegida para los usuarios

    public function mount(){
        $this->gestores = User::where('access_level_id', 4)->get();
        $this->estados = InvoiceStatus::all();
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
        $query = Invoice::
            when($this->buscar, function ($query) {
                $query->whereHas('cliente', function ($subQuery) {
                    $subQuery->where('name', 'like', '%' . $this->buscar . '%')
                            ->orWhere('email', 'like', '%' . $this->buscar . '%');
            })
            ->orWhereHas('proyecto', function ($subQuery) { // Busca en los conceptos de presupuesto
                $subQuery->where('name', 'like', '%' . $this->buscar . '%');
            });
            })
            ->when($this->selectedGestor, function ($query) {
                $query->where('admin_user_id', $this->selectedGestor);
            })
            ->when($this->selectedEstados, function ($query) {
                $query->where('invoice_status_id', $this->selectedEstados);
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
}
