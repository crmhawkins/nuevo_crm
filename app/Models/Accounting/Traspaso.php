<?php

namespace App\Models\Accounting;

use App\Models\Other\BankAccounts;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Traspaso extends Model
{
    use HasFactory;
    protected $table = 'traspaso';

    protected $fillable = [
        'from_bank_id',
        'to_bank_id',
        'amount',
        'fecha',
    ];

    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
    ];

    public function from(){
        return $this->belongsTo(BankAccounts::class, 'from_bank_id');
    }

    public function to(){
        return $this->belongsTo(BankAccounts::class, 'to_bank_id');
    }
}
