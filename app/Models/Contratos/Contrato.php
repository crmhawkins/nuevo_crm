<?php

namespace App\Models\Contratos;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Contrato extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'contratos';

    protected $fillable = [
        'admin_user_id',
        'fecha',
        'archivo',
    ];

    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
    ];

    public function usuario() {
        return $this->belongsTo(User::class,'admin_user_id');
    }
}
