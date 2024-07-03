<?php

namespace App\Models\Logs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LogsEmail extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'logs_email';

    /**
     * Atributos asignados en masa.
     *
     * @var array
     */
    protected $fillable = [
        'mail_emisor',
        'mail_receptor',
        'status',
        'mensaje',
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
