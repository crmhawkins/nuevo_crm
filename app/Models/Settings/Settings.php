<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    use HasFactory;

    protected $fillable = [
        'price_hour',
        'fecha_inicio_verano',
        'fecha_fin_verano',
        'fecha_inicio_invierno',
        'fecha_fin_invierno',
    ];
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
    ];

    public function horarios()
    {
        return $this->hasMany(schedule::class);
    }
}
