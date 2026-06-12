<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MjuVision extends Model
{
    protected $table = 'mju_vision';

    protected $fillable = [
        'title',
        'description',
        'image',
    ];
}
