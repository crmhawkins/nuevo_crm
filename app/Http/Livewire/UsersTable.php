<?php

namespace App\Http\Livewire;

use App\Models\Users\User;
use App\Models\Users\UserAccessLevel;
use App\Models\Users\UserDepartament;
use Livewire\Component;
use Livewire\WithPagination;

class UsersTable extends Component
{
    use WithPagination;

    public $departamentos;
    public $niveles;
    public $buscar;
    public $selectedDepartamento = '';
    public $selectedNivel = '';
    public $perPage = 10;

    protected $users; // Propiedad protegida para los usuarios

    public function mount(){
        $this->departamentos = UserDepartament::all();
        $this->niveles = UserAccessLevel::all();
    }

    public function render()
    {

        $this->actualizarUsuarios(); // Inicializa los usuarios
        return view('livewire.users-table', [
            'users' => $this->getUsers() // Utiliza un método para obtener los usuarios
        ]);
    }

    protected function actualizarUsuarios()
    {
        $query = User::where('inactive', 0)
            ->when($this->buscar, function ($query) {
                $query->where('name', 'like', '%' . $this->buscar . '%')
                    ->orWhere('email', 'like', '%' . $this->buscar . '%');
            })
            ->when($this->selectedDepartamento, function ($query) {
                $query->where('admin_user_department_id', $this->selectedDepartamento);
            })
            ->when($this->selectedNivel, function ($query) {
                $query->where('access_level_id', $this->selectedNivel);
            });

        // Verifica si se seleccionó 'all' para mostrar todos los registros
        $this->users = $this->perPage === 'all' ? $query->get() : $query->paginate($this->perPage);
    }

    public function getUsers()
    {
        // Si es necesario, puedes incluir lógica adicional aquí antes de devolver los usuarios
        return $this->users;
    }

    // Supongamos que tienes un método para actualizar los filtros
    public function aplicarFiltro()
    {
        // Aquí aplicarías los filtros
        // Por ejemplo: $this->filtroEspecifico = 'valor';

        $this->actualizarUsuarios(); // Luego actualizas la lista de usuarios basada en los filtros
    }

    public function updating($propertyName)
    {
        if ($propertyName === 'buscar' || $propertyName === 'selectedDepartamento' || $propertyName === 'selectedNivel' || $propertyName === 'perPage') {
            if($propertyName !== 'perPage' || $this->perPage !== 'all') {
                $this->resetPage();
            }
            // $this->actualizarUsuarios();
        }

    }
    public function updatingPerPage()
    {
        $this->resetPage();
    }
}
