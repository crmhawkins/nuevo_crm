<?php

namespace App\Models\Alerts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Alerts\AlertStatus;
use App\Models\Alerts\Stage;
use App\Models\Users\User;

class Alert extends Model
{
    use SoftDeletes;

    protected $table = 'alerts';

    /**
     * Atributos asignados en masa.
     *
     * @var array
     */
    protected $fillable = [
        'admin_user_id',
        'stage_id',
        'activation_datetime',
        'status_id',
        'reference_id',
        'cont_postpone',
        'description',
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
    public function adminUser()
    {
        return $this->belongsTo(User::class,'admin_user_id');
    }
    /**
     * Obtener el estado del la alerta
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */

    public function alertStatus()
    {
        return $this->belongsTo(AlertStatus::class, 'status_id');
    }
    /**
     * Obtener la etapa del la alerta
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */

    public function stage()
    {
        return $this->belongsTo(Stage::class, 'stage_id');
    }

}
