<?php

namespace App\Models\Whatsapp;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mensaje extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'mensajes';

    protected $fillable = [
        'id_mensaje',
        'id_three',
        'remitente',
        'mensaje',
        'respuesta',
        'status_mensaje',
        'status',
        'type',
        'date',
        'is_automatic',
        'ayuda_id',
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
