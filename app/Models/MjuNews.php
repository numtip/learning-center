<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MjuNews extends Model
{
    use HasFactory;

    protected $table = 'mju_news';

    protected $fillable = [
        'title',
        'image',
        'description',
        'category',
    ];
}
