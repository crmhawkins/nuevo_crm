<?php

namespace App\Http\Livewire;

use App\Actions\DeleteProjectAction;
use App\Filters\ProjectsGestorFilter;
use App\Models\Projects\Project;
use LaravelViews\Actions\RedirectAction;
use LaravelViews\Views\TableView;

class ProjectsTableView extends TableView
{

    /**
     * Sets a model class to get the initial data
     */
    protected $model = Project::class;

    public $searchBy = ['name', 'cliente.name', 'usuario.name', 'description'];

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
            'Cliente',
            'Gestor',
            'Descripcion'
        ];
    }

    public function row($model): array
    {
        return [
            $model->name,
            $model->cliente->name,
            $model->usuario->name,
            $model->description,
        ];
    }

    protected function actionsByRow()
    {
        return [
            new RedirectAction('campania.show','Ver Campaña', 'eye'),
            new RedirectAction('campania.edit','Editar Campaña', 'edit'),
            new DeleteProjectAction
        ];
    }

    protected function filters()
    {
        return [ new ProjectsGestorFilter ];

    }
}
