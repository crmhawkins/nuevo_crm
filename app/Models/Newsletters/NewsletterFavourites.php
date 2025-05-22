<?php

namespace App\Models\Newsletters;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NewsletterFavourites extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'newsletters_favourites';

    /**
     * Atributos asignados en masa.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'newsletter_id',
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
    public function adminUser()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    /**
     * Obtener el cliente al que está vinculado
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function newsletter()
    {
        return $this->belongsTo(NewsletterManual::class,'newsletter_id');
    }
}
