<?php

namespace App\Http\Livewire;

use App\Models\Clients\Client;
use App\Models\Users\User;
use Livewire\Component;
use Livewire\WithPagination;

class ClientsTable extends Component
{
    use WithPagination;

    public $gestores;
    public $buscar;
    public $selectedGestor = '';
    public $perPage = 10;
    public $sortColumn = 'created_at'; // Columna por defecto
    public $sortDirection = 'desc'; // Dirección por defecto
    protected $clients;

    public function mount(){
        $this->gestores = User::where('access_level_id', 4)->get();
    }

    public function render()
    {
        $this->actualizarClientes();
        return view('livewire.clients-table', [
            'clients' => $this->clients
        ]);
    }

    protected function actualizarClientes()
    {
        // Base query: solo clientes (is_client = 1)
        $query = Client::where('is_client', 1);

        // Si hay búsqueda, agregar condiciones de búsqueda agrupadas
        if (!empty(trim($this->buscar))) {
            $buscar = trim($this->buscar);
            $query->where(function ($q) use ($buscar) {
                $q->where('name', 'like', '%' . $buscar . '%')
                  ->orWhere('email', 'like', '%' . $buscar . '%')
                  ->orWhere('company', 'like', '%' . $buscar . '%')
                  ->orWhere('cif', 'like', '%' . $buscar . '%')
                  ->orWhere('identifier', 'like', '%' . $buscar . '%')
                  ->orWhere('activity', 'like', '%' . $buscar . '%');
            });
        }

        // Filtro por gestor
        if ($this->selectedGestor) {
            $query->where('admin_user_id', $this->selectedGestor);
        }

        // Ordenamiento
        $query->orderBy($this->sortColumn, $this->sortDirection);

        // Ejecutar consulta
        $this->clients = $this->perPage === 'all'
            ? $query->get()
            : $query->paginate(is_numeric($this->perPage) ? $this->perPage : 10);
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
        if ($propertyName === 'buscar' || $propertyName === 'selectedGestor') {
            $this->resetPage();
        }
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }
}
