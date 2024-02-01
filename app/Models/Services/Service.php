<?php

namespace App\Models\Services;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'services';

    protected $fillable = [
        'services_categories_id',
        'title',
        'concept',
        'price',
        'estado',
        'order'
    ];

    /**
     * Mutaciones de fecha.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
    ];

    public function serviceCategoria() {
        return $this->belongsTo(\App\Models\Services\ServiceCategories::class,'services_categories_id');
    }
}
