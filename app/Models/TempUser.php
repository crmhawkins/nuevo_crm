<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempUser extends Model
{
    protected $table = 'temp_users';

    protected $fillable = [
        'user',
        'password',
        'compra'
    ];

    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
    ];

    public function purchase() {
        return $this->belongsTo(Purchase::class);
    }
}
