<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class StockAlertNotification extends Notification
{
    use Queueable;

    protected $productName;
    protected $type;

    public function __construct($productName, $type)
    {
        $this->productName = $productName;
        $this->type = $type;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $title = '';
        $message = '';

        if ($this->type === 'out_of_stock') {
            $title = 'สินค้าหมด ❌';
            $message = 'แจ้งเตือน: สินค้า "' . $this->productName . '" หมดสต็อกแล้ว กรุณาเติมสินค้าเพื่อให้สามารถจำหน่ายต่อได้';
        } elseif ($this->type === 'low_stock') {
            $title = 'สินค้าใกล้หมด ⚠️';
            $message = 'สินค้า "' . $this->productName . '" ใกล้หมดแล้ว (เหลือ 5 ชิ้นหรือน้อยกว่า)';
        } elseif ($this->type === 'order_failed') {
            $title = 'คำสั่งซื้อถูกระงับ ❌';
            $message = 'แจ้งเตือน: มีคำสั่งซื้อที่ไม่สามารถดำเนินการได้ เนื่องจากสินค้า "' . $this->productName . '" หมดสต็อก';
        }

        return [
            'type' => 'stock_alert',
            'title' => $title,
            'message' => $message,
            'url' => '/superadmin/products'
        ];
    }

    public function toArray($notifiable)
    {
        return $this->toDatabase($notifiable);
    }
}
