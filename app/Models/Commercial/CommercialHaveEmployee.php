<?php

namespace App\Models\Commercial;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommercialHaveEmployee extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'commercial_have_employee';

    /**
     * Atributos asignados en masa.
     *
     * @var array
     */
    protected $fillable = [
        'admin_user_id',
        'commercial_id',
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
        return $this->belongsTo(User::class,'admin_user_id');
    }

    /**
     * Obtener el usuario
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function commercialUser()
    {
        return $this->belongsTo(User::class,'commercial_id');
    }
}
