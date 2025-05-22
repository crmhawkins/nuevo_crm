<?php

namespace App\Models\Bajas;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Baja extends Model
{

    use SoftDeletes;

    protected $table = 'bajas';

    /**
     * Atributos asignados en masa. Por seguridad.
     *
     * @var array
     */
    protected $fillable = [
        'admin_user_id',
        'archivos',
        'inicio',
        'fin',
        'observacion'
    ];

    /**
     * Atributos que deben mutarse a fechas.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at'
    ];

    public function usuario()
    {
        return $this->belongsTo(\App\Models\Users\User::class, 'admin_user_id');
    }

}

