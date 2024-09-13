<?php

namespace App\Models\Llamadas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Llamada extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_user_id',
        'start_time',
        'end_time',
        'is_active',
        'phone',
        'client_id'
    ];

    public function user() {
        return $this->belongsTo(\App\Models\Users\User::class, 'admin_user_id');
    }

}
