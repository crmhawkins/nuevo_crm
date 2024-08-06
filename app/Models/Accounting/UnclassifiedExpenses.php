<?php

namespace App\Models\Accounting;

use App\Models\Other\BankAccounts;
use App\Models\PurcharseOrde\PurcharseOrder;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UnclassifiedExpenses extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'unclassified_expenses';

    protected $fillable = [
        'pdf_file_name',
        'company_name',
        'bank',
        'iban',
        'amount',
        'received_date',
        'invoice_number',
        'order_number',
        'accepted',
        'message',
        'documents'
    ];

    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
    ];

    public function getFormattedCreatedAtAttribute()
    {
        return Carbon::parse($this->attributes['created_at'])->format('Y-m-d');
    }

    public function OrdenCompra()
    {
        return $this->belongsTo(PurcharseOrder::class, 'order_number');
    }


}
