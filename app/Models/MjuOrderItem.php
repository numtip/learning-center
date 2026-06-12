<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MjuOrderItem extends Model
{
    protected $table = 'mju_order_items';

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'price',
        'qty',
        'subtotal',
        'product_image',
        'category'
    ];

    public function order()
    {
        return $this->belongsTo(MjuOrder::class, 'order_id');
    }
}
