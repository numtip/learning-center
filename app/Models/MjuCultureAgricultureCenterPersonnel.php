<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MjuCultureAgricultureCenterPersonnel extends Model
{
    protected $table = 'mju_culture_agriculture_center_personnel';

    protected $fillable = [
        'personnel_name',
        'position',
        'center_name',
        'image',
        'created_at',
        'updated_at',
    ];
}
