<?php

namespace App\Models\Email;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StatusMail extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'status_email';

    /**
     * Atributos asignados en masa.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'color',
        'other',

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
