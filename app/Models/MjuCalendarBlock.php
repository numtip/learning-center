<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MjuCalendarBlock extends Model
{
    use HasFactory;

    protected $fillable = [
        'blocked_date',
        'reason'
    ];
}
