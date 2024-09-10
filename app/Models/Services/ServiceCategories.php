<?php

namespace App\Models\Services;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceCategories extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'services_categories';

    protected $fillable = [
        'name',
        'terms',
        'type',
        'inactive',
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
