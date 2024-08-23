<?php

namespace App\Models\Todo;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TodoUsers extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'todo_users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'todo_id',
        'admin_user_id',
        'completada',
    ];

     /**
     * Mutaciones de fecha.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
    ];

    public function usuarios() {
        return $this->belongsTo(User::class,'admin_user_id');
    }

    public function todo() {
        return $this->belongsTo(Todo::class,'todo_id');
    }

}
