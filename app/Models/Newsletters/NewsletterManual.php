<?php

namespace App\Models\Newsletters;

use App\Models\Services\ServiceCategories;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NewsletterManual extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'newsletters_manual';

    /**
     * Atributos asignados en masa.
     *
     * @var array
     */
    protected $fillable = [
        'clients_array_id',
        'category',
        'date_sent',
        'first_title_newsletter',
        'banner_description',
        'second_title_newsletter',
        'images_promo',
        'urls',
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
     * Obtener la categoria del servicio
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function getCategory()
    {
        return $this->belongsTo(ServiceCategories::class,'category');
    }
}
