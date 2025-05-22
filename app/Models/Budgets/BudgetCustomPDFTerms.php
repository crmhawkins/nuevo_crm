<?php

namespace App\Models\Budgets;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class BudgetCustomPDFTerms extends Model
{

    protected $table = 'custom_pdf_budget_terms';
    public $timestamps = false;

    /**
     * Atributos asignados en masa.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    // PARA LOS ID
    /**
     * ESTADO: Pendiente de confirmar
     *
     * @var string
     */
    const PENDING_CONFIRMATION = 1;

    /**
     * ESTADO: Pendiente de aceptar
     *
     * @var string
     */
    const PENDING_ACCEPT = 2;

    /**
     * ESTADO: Aceptado
     *
     * @var string
     */
    const ACCEPTED = 3;

    /**
     * ESTADO: Cancelado
     *
     * @var string
     */
    const CANCELLED = 4;

    /**
     * ESTADO: Finalizado
     *
     * @var string
     */
    const FINISHED = 5;

    /**
     * ESTADO: Facturado
     *
     * @var string
     */
    const INVOICED = 6;

    /**
     * ESTADO: Facturado parcialmente
     *
     * @var string
     */
    const INVOICED_PARTIALLY = 7;

}
