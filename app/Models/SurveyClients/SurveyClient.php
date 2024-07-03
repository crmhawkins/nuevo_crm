<?php

namespace App\Models\SurveyClients;

use App\Models\Budgets\Budget;
use App\Models\Clients\Client;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SurveyClient extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'survey_clients';

    /**
     * Atributos asignados en masa.
     *
     * @var array
     */
    protected $fillable = [
        'budget_id',
        'client_id',
        'quest1',
        'quest2',
        'quest3',
        'quest4',
        'quest5',
        'quest6',
        'quest7',
        'quest8',
        'quest3',
        'valoracion_final',
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
     * Obtener el empleado      
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function client()
    {
        return $this->belongsTo(Client::class,'client_id');
    }

    /**
     * Obtener el presupuesto
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function budget()
    {
        return $this->belongsTo(Budget::class,'budget_id');
    }
}
