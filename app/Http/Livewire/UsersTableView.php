<?php

namespace App\Http\Livewire;

use App\Actions\DeleteUserAction;
use App\Filters\UsersActiveFilter;
use App\Filters\UsersCargoFilter;
use App\Filters\UsersDepartamentoFilter;
use App\Filters\UsersInactiveFilter;
use App\Models\Users\User;
use LaravelViews\Views\TableView;
use Illuminate\Database\Eloquent\Builder;

use LaravelViews\Facades\UI;
use LaravelViews\Actions\RedirectAction;

class UsersTableView extends TableView
{
    /**
     * Sets a model class to get the initial data
     */
    protected $model = User::class;

    public $searchBy = ['name', 'posicion.name', 'acceso.name', 'email', 'departamento.name'];

    protected $paginate = 10;

    public function headers(): array
    {
        return [
            'Avatar',
            'Nombre',
            'Email',
            'Nivel de Acceso',
            'Departamento',
            'Cargo',
            'Activo',
            'Acciones'
        ];
    }

    public function row($model)
    {
            return [
                UI::avatar($model->image ? 'http://127.0.0.1:8000/storage/avatars/'.$model->image : 'http://127.0.0.1:8000/assets/images/guest.webp'),
                $model->name,
                $model->email,
                $model->acceso->name,
                $model->departamento->name,
                $model->posicion->name,
                $model->inactive = $model->inactive == 0 ? 'Activo': 'Inactivo',
            ];

    }

    /** For actions by item */
    protected function actionsByRow()
    {
        return [
            new RedirectAction('user.show','Ver Usuario', 'eye'),
            new RedirectAction('user.edit','Editar Usuario', 'edit'),
            new DeleteUserAction
        ];
    }
    protected function filters()
    {
        return [
            new UsersActiveFilter,
            new UsersInactiveFilter,
            new UsersDepartamentoFilter,
            new UsersCargoFilter

        ];

    }

}
