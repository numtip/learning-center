<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MjuOrganization extends Model
{
    protected $table = 'mju_organization';

    protected $fillable = [
        'title',
        'image',
    ];
}
