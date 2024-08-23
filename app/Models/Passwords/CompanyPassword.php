<?php

namespace App\Models\Passwords;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyPassword extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'company_passwords';

    /**
     * Atributos asignados en masa.
     *
     * @var array
     */
    protected $fillable = [
        'client_id',
        'website',
        'user',
        'password',
    ];

    /**
     * Mutaciones de fecha.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
    ];
    public function cliente() {
        return $this->belongsTo(\App\Models\Clients\Client::class,'client_id');
    }
}
