<?php

namespace App\Models\Email;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attachment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['email_id', 'file_path', 'file_name'];

    protected $dates = ['created_at', 'updated_at', 'deleted_at' ];

    // Relación con el modelo Email
    public function email()
    {
        return $this->belongsTo(Email::class);
    }
}
