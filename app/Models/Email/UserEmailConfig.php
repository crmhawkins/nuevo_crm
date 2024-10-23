<?php
namespace App\Models\Email;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserEmailConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_user_id',
        'host',
        'port',
        'smtp_host',
        'smtp_port',
        'username',
        'password',
        'firma',
    ];

    // Relación con el usuario
    public function user() {
        return $this->belongsTo(User::class,'admin_user_id');
    }
}
