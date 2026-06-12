<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MjuOrder extends Model
{
    protected $table = 'mju_orders';

    protected $fillable = [
        'order_no',
        'total',
        'shipping_type',
        'shipping_fee',
        'shipping_address',
        'customer_name',
        'customer_phone',
        'status',
        'slip_image',
        'tracking_no',
        'updated_by'
    ];

    public function items()
    {
        return $this->hasMany(MjuOrderItem::class, 'order_id');
    }
}
