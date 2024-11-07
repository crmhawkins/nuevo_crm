<?php

namespace App\Models\Email;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Email extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'emails';

    // Definir los campos que se pueden llenar mediante asignación masiva
    protected $fillable = [
        'sender',
        'to',
        'cc',
        'subject',
        'body',
        'admin_user_id',
        'status_id',
        'category_id',
        'view',
        'message_id',
        'isFactura'
    ];


    /**
     * Mutaciones de fecha.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
    ];

    // Relación con StatusMail (status)
    public function status()
    {
        return $this->belongsTo(StatusMail::class, 'status_id');
    }

    // Relación con CategoryEmail (categoría)
    public function category()
    {
        return $this->belongsTo(CategoryEmail::class, 'category_id');
    }

    // Relación con User (usuario que envió o recibe el email)
    public function user()
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class, 'email_id');
    }

}
