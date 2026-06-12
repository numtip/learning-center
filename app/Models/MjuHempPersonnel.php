<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MjuHempPersonnel extends Model
{
    use HasFactory;

    // กำหนดชื่อตารางให้ตรงกับในฐานข้อมูลของคุณ
    protected $table = 'mju_hemp_research_and_development_center_personnel';

    // กำหนดฟิลด์ที่อนุญาตให้บันทึกข้อมูลได้ (Mass Assignment)
    protected $fillable = [
        'personnel_name',
        'position',
        'center_name',
        'image',
    ];

}
