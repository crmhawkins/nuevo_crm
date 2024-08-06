<?php

namespace App\Models\Alerts;

use Illuminate\Database\Eloquent\Model;
use Hawkins\Contracts\Acl\AclGroup;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;


class AlertStatus extends Model
{

    protected $table = 'alert_status';
    public $timestamps = false;

    /**
     * Atributos asignados en masa.
     *
     * @var array
     */
    protected $fillable = [
        'status',
    ];

      /**
     * ESTADO: Pendiente
     *
     * @var string
     */
    const ALERT_STATUS_PENDING = 1;
    /**
     * ESTADO: Resuelta
     *
     * @var string
     */
    const ALERT_STATUS_SOLVED = 2;


}


