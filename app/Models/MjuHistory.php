<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MjuHistory extends Model
{
    use HasFactory;

    protected $table = 'mju_history';

    protected $fillable = [
        'year',
        'title',
        'description',
        'image',
        'image_alt',
    ];
}
