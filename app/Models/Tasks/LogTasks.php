<?php

namespace App\Models\Tasks;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogTasks extends Model
{
    use HasFactory;

    protected $table = 'log_tasks';
    /**
     * Atributos asignados en masa.
     *
     * @var array
     */
    protected $fillable = [
        'admin_user_id',
        'task_id',
        'date_start',
        'date_end',
        'status',
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
     * Obtener el empleado      
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function empleado()
    {
        return $this->belongsTo(User::class,'admin_user_id');
    }

    /**
     * Obtener la tarea   
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tarea()
    {
        return $this->belongsTo(Task::class,'task_id')->withTrashed();
    }
}
