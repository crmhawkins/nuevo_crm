<?php

namespace App\Models\Accounting;

use App\Models\Invoices\Invoice;
use App\Models\Other\BankAccounts;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ingreso extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'ingresos';

    /**
     * Atributos asignados en masa.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'quantity',
        'bank_id',
        'invoice_id',
        'budget_date',
        'date'
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
     * Obtener Banco
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function bankAccount()
    {
        return $this->belongsTo(BankAccounts::class,'bank_id');
    }

    public function getInvoice()
    {
        return $this->belongsTo(Invoice::class,'invoice_id');
    }
}
