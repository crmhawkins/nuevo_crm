<?php

namespace App\Http\Livewire;

use App\Models\Services\ServiceCategories;
use Livewire\Component;
use Livewire\WithPagination;

class ServicesCategoriesTable extends Component
{
    use WithPagination;

    public $buscar;
    public $selectedCategoria = '';
    public $perPage = 10;

    protected $categorias; // Propiedad protegida para los usuarios
    public function render()
    {
        $this->actualizarServiciosCategoria(); // Ahora se llama directamente en render para refrescar los clientes.
        return view('livewire.services-categories-table', [
            'categorias' => $this->categorias
        ]);
    }

    protected function actualizarServiciosCategoria()
    {
        // Comprueba si se ha seleccionado "Todos" para la paginación
        if ($this->perPage === 'all') {
            $this->categorias = ServiceCategories::when($this->buscar, function ($query) {
                    $query->where('name', 'like', '%' . $this->buscar . '%');
                })
                ->get(); // Obtiene todos los registros sin paginación
        } else {
            // Usa paginación con la cantidad especificada por $this->perPage
            $this->categorias = ServiceCategories::when($this->buscar, function ($query) {
                    $query->where('name', 'like', '%' . $this->buscar . '%');
                })
                ->paginate(is_numeric($this->perPage) ? $this->perPage : 10); // Se asegura de que $this->perPage sea numérico
        }
    }

    public function getCategorias()
    {
        // Si es necesario, puedes incluir lógica adicional aquí antes de devolver los usuarios
        return $this->categorias;
    }

    public function aplicarFiltro()
    {
        // Aquí aplicarías los filtros
        // Por ejemplo: $this->filtroEspecifico = 'valor';

        $this->actualizarServiciosCategoria(); // Luego actualizas la lista de usuarios basada en los filtros
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
