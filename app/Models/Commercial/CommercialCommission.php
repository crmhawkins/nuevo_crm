<?php

namespace App\Models\Commercial;

use App\Models\Services\ServiceCategories;
use App\Models\Users\UserAccessLevel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommercialCommission extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'commercial_commission';

    /**
     * Atributos asignados en masa.
     *
     * @var array
     */
    protected $fillable = [
        'commercial_level_id',
        'commercial_product_id',
        'quanity',
    ];

    /**
     * Mutaciones de fecha.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at', 
    ];


    /**
     * Obtener el usuario
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function commercialLevel()
    {
        return $this->belongsTo(UserAccessLevel::class,'commercial_level_id');
    }

    /**
     * Obtener el usuario
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(ServiceCategories::class,'commercial_product_id');
    }
}
