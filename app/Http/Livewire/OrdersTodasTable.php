<?php

namespace App\Http\Livewire;

use App\Models\CrmActivities\CrmActivitiesMeetings;
use App\Models\PurcharseOrde\PurcharseOrder;
use App\Models\Users\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class OrdersTodasTable extends Component
{
    use WithPagination;

    public $buscar;
    public $selectedUser;
    public $selectedAnio;
    public $selectedMes;
    public $usuarios;
    public $perPage = 10;
    public $sortColumn = 'created_at'; // Columna por defecto
    public $sortDirection = 'desc'; // Dirección por defecto
    protected $orders; // Propiedad protegida para los usuarios

    public function mount(){
        $this->usuarios = User::all();
        $this->selectedUser = Auth::user()->id;
    }


    public function render()
    {
        $this->actualizar(); // Ahora se llama directamente en render para refrescar los clientes.
        return view('livewire.orders-todas-table', [
            'orders' => $this->orders
        ]);
    }

    protected function actualizar()
    {
        $query = PurcharseOrder::when($this->buscar,function ($query) {
                $query->whereHas('cliente', function ($subQuery) {
                    $subQuery->where('name', 'like', '%' . $this->buscar . '%');
                })
                ->orWhereHas('Proveedor', function ($subQuery) { // Busca en los conceptos de presupuesto
                    $subQuery->where('name', 'like', '%' . $this->buscar . '%')
                        ->orWhere('quantity', 'like', '%' . $this->buscar . '%');
                })
                ->orWhere('purchase_order.created_at', 'like', '%' . $this->buscar . '%')
                ->orWhere('purchase_order.id', 'like', '%' . $this->buscar . '%')
                ->orWhere('budgets.reference', 'like', '%' . $this->buscar . '%')
                ->orWhere('associated_expenses.reference', 'like', '%' . $this->buscar . '%');
            })
            ->join('associated_expenses', 'purchase_order.id', '=', 'associated_expenses.purchase_order_id')
            ->join('budget_concepts', 'purchase_order.budget_concept_id', '=', 'budget_concepts.id') // Join para llegar a los conceptos
            ->join('budgets', 'budget_concepts.budget_id', '=', 'budgets.id') // Join para llegar a los presupuestos
            ->join('admin_user', 'budgets.admin_user_id', '=', 'admin_user.id') // Join para llegar al usuario
            ->join('clients', 'purchase_order.client_id', '=', 'clients.id')
            ->join('suppliers', 'purchase_order.supplier_id', '=', 'suppliers.id')
            ->select('associated_expenses.*','purchase_order.id as orden', 'budgets.reference as presupuesto','clients.name as clienteNombre','suppliers.name as proveedorNombre', 'admin_user.name as gestorNombre');

        $query->orderBy($this->sortColumn, $this->sortDirection);

        // Verifica si se seleccionó 'all' para mostrar todos los registros
        $this->orders = $this->perPage === 'all' ? $query->get() : $query->paginate(is_numeric($this->perPage) ? $this->perPage : 10);
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
        if ($propertyName === 'buscar' || $propertyName === 'selectedUser' || $propertyName === 'selectedAnio' || $propertyName === 'selectedMes') {
            $this->resetPage(); // Resetear la paginación solo cuando estos filtros cambien.
        }
    }


}
