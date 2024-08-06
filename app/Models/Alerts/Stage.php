<?php

namespace App\Models\Alerts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;


class Stage extends Model
{

    protected $table = 'stages';
    public $timestamps = false;

    /**
     * Atributos asignados en masa.
     *
     * @var array
     */
    protected $fillable = [
        'stage',
    ];

      /**
     * ETAPA: Peticion Creada
     *
     * @var string
     */
    const STAGE_CREATED_PETITION = "Peticion Creada";
    /**
     * ETAPA: Presupuesto Creado
     *
     * @var string
     */
    const STAGE_CREATED_BUDGET = "Presupuesto Creado";
    /**
     * ETAPA: Factura Generada
     *
     * @var string
     */
    const STAGE_GENERATED_INVOICE = "Factura Generada";
    /**
     * ETAPA: Factura Parcial Generada
     *
     * @var string
     */
    const STAGE_GENERATED_PARTIAL_INVOICE = "Factura Parcial Generada";


}


