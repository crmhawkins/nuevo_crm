<?php

namespace App\Http\Livewire;

use App\Models\Clients\ClientIpoint;
use App\Models\Users\User;
use Livewire\Component;
use Livewire\WithPagination;

class ClientsIpointTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $gestores;
    public $buscar;
    public $selectedGestor = '';
    public $perPage = 10;
    public $sortColumn = 'created_at'; // Columna por defecto
    public $sortDirection = 'desc'; // Dirección por defecto
    protected $clients;

    public function mount(){
        $this->gestores = User::where('access_level_id', 4)->get();
        // Leer la página desde la query string si existe
        if (request()->has('ipoint_page')) {
            $this->paginators['ipoint_page'] = (int) request()->get('ipoint_page');
        }
    }

    public function render()
    {
        $this->actualizarClientes();
        return view('livewire.clients-ipoint-table', [
            'clients' => $this->clients
        ]);
    }

    protected function actualizarClientes()
    {
        // Base query: solo clientes (is_client = 1) - usar whereRaw para ser más explícito
        $query = ClientIpoint::whereRaw('is_client = 1');

        // Si hay búsqueda, agregar condiciones de búsqueda agrupadas
        if (!empty(trim($this->buscar))) {
            $buscar = trim($this->buscar);
            // Agrupar todas las condiciones de búsqueda para que respeten el filtro is_client = 1
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

        // Ejecutar consulta con paginación personalizada
        if ($this->perPage === 'all') {
            $this->clients = $query->get();
        } else {
            $page = $this->paginators['ipoint_page'] ?? 1;
            $this->clients = $query->paginate(is_numeric($this->perPage) ? $this->perPage : 10, ['*'], 'ipoint_page', $page);
        }
    }

    public function sortBy($column)
    {
        if ($this->sortColumn === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortColumn = $column;
            $this->sortDirection = 'asc';
        }
        $this->paginators['ipoint_page'] = 1;
    }

    public function updating($propertyName)
    {
        if ($propertyName === 'buscar' || $propertyName === 'selectedGestor') {
            $this->paginators['ipoint_page'] = 1;
        }
    }

    public function updatingPerPage()
    {
        $this->paginators['ipoint_page'] = 1;
    }

    public function refresh()
    {
        // Método público para refrescar el componente desde JavaScript
        // No hace nada, solo fuerza que Livewire re-renderice el componente
    }
}
