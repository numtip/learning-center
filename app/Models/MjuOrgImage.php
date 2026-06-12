<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MjuOrgImage extends Model
{
    protected $table = 'mju_org_images';

    protected $fillable = [
        'division',
        'section',
        'image',
        'order_no',
    ];
}
