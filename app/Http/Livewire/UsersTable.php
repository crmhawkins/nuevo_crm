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
    public $sortColumn = 'created_at'; // Columna por defecto
    public $sortDirection = 'desc'; // Dirección por defecto
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
                $query->where('admin_user.name', 'like', '%' . $this->buscar . '%')
                    ->orWhere('admin_user.email', 'like', '%' . $this->buscar . '%');
            })
            ->when($this->selectedDepartamento, function ($query) {
                $query->where('admin_user.admin_user_department_id', $this->selectedDepartamento);
            })
            ->when($this->selectedNivel, function ($query) {
                $query->where('admin_user.access_level_id', $this->selectedNivel);
            })
            ->join('admin_user_access_level', 'admin_user.access_level_id', '=', 'admin_user_access_level.id')
            ->join('admin_user_department', 'admin_user.admin_user_department_id', '=', 'admin_user_department.id')
            ->join('admin_user_position', 'admin_user.admin_user_position_id', '=', 'admin_user_position.id')
            ->select('admin_user.*', 'admin_user_position.name as cargo', 'admin_user_department.name as departamento','admin_user_access_level.name as acceso');

            $query->orderBy($this->sortColumn, $this->sortDirection);

            // Verifica si se seleccionó 'all' para mostrar todos los registros
            $this->users = $this->perPage === 'all' ? $query->get() : $query->paginate(is_numeric($this->perPage) ? $this->perPage : 10);
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
