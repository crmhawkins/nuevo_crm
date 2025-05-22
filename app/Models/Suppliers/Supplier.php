<?php

namespace App\Models\Suppliers;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'suppliers';

    /**
     * Atributos asignados en masa.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'dni',
        'cif',
        'email',
        'country',
        'city',
        'province',
        'address',
        'zipcode',
        'work_activity',
        'fax',
        'phone',
        'web',
        'facebook',
        'twitter',
        'linkedin',
        'instagram',
        'pinterest',
        'note',
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
