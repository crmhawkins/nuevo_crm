<?php

namespace App\Http\Livewire;

use App\Models\Budgets\Budget;
use App\Models\Budgets\BudgetStatu;
use App\Models\Users\User;
use App\Models\Users\UserAccessLevel;
use App\Models\Users\UserDepartament;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class BudgetsTable extends Component
{
    use WithPagination;

    public $gestores;
    public $estados;
    public $meses = [
        1 => 'Enero',
        2 => 'Febrero',
        3 => 'Marzo',
        4 => 'Abril',
        5 => 'Mayo',
        6 => 'Junio',
        7 => 'Julio',
        8 => 'Agosto',
        9 => 'Septiembre',
        10 => 'Octubre',
        11 => 'Noviembre',
        12 => 'Diciembre'
    ];
    public $buscar;
    public $selectedGestor = '';
    public $selectedEstados = '';
    public $perPage = 10;

    public $sortColumn = 'created_at'; // Columna por defecto
    public $sortDirection = 'desc'; // Dirección por defecto
    public $selectedMonth;
    public $selectedYear;

    protected $budgets; // Propiedad protegida para los usuarios

    public function mount(){
        $this->gestores = User::where('inactive',0)->where('access_level_id', 4)->get();
        $this->estados = BudgetStatu::all();
        $this->selectedYear = Carbon::now()->year;

    }

    public function render()
    {

        $this->actualizarPresupuestos(); // Inicializa los presupuestos
        return view('livewire.budgets-table', [
            'budgets' => $this->getBudgets() // Utiliza un método para obtener los presupuestos
        ]);
    }

    protected function actualizarPresupuestos()
    {
        $query = Budget::
            when($this->buscar, function ($query) {
                $query->whereHas('cliente', function ($subQuery) {
                    $subQuery->where('company', 'like', '%' . $this->buscar . '%')
                            ->orWhere('name', 'like', '%' . $this->buscar . '%')
                            ->orWhere('email', 'like', '%' . $this->buscar . '%');
                })
                ->orWhereHas('proyecto', function ($subQuery) { // Busca en los conceptos de presupuesto
                    $subQuery->where('name', 'like', '%' . $this->buscar . '%');
                })
                ->orWhere('reference', 'like', '%' . $this->buscar . '%');
            })
            ->when($this->selectedGestor, function ($query) {
                $query->where('admin_user_id', $this->selectedGestor);
            })
            ->when($this->selectedEstados, function ($query) {
                $query->where('budget_status_id', $this->selectedEstados);

            })->when($this->selectedMonth, function ($query) {
                $query->whereMonth('creation_date', $this->selectedMonth);

            })->when($this->selectedYear, function ($query) {
                $query->whereYear('creation_date', $this->selectedYear);

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

    // Supongamos que tienes un método para actualizar los filtros
    public function aplicarFiltro()
    {
        // Aquí aplicarías los filtros
        // Por ejemplo: $this->filtroEspecifico = 'valor';

        $this->actualizarPresupuestos(); // Luego actualizas la lista de usuarios basada en los filtros
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
