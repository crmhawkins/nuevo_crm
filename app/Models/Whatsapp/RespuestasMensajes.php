<?php

namespace App\Models\Whatsapp;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RespuestasMensajes extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'respuestas_mensajes';

    protected $fillable = [
        'remitente',
        'mensaje',
        'respuesta',
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
