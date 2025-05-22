<?php

namespace App\Http\Livewire;

use App\Actions\DeleteClientAction;
use App\Filters\BudgetsAnioFilter;
use App\Filters\BudgetsEstadosFilter;
use App\Filters\BudgetsGestorFilter;
use App\Models\Budgets\Budget;
use Illuminate\Contracts\Database\Eloquent\Builder;
use LaravelViews\Actions\RedirectAction;
use LaravelViews\Views\TableView;

class BudgetsTableView extends TableView
{
    /**
     * Sets a model class to get the initial data
     */
    protected $model = Budget::class;

    public $searchBy = ['reference', 'concept', 'creation_date', 'description', 'total'];

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
            'Marca',
            'Actividad',
            'Fecha Creacion',
            'Empresa',
            'Email',
            'Gestor',
            'Acciones'
        ];
    }

    public $filterYear;


    public function row($model): array
    {
        return [
            // UI::avatar($model->image ? 'http://127.0.0.1:8000/storage/avatars/'.$model->image : 'http://127.0.0.1:8000/assets/images/guest.webp'),
            $model->reference,
            $model->budget_status_id,
            $model->concept,
            $model->creation_date,
            $model->description,
            $model->total,
            $model->usuario->name,
        ];
    }

    protected function actionsByRow()
    {
        return [
            new RedirectAction('presupuesto.show','Ver Presupuesto', 'eye'),
            new RedirectAction('presupuesto.edit','Editar Presupuesto', 'edit'),
            new DeleteClientAction
        ];
    }

    protected function filters()
    {
        return [
            new BudgetsGestorFilter,
            new BudgetsEstadosFilter,
            new BudgetsAnioFilter
        ];

    }
}
