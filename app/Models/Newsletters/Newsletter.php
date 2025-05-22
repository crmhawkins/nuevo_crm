<?php

namespace App\Models\Newsletters;

use App\Models\Clients\Client;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Newsletter extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'newsletters';

    /**
     * Atributos asignados en masa.
     *
     * @var array
     */
    protected $fillable = [
        'client_id',
        'newsletter_id',
        'campaign',
        'email',
        'sent',
        'open',
        'times_opened',
        'date_sent',
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
     * Obtener el cliente al que está vinculado
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function client()
    {
        return $this->belongsTo(Client::class,'client_id');
    }

     /**
     * 
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function newsletter()
    {
        return $this->belongsTo(NewsletterManual::class,'newsletter_id');
    }
}
