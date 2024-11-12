<?php

namespace App\Models\Accounting;

use App\Models\Other\BankAccounts;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Gasto extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'gastos';

    /**
     * Atributos asignados en masa.
     *
     * @var array
     */
    protected $fillable = [
        'invoice_id',
        'payment_method_id',
        'bank_id',
        'title',
        'quantity',
        'received_date',
        'date',
        'reference',
        'state',
        'budget_date',
        'documents',
        'transfer_movement',
        'aprobado',
        'iva',
        'categoria_id'

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
     * Obtener los gastos
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function bankAccount()
    {
        return $this->belongsTo(BankAccounts::class,'bank_id');
    }

    public function categoria(){
        return $this->belongsTo(CategoriaGastos::class,'categoria_id');
    }


}
