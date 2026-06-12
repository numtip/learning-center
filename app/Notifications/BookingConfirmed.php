<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Carbon\Carbon;

class BookingConfirmed extends Notification
{
    use Queueable;

    protected $booking;

    public function __construct($booking)
    {
        $this->booking = $booking;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $date = Carbon::parse($this->booking->visit_date)->addYears(543)->locale('th')->isoFormat('D MMMM YYYY');

        return (new MailMessage)
            ->subject('ยืนยันการจองกิจกรรม - ศูนย์การเรียนรู้ MJU')
            ->greeting('สวัสดีคุณ ' . $this->booking->name)
            ->line('เรารับข้อมูลการจองกิจกรรมของคุณเรียบร้อยแล้ว')
            ->line('**รายละเอียดการจอง:**')
            ->line('📅 วันที่เข้าชม: ' . $date)
            ->line('👥 จำนวนผู้เข้าร่วม: ' . $this->booking->people . ' คน')
            ->line('🏫 ศูนย์ที่เลือก: ' . implode(', ', $this->booking->centers))
            ->line('📌 สถานะปัจจุบัน: รอยืนยัน (เจ้าหน้าที่จะตรวจสอบคิวและติดต่อกลับ)')
            ->action('ตรวจสอบสถานะการจอง', url('/service/booking'))
            ->line('ขอบคุณที่ใช้บริการศูนย์การเรียนรู้ของเราค่ะ');
    }
}
