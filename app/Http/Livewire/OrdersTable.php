<?php

namespace App\Http\Livewire;

use App\Models\Accounting\AssociatedExpenses;
use App\Models\CrmActivities\CrmActivitiesMeetings;
use App\Models\Users\User;
use Livewire\Component;
use Livewire\WithPagination;

class OrdersTable extends Component
{
    use WithPagination;

    public $buscar;
    public $selectedUser;
    public $selectedAnio;
    public $selectedMes;
    public $usuarios;
    public $perPage = 10;
    public $sortColumn = 'reference'; // Columna por defecto
    public $sortDirection = 'asc'; // Dirección por defecto

    protected $orders; // Propiedad protegida para los usuarios

    public function mount(){
        $this->usuarios = User::all();
    }


    public function render()
    {
        $this->actualizarNominas(); // Ahora se llama directamente en render para refrescar los clientes.
        return view('livewire.orders-table', [
            'orders' => $this->orders
        ]);
    }

    protected function actualizarNominas()
    {
        $query = AssociatedExpenses::where('state', 'PENDIENTE')
            ->where('aceptado_gestor', null)
            ->when($this->buscar, function ($query) {
            $query->whereHas('cliente', function ($subQuery) {
                $subQuery->where('name', 'like', '%' . $this->buscar . '%');
            })
            ->orWhereHas('OrdenCompra', function ($subQuery) { // Busca en los conceptos de presupuesto
                $subQuery->where('reference', 'like', '%' . $this->buscar . '%')
                    ->orWhere('quantity', 'like', '%' . $this->buscar . '%');
            })
            ->orWhere('subject', 'like', '%' . $this->buscar . '%')
            ->orWhere('date', 'like', '%' . $this->buscar . '%');
        })
        ->join('purchase_order', 'associated_expenses.purchase_order_id', '=', 'purchase_order.id')
        ->join('clients', 'purchase_order.client_id', '=', 'clients.id')
        ->join('suppliers', 'purchase_order.supplier_id', '=', 'suppliers.id')
        ->select('associated_expenses.*', 'clients.name as clienteNombre','suppliers.name as proveedorNombre');

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
