<?php

namespace App\Models\Events;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'url',
        'color',
        'admin_user_id',
        'start',
        'end',
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
    public function user()
    {
        return $this->belongsTo(User::class,'admin_user_id');
    }
    public function nonNullAttributes()
    {
        return array_filter($this->attributesToArray(), function($value) {
            return $value !== null;
        });
    }
}
