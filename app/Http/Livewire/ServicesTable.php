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
    public $sortColumn = 'created_at'; // Columna por defecto
    public $sortDirection = 'desc'; // Dirección por defecto
    protected $services; // Propiedad protegida para los usuarios

    public function mount(){
        $this->categorias = ServiceCategories::where('inactive',0)->get();
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
        $query = Service::where('services.inactive',0)->when($this->buscar, function ($query) {
                    $query->where('title', 'like', '%' . $this->buscar . '%')
                          ->orWhere('concept', 'like', '%' . $this->buscar . '%')
                          ->orWhere('price', 'like', '%' . $this->buscar . '%')
                          ->orWhere('estado', 'like', '%' . $this->buscar . '%')
                          ->orWhere('order', 'like', '%' . $this->buscar . '%');
                })
                ->when($this->selectedCategoria, function ($query) {
                    $query->where('services_categories_id', $this->selectedCategoria);
                }) ->join('services_categories', 'services.services_categories_id', '=', 'services_categories.id')
                ->select('services.*', 'services_categories.name as categoria_nombre');

        $query->orderBy($this->sortColumn, $this->sortDirection);

        // Verifica si se seleccionó 'all' para mostrar todos los registros
        $this->services = $this->perPage === 'all' ? $query->get() : $query->paginate(is_numeric($this->perPage) ? $this->perPage : 10);
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
        if ($propertyName === 'buscar' || $propertyName === 'selectedCategoria') {
            $this->resetPage(); // Resetear la paginación solo cuando estos filtros cambien.
        }
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }
}
