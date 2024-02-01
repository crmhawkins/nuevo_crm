<?php

namespace App\Models\Contacts;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactEmail extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'contacts_emails';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'contact_id',
        'email',

    ];

     /**
     * Mutaciones de fecha.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
    ];

    public function contacto() {
        return $this->belongsTo(\App\Models\Contacts\Contact::class,'contact_id');
    }

}
