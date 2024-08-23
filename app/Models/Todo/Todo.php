<?php

namespace App\Models\Todo;

use App\Models\Clients\Client;
use App\Models\Tasks\Task;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Todo extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'to-do';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'titulo',
        'descripcion',
        'admin_user_id',
        'project_id',
        'client_id',
        'budget_id',
        'task_id',
        'finalizada'
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
    public function TodoUsers() {
        return $this->hasMany(TodoUsers::class,'todo_id');
    }

    public function proyecto() {
        return $this->belongsTo(\App\Models\Projects\Project::class,'project_id');
    }

    public function presupuesto() {
        return $this->belongsTo(\App\Models\Budgets\Budget::class,'budget_id');
    }

    public function tarea() {
        return $this->belongsTo(Task::class,'task_id');
    }

    public function cliente() {
        return $this->belongsTo(Client::class,'client_id');
    }

    public function mensajes() {
        return $this->hasMany(Messages::class,'todo_id');
    }

    public function unreadMessagesCountByUser($userId) {
        return $this->mensajes()->whereDoesntHave('reads', function ($query) use ($userId) {
            $query->where('admin_user_id', $userId)->where('is_read', true);
        })->count();
    }

    public function isCompletedByUser($userId)
    {
        return $this->todoUsers()->where('admin_user_id', $userId)->where('completada', true)->exists();
    }
}
