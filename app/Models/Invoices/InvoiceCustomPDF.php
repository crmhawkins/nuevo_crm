<?php

namespace App\Models\Budgets;

use Illuminate\Database\Eloquent\Model;
use App\Models\Budgets\BudgetCustomPDFTerms;

class InvoiceCustomPDF extends Model
{

    protected $table = 'custom_pdf_invoice';

    /**
     * Atributos asignados en masa.
     *
     * @var array
     */
    protected $fillable = [
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


}
