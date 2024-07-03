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
    public $sortColumn = 'name'; // Columna por defecto
    public $sortDirection = 'asc'; // Dirección por defecto

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
        $query = Client::where('is_client', 1)
            ->when($this->buscar, function ($query) {
                $query->where('name', 'like', '%' . $this->buscar . '%')
                      ->orWhere('email', 'like', '%' . $this->buscar . '%')
                      ->orWhere('cif', 'like', '%' . $this->buscar . '%')
                      ->orWhere('identifier', 'like', '%' . $this->buscar . '%')
                      ->orWhere('activity', 'like', '%' . $this->buscar . '%');
            })
            ->when($this->selectedGestor, function ($query) {
                $query->where('admin_user_id', $this->selectedGestor);
            });

        // Aplica la ordenación
        $query->orderBy($this->sortColumn, $this->sortDirection);

        if ($this->perPage === 'all') {
            $this->clients = $query->get();
        } else {
            $this->clients = $query->paginate(is_numeric($this->perPage) ? $this->perPage : 10);
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
