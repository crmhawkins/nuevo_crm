<?php

namespace App\Http\Livewire;

use App\Models\Nominas\Nomina;
use App\Models\Users\User;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;

class NominasUserTable extends Component
{
    use WithPagination;

    public $identificador;
    public $buscar;
    public $selectedUser;
    public $selectedAnio;
    public $selectedMes;
    public $usuarios;
    public $perPage = 10;
    public $sortColumn = 'created_at'; // Columna por defecto
    public $sortDirection = 'desc'; // Dirección por defecto
    protected $nominas; // Propiedad protegida para los usuarios

    public function mount(){
        $this->usuarios = User::all();
    }


    public function render()
    {
        $this->actualizarNominas(); // Ahora se llama directamente en render para refrescar los clientes.
        return view('livewire.nominas-user-table', [
            'nominas' => $this->nominas
        ]);
    }

    protected function actualizarNominas()
    {
        // Comprueba si se ha seleccionado "Todos" para la paginación
        $query = Nomina::where('admin_user_id',$this->identificador)
        ->when($this->buscar, function ($query) {
            $query->where('name', 'like', '%' . $this->buscar . '%');
        })
        ->when($this->selectedUser, function ($query) {
            $query->where('admin_user_id', $this->selectedUser);
        })
        ->when($this->selectedAnio, function ($query) {
            $query->whereYear('fecha', $this->selectedAnio);
        })
        ->when($this->selectedMes, function ($query) {
            $query->whereMonth('fecha', $this->selectedMes);
        });

        $query->orderBy($this->sortColumn, $this->sortDirection);

        // Verifica si se seleccionó 'all' para mostrar todos los registros
        $this->nominas = $this->perPage === 'all' ? $query->get() : $query->paginate(is_numeric($this->perPage) ? $this->perPage : 10);
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
        if ($propertyName === 'buscar' || $propertyName === 'selectedAnio' || $propertyName === 'selectedMes') {
            $this->resetPage(); // Resetear la paginación solo cuando estos filtros cambien.
        }
    }

    public function downloadNomina($nominaId) {
        $nomina = Nomina::findOrFail($nominaId);

        if ($nomina->archivo && Storage::disk('public')->exists($nomina->archivo)) {
            $path = storage_path('app/public/' . $nomina->archivo);
            return response()->download($path);
        } else {
            session()->flash('error', 'El archivo no existe.');
            return redirect()->back();
        }
    }

}
