<?php

namespace App\Models\Invoices;

use App\Models\Budgets\BudgetConceptType;
use App\Models\Services\Service;
use App\Models\Services\ServiceCategories;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceConcepts extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'invoice_concepts';

    /**
     * Atributos asignados en masa.
     *
     * @var array
     */
    protected $fillable = [
        'invoice_id',
        'concept_type_id',
        'service_id',
        'services_category_id',
        'title',
        'concept',
        'units',
        'purchase_price',
        'margin_percentage',
        'benefit_margin',
        'sale_price',
        'discount',
        'total',
        'total_no_discount',
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
     * Obtener la facrtura
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class,'invoice_id');
    }

    /**
     * Obtener el tipo de concepto
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type()
    {
        return $this->belongsTo(BudgetConceptType::class,'concept_type_id');
    }



    /**
     * Obtener el servicio
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function service()
    {
        return $this->belongsTo(Service::class,'service_id');
    }

    /**
     * Obtener la categoria del servicio
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function serviceCategory()
    {
        return $this->belongsTo(ServiceCategories::class,'service_category_id');
    }
}
