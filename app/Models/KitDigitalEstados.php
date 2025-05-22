<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KitDigitalEstados extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'ayudas_estados_kit';


    protected $fillable = [
        'nombre',
        'color',
        'text_color',
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
