<?php

namespace App\Models;

use App\Models\Budgets\Budget;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PresupuestoComentario extends Model
{
    use SoftDeletes;

    protected $fillable = ['presupuesto_id', 'user_id', 'comentario'];

    public function user()
    {
        return $this->belongsTo(AdminUser::class);
    }

    public function presupuesto()
    {
        return $this->belongsTo(Budget::class);
    }
}
