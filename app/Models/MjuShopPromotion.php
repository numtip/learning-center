<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MjuShopPromotion extends Model
{
    protected $table = 'mju_shop_promotion_list';

    protected $fillable = [
        'title',
        'image1',
        'image2',
        'image3',
        'image4',
        'image5',
        'description',
    ];
}
