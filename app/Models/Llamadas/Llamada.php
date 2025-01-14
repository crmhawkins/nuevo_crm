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
        'client_id',
        'kit_id',
        'comentario'
    ];
    protected $dates = [
        'created_at','updated_at', 'deleted_at','start_time','end_time'
   ];
    public function user() {
        return $this->belongsTo(\App\Models\Users\User::class, 'admin_user_id');
    }
    public function client() {
        return $this->belongsTo(\App\Models\Clients\Client::class, 'client_id');
    }
    public function kit() {
        return $this->belongsTo(\App\Models\KitDigital::class, 'kit_id');
    }

    public function getDurationAttribute()
    {
        if ($this->start_time && $this->end_time) {
            $start = Carbon::parse($this->start_time);
            $end = Carbon::parse($this->end_time);
            return $start->diff($end)->format('%H:%I:%S');
        }
        return null;
    }

}
