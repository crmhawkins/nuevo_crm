<?php

namespace App\Models\Other;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccounts extends Model
{
    use HasFactory;

    protected $table = 'bank_accounts';
    public $timestamps = false;
    /**
     * Atributos asignados en masa.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];
}
