<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CenterBooking extends Model
{
    protected $table = 'center_bookings';

    // เพิ่ม admin_note เข้าไปใน array นี้
    protected $fillable = [
        'user_id',
        'type',
        'name',
        'org',
        'email',
        'phone',
        'visit_date',
        'centers',
        'activities',
        'level',
        'people',
        'food_package',
        'price_per_person',
        'total_price',
        'status',
        'admin_note', 
    ];

    protected $casts = [
        'centers' => 'array',
        'activities' => 'array'
    ];
}
