<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class NewBookingNotification extends Notification
{
    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'booking',
            'title' => 'มีการจองใหม่ 📅',
            'message' => 'มีการจองกิจกรรมเข้ามาใหม่',
            'url' => '/admin/booking'
        ];
    }
}
