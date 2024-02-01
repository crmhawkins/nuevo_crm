<?php

namespace App\Actions;

use LaravelViews\Actions\Action;
use LaravelViews\Views\View;
use LaravelViews\Actions\Confirmable;


class DeleteProjectAction extends Action
{
    /**
     * Any title you want to be displayed
     * @var String
     * */
    public $title = "Borrar Campaña";

    use Confirmable;


    /**
     * This should be a valid Feather icon string
     * @var String
     */
    public $icon = "trash-2";

    /**
     * Execute the action when the user clicked on the button
     *
     * @param $model Model object of the list where the user has clicked
     * @param $view Current view where the action was executed from
     */
    public function handle($model, View $view)
    {
        // $model->inactive = true;
        $model->delete();
        // session()->flash('toast', [
        //     'icon' => 'success',
        //     'mensaje' => 'El usuario fue borrado correctamente'
        // ]);
        $this->success('ELa campaña fue borrada correctamente');

    }

    public function getConfirmationMessage($item = null)
{
    return '¿Esta seguro que deseas borrar la campaña?';
}
}
