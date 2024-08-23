<?php

namespace App\Models\Todo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Messages extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'messages';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'mensaje',
        'archivo',
        'todo_id',
        'admin_user_id',
    ];

     /**
     * Mutaciones de fecha.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
    ];


    public function todo() {
        return $this->belongsTo(Todo::class,'todo_id');
    }

    public function user() {
        return $this->belongsTo(\App\Models\Users\User::class,'admin_user_id');
    }

    public function reads()
    {
        return $this->hasMany(\App\Models\Todo\MessageRead::class, 'message_id');
    }
}
