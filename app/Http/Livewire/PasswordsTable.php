<?php

namespace App\Http\Livewire;


use App\Models\Clients\Client;
use App\Models\Passwords\CompanyPassword;
use Livewire\Component;
use Livewire\WithPagination;

class PasswordsTable extends Component
{
    use WithPagination;

    public $buscar;
    public $selectedCliente = '';
    public $selectedEstado;
    public $clientes;
    public $estados;
    public $perPage = 10;
    public $sortColumn = 'created_at'; // Columna por defecto
    public $sortDirection = 'desc'; // Dirección por defecto
    protected $passwords; // Propiedad protegida para los usuarios

    public function mount(){
        $this->clientes = Client::all();
    }


    public function render()
    {
        $this->actualizarDominios(); // Ahora se llama directamente en render para refrescar los clientes.
        return view('livewire.passwords-table', [
            'passwords' => $this->passwords
        ]);
    }

    protected function actualizarDominios()
    {
        $query = CompanyPassword::when($this->buscar, function ($query) {
                    $query->where('website', 'like', '%' . $this->buscar . '%')
                        ->orWhere('user', 'like', '%' . $this->buscar . '%');
                })
                ->when($this->selectedCliente, function ($query) {
                    $query->where('client_id', $this->selectedCliente);
                }); // Obtiene todos los registros sin paginación

         // Aplica la ordenación
         $query->orderBy($this->sortColumn, $this->sortDirection);

         // Verifica si se seleccionó 'all' para mostrar todos los registros
         $this->passwords = $this->perPage === 'all' ? $query->get() : $query->paginate(is_numeric($this->perPage) ? $this->perPage : 10);
    }

    public function getCategorias()
    {
        // Si es necesario, puedes incluir lógica adicional aquí antes de devolver los usuarios
        return $this->passwords;
    }

    public function aplicarFiltro()
    {
        // Aquí aplicarías los filtros
        // Por ejemplo: $this->filtroEspecifico = 'valor';

        $this->actualizarDominios(); // Luego actualizas la lista de usuarios basada en los filtros
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
        if ($propertyName === 'buscar' || $propertyName === 'selectedCliente') {
            $this->resetPage(); // Resetear la paginación solo cuando estos filtros cambien.
        }
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }
}
