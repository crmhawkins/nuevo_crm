<?php

namespace App\Models\Jornada;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Jornada extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_user_id',
        'start_time',
        'end_time',
        'is_active'
    ];

    public function user() {
        return $this->belongsTo(\App\Models\Users\User::class, 'admin_user_id');
    }


    public function pauses() {
        return $this->hasMany(\App\Models\Jornada\Pause::class);
    }
    public function pausasActiva() {
        return $this->pauses()->whereNull('end_time')->first();
    }

    public function getTotalWorkedTimeAttribute() {
        $totalPausedTime = $this->pauses->sum(function($pause) {
            return $pause->end_time->diffInSeconds($pause->start_time);
        });

        $endTime = $this->end_time ?? now();
        $workedTime = $endTime->diffInSeconds($this->start_time) - $totalPausedTime;

        return $workedTime;
    }
}
