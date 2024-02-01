<?php

namespace App\Http\Livewire;

use App\Actions\DeleteClientAction;
use App\Actions\DeleteUserAction;
use App\Filters\ClientsGestorFilter;
use App\Models\Clients\Client;
use LaravelViews\Actions\RedirectAction;
use LaravelViews\Views\TableView;

class ClientsTableView extends TableView
{
    /**
     * Sets a model class to get the initial data
     */
    protected $model = Client::class;

    public $searchBy = ['name', 'cif', 'identifier', 'email', 'activity', 'company', 'phone', 'web'];

    protected $paginate = 10;


    /**
     * Sets the headers of the table as you want to be displayed
     *
     * @return array<string> Array of headers
     */
    public function headers(): array
    {
        return [
            'Nombre',
            'Cif',
            'Marca',
            'Actividad',
            'Empresa',
            'Email',
            'Telefono',
            'Web',
            'Gestor',
            'Acciones'
        ];
    }

    /**
     * Sets the data to every cell of a single row
     *
     * @param $model Current model for each row
     */
    public function row($model): array
    {
        return [
            // UI::avatar($model->image ? 'http://127.0.0.1:8000/storage/avatars/'.$model->image : 'http://127.0.0.1:8000/assets/images/guest.webp'),
            $model->name,
            $model->cif,
            $model->identifier,
            $model->activity,
            $model->company,
            $model->email,
            $model->phone,
            $model->web,
            $model->gestor->name,
            $model->inactive = $model->inactive == 0 ? 'Activo': 'Inactivo',
        ];
    }

    /** For actions by item */
    protected function actionsByRow()
    {
        return [
            new RedirectAction('cliente.show','Ver Cliente', 'eye'),
            new RedirectAction('cliente.edit','Editar Cliente', 'edit'),
            new DeleteClientAction
        ];
    }

    protected function filters()
    {
        return [
            new ClientsGestorFilter,

        ];

    }
}
