<?php

namespace App\Models\Logs;

use App\Models\KitDigital;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LogActions extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'log_actions';

    /**
     * Atributos asignados en masa.
     *
     * @var array
     */
    protected $fillable = [
        'tipo',
        'admin_user_id',
        'action',
        'description',
        'reference_id',

    ];


    /**
     * Mutaciones de fecha.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
    ];


    public function usuario()
    {
        return $this->belongsTo(\App\Models\Users\User::class, 'admin_user_id');
    }

    public function tipo()
    {
        return $this->belongsTo(\App\Models\Logs\LogsTipes::class, 'tipo');
    }

    public function ayudas(){
        return $this->belongsTo(KitDigital::class, 'reference_id');
    }


}
