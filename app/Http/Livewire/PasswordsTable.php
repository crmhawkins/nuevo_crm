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
        // Comprueba si se ha seleccionado "Todos" para la paginación
        if ($this->perPage === 'all') {
            $this->passwords = CompanyPassword::when($this->buscar, function ($query) {
                    $query->where('website', 'like', '%' . $this->buscar . '%');
                })
                ->when($this->selectedCliente, function ($query) {
                    $query->where('client_id', $this->selectedCliente);
                })
                ->get(); // Obtiene todos los registros sin paginación
        } else {
            // dd($this->perPage);
            // Usa paginación con la cantidad especificada por $this->perPage
            $this->passwords = CompanyPassword::when($this->buscar, function ($query) {
                    $query->where('website', 'like', '%' . $this->buscar . '%');
                })
                ->when($this->selectedCliente, function ($query) {
                    $query->where('client_id', $this->selectedCliente);
                })
                ->paginate(is_numeric($this->perPage) ? $this->perPage : 10); // Se asegura de que $this->perPage sea numérico
        }
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
