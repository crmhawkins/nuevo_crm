<?php
namespace App\Models\Email;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserEmailConfig extends Model
{
    use HasFactory;
    use SoftDeletes;

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
