<?php

namespace App\Models\CrmActivities;

use App\Models\Clients\Client;
use App\Models\Other\ContactBy;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CrmActivitiesMeetings extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'crm_activities_meetings';

    /**
     * Atributos asignados en masa.
     *
     * @var array
     */
    protected $fillable = [
        'admin_user_id',
        'client_id',
        'contact_by_id',
        'done',
        'subject',
        'address',
        'date',
        'time_start',
        'time_end',
        'description',
        'files',
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
     * Obtener el cliente
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function client()
    {
        return $this->belongsTo(Client::class,'client_id');
    }

    /**
     * Obtener como se le contactó
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function contactBy()
    {
        return $this->belongsTo(ContactBy::class,'contact_by_id');
    }
}
