<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MjuPersonnel extends Model
{
    protected $table = 'mju_personnel';

    protected $fillable = [
        'title',
        'image',
    ];
}
