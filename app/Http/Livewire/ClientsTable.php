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
    public $soloClientes = false; // false = clientes, true = leads
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
        // Determinar el valor de is_client según el filtro
        // soloClientes = false -> mostrar clientes (is_client = 1)
        // soloClientes = true -> mostrar leads (is_client = 0)
        $isClientValue = $this->soloClientes ? 0 : 1;
        
        $query = Client::query()
            ->where('is_client', $isClientValue)
            ->when($this->buscar, function ($query) {
                $query->where(function($q) {
                    $q->where('name', 'like', '%' . $this->buscar . '%')
                      ->orWhere('email', 'like', '%' . $this->buscar . '%')
                      ->orWhere('company', 'like', '%' . $this->buscar . '%')
                      ->orWhere('cif', 'like', '%' . $this->buscar . '%')
                      ->orWhere('identifier', 'like', '%' . $this->buscar . '%')
                      ->orWhere('activity', 'like', '%' . $this->buscar . '%');
                });
            })
            ->when($this->selectedGestor, function ($query) {
                $query->where('admin_user_id', $this->selectedGestor);
            });

            $query->orderBy($this->sortColumn, $this->sortDirection);

            // Verifica si se seleccionó 'all' para mostrar todos los registros
            $this->clients = $this->perPage === 'all' ? $query->get() : $query->paginate(is_numeric($this->perPage) ? $this->perPage : 10);
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
        if (in_array($propertyName, ['buscar', 'selectedGestor', 'soloClientes'])) {
            $this->resetPage();
        }
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }
}
