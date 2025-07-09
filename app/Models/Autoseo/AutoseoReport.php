<?php

namespace App\Models\Autoseo;

use Illuminate\Database\Eloquent\Model;

class AutoseoReport extends Model
{
    protected $table = 'autoseo_reports';

    protected $fillable = [
        'autoseo_id',
        'path',
        'creation_date'
    ];

    public function autoseo()
    {
        return $this->belongsTo(Autoseo::class);
    }
}
