<?php

namespace App\Models\Autoseo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutoseoReportsModel extends Model
{
    use HasFactory;
    protected $table = 'autoseo_reports';

    protected $fillable = [
        'autoseo_id',
        'path',
    ];
}
