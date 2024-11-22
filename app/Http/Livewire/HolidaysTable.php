<?php

namespace App\Http\Livewire;

use App\Models\Accounting\Gasto;
use App\Models\Clients\Client;
use App\Models\Holidays\Holidays;
use App\Models\Holidays\HolidaysPetitions;
use Livewire\Component;
use Livewire\WithPagination;

class HolidaysTable extends Component
{
    use WithPagination;

    public $buscar;
    public $selectedCliente = '';
    public $selectedEstado;
    public $clientes;
    public $estados;
    public $perPage = 10;
    public $numberOfholidaysPetitions;
    public $holydayEvents;
    public $sortColumn = 'created_at'; // Columna por defecto
    public $sortDirection = 'desc'; // Dirección por defecto
    protected $holidays; // Propiedad protegida para los gastosbusqueda


    public function mount(){
        $this->holydayEvents = [];
        $data = HolidaysPetitions::orderBy('created_at', 'asc')->get();

        if ($data->count()) {
            foreach ($data as $value) {
                $color = '#FFFFFF'; // Color por defecto

                // Asignar color según el estado
                if ($value->holidays_status_id == 1) {
                    $color = '#C3EBC4'; // Color para estado 1
                } elseif ($value->holidays_status_id == 2) {
                    $color = '#FBC4C4'; // Color para estado 2
                } elseif ($value->holidays_status_id == 3) {
                    $color = '#FFDD9E'; // Color para estado 3
                }

                // Verificar si el usuario está asociado con la petición
                if ($value->adminUser) {
                    $this->holydayEvents[] = [
                        'title' => $value->adminUser->name, // Título del evento
                        'start' => (new \DateTime($value->from))->format('Y-m-d'), // Fecha de inicio
                        'end' => (new \DateTime($value->to . ' +1 day'))->format('Y-m-d'), // Fecha de fin
                        'color' => $color, // Color del evento
                        'url' => url('/holidays/managePetition/' . $value->id) // URL del evento
                    ];
                }
            }
        }
    }
    public function render()
    {
        $this->actualizargastos(); // Ahora se llama directamente en render para refrescar los gastos.
        return view('livewire.holidays-table', [
            'holidays' => $this->holidays
        ]);
    }

    protected function actualizargastos()
    {
        // Comprueba si se ha seleccionado "Todos" para la paginación
        $query = Holidays::whereHas('adminUser', function ($query) {
            $query->where('inactive', 0); // Filtra usuarios que no están inactivos
        })
        ->when($this->buscar, function ($query) {
            $query->whereHas('adminUser', function ($query) {
                $query->where('name', 'like', '%' . $this->buscar . '%');
            });
        }); // Obtiene todos los registros sin paginación

         // Aplica la ordenación
         $query->orderBy($this->sortColumn, $this->sortDirection);

         // Verifica si se seleccionó 'all' para mostrar todos los registros
         $this->holidays = $this->perPage === 'all' ? $query->get() : $query->paginate(is_numeric($this->perPage) ? $this->perPage : 10);
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
        if ($propertyName === 'buscar' || $propertyName === 'selectedCliente' || $propertyName === 'selectedEstado') {
            $this->resetPage(); // Resetear la paginación solo cuando estos filtros cambien.
        }
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }



}
