<?php

namespace App\Models\Invoices;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceReferenceAutoincrement extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'invoice_reference_autoincrements';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'reference_autoincrement',
        'year',
        'month_num',
        'month',
        'month_full',
        'day',
        'letter_months',
        'ceuta',

    ];

     /**
     * Mutaciones de fecha.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
    ];
}
