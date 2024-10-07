<?php

namespace App\Models\CrmActivities;

use App\Models\Contacts\Contact;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CrmActivitiesMeetingsXContact extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'crm_activities_meetings_x_contact';

    /**
     * Atributos asignados en masa.
     *
     * @var array
     */
    protected $fillable = [
        'meeting_id',
        'contact_id',
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
    public function Contacto()
    {
        return $this->belongsTo(Contact::class,'contact_id');
    }

    /**
     * Obtener el cliente al que está vinculado
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function meeting()
    {
        return $this->belongsTo(CrmActivitiesMeetings::class,'meeting_id');
    }
}
