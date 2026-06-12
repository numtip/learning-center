<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MjuAgricultureTheoryPersonnel extends Model
{
    use HasFactory;

    protected $table = 'mju_agriculture_theory_personnel';

    protected $fillable = [
        'personnel_name',
        'position',
        'center_name',
        'image',
        'created_at',
        'updated_at',
    ];
}
