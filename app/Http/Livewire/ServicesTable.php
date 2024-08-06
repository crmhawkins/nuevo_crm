<?php

namespace App\Http\Livewire;

use App\Models\Services\Service;
use App\Models\Services\ServiceCategories;
use Livewire\Component;
use Livewire\WithPagination;

class ServicesTable extends Component
{
    use WithPagination;

    public $categorias;
    public $buscar;
    public $selectedCategoria = '';
    public $perPage = 10;
    protected $services; // Propiedad protegida para los usuarios

    public function mount(){
        $this->categorias = ServiceCategories::all();
        // $this->actualizarServicios(); // Inicializa los usuarios
    }
    public function render()
    {
        $this->actualizarServicios(); // Ahora se llama directamente en render para refrescar los clientes.
        return view('livewire.services-table', [
            'servicios' => $this->services
        ]);
    }

    protected function actualizarServicios()
    {
        // Comprueba si se ha seleccionado "Todos" para la paginación
        if ($this->perPage === 'all') {
            $this->services = Service::when($this->buscar, function ($query) {
                    $query->where('title', 'like', '%' . $this->buscar . '%')
                          ->orWhere('concept', 'like', '%' . $this->buscar . '%')
                          ->orWhere('price', 'like', '%' . $this->buscar . '%')
                          ->orWhere('estado', 'like', '%' . $this->buscar . '%')
                          ->orWhere('order', 'like', '%' . $this->buscar . '%');
                })
                ->when($this->selectedCategoria, function ($query) {
                    $query->where('services_categories_id', $this->selectedCategoria);
                })
                ->get(); // Obtiene todos los registros sin paginación
        } else {
            // Usa paginación con la cantidad especificada por $this->perPage
            $this->services = Service::when($this->buscar, function ($query) {
                    $query->where('title', 'like', '%' . $this->buscar . '%')
                          ->orWhere('concept', 'like', '%' . $this->buscar . '%')
                          ->orWhere('price', 'like', '%' . $this->buscar . '%')
                          ->orWhere('estado', 'like', '%' . $this->buscar . '%')
                          ->orWhere('order', 'like', '%' . $this->buscar . '%');
                })
                ->when($this->selectedCategoria, function ($query) {
                    $query->where('services_categories_id', $this->selectedCategoria);
                })
                ->paginate(is_numeric($this->perPage) ? $this->perPage : 10); // Se asegura de que $this->perPage sea numérico
        }
    }

    public function getServices()
    {
        // Si es necesario, puedes incluir lógica adicional aquí antes de devolver los usuarios
        return $this->services;
    }

    public function aplicarFiltro()
    {
        // Aquí aplicarías los filtros
        // Por ejemplo: $this->filtroEspecifico = 'valor';

        $this->actualizarServicios(); // Luego actualizas la lista de usuarios basada en los filtros
    }

    public function updating($propertyName)
    {
        if ($propertyName === 'buscar' || $propertyName === 'selectedCategoria') {
            $this->resetPage(); // Resetear la paginación solo cuando estos filtros cambien.
        }
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }
}
