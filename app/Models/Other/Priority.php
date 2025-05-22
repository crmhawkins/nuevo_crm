<?php

namespace App\Models\Other;

use Illuminate\Database\Eloquent\Model;
use Hawkins\Contracts\Acl\AclGroup;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;


class Priority extends Model
{

    protected $table = 'priority';
    public $timestamps = false;
    /**
     * Atributos asignados en masa.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

     /**
     * PRIORIDAD: Baja
     * 
     * @var int
     */
    const PRIORITY_LOW = "Baja";
    /**
     * PRIORIDAD: Media
     *
     * @var int
     */
    const PRIORITY_MEDIUM = "Media";
    /**
     * PRIORIDAD: Alta
     *
     * @var int
     */
    const PRIORITY_HIGH = "Alta";
    /**
     * PRIORIDAD: Urgente
     *
     * @var int
     */
    const PRIORITY_URGENT = "Urgente";

}