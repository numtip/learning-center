<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewOrderNotification extends Notification
{
    use Queueable;

    protected $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'order',
            'title' => 'มีออเดอร์ใหม่ 🛒',
            'message' => 'มีคำสั่งซื้อใหม่ รหัส: ' . $this->order->order_no,
            'url' => '/admin/orders',
            'order_id' => $this->order->id
        ];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'order',
            'title' => 'มีออเดอร์ใหม่ 🛒',
            'message' => 'มีคำสั่งซื้อใหม่ รหัส: ' . $this->order->order_no,
            'url' => '/admin/orders',
            'order_id' => $this->order->id
        ];
    }
}
