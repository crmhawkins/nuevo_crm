<?php

namespace App\Models\Events;

use App\Models\Budgets\Budget;
use App\Models\Clients\Client;
use App\Models\Projects\Project;
use App\Models\Tasks\Task;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'client_id',
        'descripcion',
        'color',
        'admin_user_id',
        'budget_id',
        'project_id',
        'task_id',
        'start',
        'end',
    ];

    /**
     * Mutaciones de fecha.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
    ];


        /**
     * Obtener el usuario
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class,'admin_user_id');
    }
    public function cliente()
    {
        return $this->belongsTo(Client::class,'client_id');
    }
    public function presupuesto()
    {
        return $this->belongsTo(Budget::class,'budget_id');
    }
    public function proyecto()
    {
        return $this->belongsTo(Project::class,'project_id');
    }
    public function tarea()
    {
        return $this->belongsTo(Task::class,'task_id');
    }
    public function nonNullAttributes()
{
    $attributes = $this->attributesToArray();

    // Modificar el atributo 'client_id' para devolver el nombre del cliente en lugar del ID
    if (isset($attributes['client_id']) && $attributes['client_id'] !== null) {
        $attributes['cliente_name'] = $this->cliente ? $this->cliente->company ?? $this->cliente->name : 'No disponible';
    }
    if (isset($attributes['budget_id']) && $attributes['budget_id'] !== null) {
        $attributes['presupuesto_ref'] = $this->presupuesto ? $this->presupuesto->reference : 'No disponible';
        $attributes['presupuesto_conp'] = $this->presupuesto ? $this->presupuesto->concept : 'No disponible';
    }
    if (isset($attributes['project_id']) && $attributes['project_id'] !== null) {
        $attributes['proyecto_name'] = $this->proyecto ? $this->proyecto->name : 'No disponible';
    }

    // Filtrar los atributos para que devuelva solo aquellos que no sean null
    return array_filter($attributes, function($value) {
        return $value !== null;
    });
}
}
