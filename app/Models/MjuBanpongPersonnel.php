<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MjuBanpongPersonnel extends Model
{
    protected $table = 'mju_banpong_personnel';

    protected $fillable = [
        'personnel_name',
        'position',
        'center_name',
        'image',
        'created_at',
        'updated_at',
    ];
}
