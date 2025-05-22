<?php

namespace App\Models\Logs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogsTipes extends Model
{
    use HasFactory;

    protected $table = 'logs_tipes';

     /**
     * Atributos asignados en masa.
     *
     * @var array
     */
    protected $fillable = [
        'name'
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
