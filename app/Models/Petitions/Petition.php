<?php

namespace App\Models\Petitions;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Petition extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'petitions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'admin_user_id',
        'client_id',
        'note',
        'finished',
    ];

    /**
     * Mutaciones de fecha.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
    ];

    public function usuario() {
        return $this->belongsTo(\App\Models\Users\User::class,'admin_user_id');
    }
    public function cliente() {
        return $this->belongsTo(\App\Models\Clients\Client::class,'client_id');
    }
    public function getEstado() {
        if ($this->finished) {
            return 'Realizado';
        } else {
            return 'Pendiente';
        }
    }

}
