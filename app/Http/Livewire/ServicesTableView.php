<?php

namespace App\Http\Livewire;

use App\Actions\DeleteServiceAction;
use App\Models\Services\Service;
use LaravelViews\Views\TableView;
use Illuminate\Support\Str;
use LaravelViews\Actions\RedirectAction;
use LaravelViews\Facades\Header;
use Illuminate\Database\Eloquent\Builder;

class ServicesTableView extends TableView
{
    /**
     * Sets a model class to get the initial data
     */
    protected $model = Service::class;

    public $searchBy = ['title', 'concept', 'serviceCategoria.name', 'price'];

    protected $paginate = 10;
    public $sortField = 'created_at'; // campo predeterminado para la ordenación
    public $sortDirection = 'asc'; // dirección predeterminada para la ordenación

    /**
     * Sets the headers of the table as you want to be displayed
     *
     * @return array<string> Array of headers
     */

    public function headers(): array
    {
        return [
            Header::title('Titulo')->sortBy('title'),
            Header::title('Categoria')->sortBy('services_categories.name'), // Mantén esta referencia
            'Precio',
        ];
    }
    protected function query(): Builder
    {
        $query = parent::query();

        // Realizar una unión con la tabla de categorías
        $query->leftJoin('service_categories', 'services.services_categories_id', '=', 'service_categories.id');

        if ($this->sortField === 'serviceCategoria.name') {
            // Ordenar por el nombre de la categoría
            $query->orderBy('service_categories.name', $this->sortDirection);
        }

        return $query;
    }


    /**
     * Sets the data to every cell of a single row
     *
     * @param $model Current model for each row
     */
    public function row($model): array
    {
        return [
            $model->title,
            // $model->serviceCategoria->name === null ? $model->serviceCategoria->name : 'Sin Categoría',
            'Categoria' => $model->servicoNombre?->name ?? 'Sin Categoría',
            // 'Categoria' => $model->calcularSumaPresupuestos() ?? 'Sin Categoría',
            $model->price,
        ];
    }

    protected function actionsByRow()
    {
        return [
            new RedirectAction('servicios.show','Ver Servicio', 'eye'),
            new RedirectAction('servicios.edit','Editar Servicio', 'edit'),
            new DeleteServiceAction
        ];
    }

    protected function applySorting(Builder $query, $field, $direction)
    {
        if ($field === 'serviceCategoria.name') {
            // Subconsulta para obtener el nombre de la categoría
            $query->leftJoin('service_categories', 'services.services_categories_id', '=', 'service_categories.id')
                ->orderBy(function ($query) use ($direction) {
                    $query->select('name')
                        ->from('service_categories')
                        ->whereColumn('services.services_categories_id', 'service_categories.id')
                        ->limit(1);
                }, $direction);
        } else {
            // Para otros campos, utiliza la ordenación estándar
            parent::applySorting($query, $field, $direction);
        }
    }

}
