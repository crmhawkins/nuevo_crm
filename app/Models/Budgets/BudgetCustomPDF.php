<?php

namespace App\Models\Budgets;

use Illuminate\Database\Eloquent\Model;
use App\Models\Budgets\BudgetCustomPDFTerms;

class BudgetCustomPDF extends Model
{

    protected $table = 'custom_pdf_budget';

    /**
     * Atributos asignados en masa.
     *
     * @var array
     */
    protected $fillable = [
        'term_condition_id',
        'company_name',
        'head_string_1',
        'head_string_2',
        'head_string_3',
        'nif',
        'logo_image',
        'footer_string_1',
        'footer_string_2',
        'terms',
    ];

    /**
     * Mutaciones de fecha.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at',
    ];

    /**
     * Obtener la categoria del servicio
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function getTermCondition()
    {
        return $this->belongsTo(BudgetCustomPDFTerms::class,'custom_pdf_budget_terms');
    }

}
