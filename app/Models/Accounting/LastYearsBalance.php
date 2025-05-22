<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Other\BankAccounts;

class LastYearsBalance extends Model{
    use HasFactory,SoftDeletes;

    protected $table = 'last_years_balance';

    /**
     * Atributos asignados en masa.
     *
     * @var array
     */
    protected $fillable = [
        'bank_id',
        'year',
        'quantity',
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

}
