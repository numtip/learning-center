<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MjuHempResearchAndDevelopmentCenterPersonnel extends Model
{
    protected $table = 'mju_hemp_research_and_development_center_personnel';

    protected $fillable = [
        'personnel_name',
        'position',
        'center_name',
        'image',
        'created_at',
        'updated_at',
    ];
}
