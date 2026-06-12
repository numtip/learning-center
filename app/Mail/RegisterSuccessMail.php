<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RegisterSuccessMail extends Mailable
{
    use SerializesModels;

    public $user;
    public $password;

    public function __construct($user, $password)
    {
        $this->user = $user;
        $this->password = $password;
    }

    public function build()
    {
        return $this->subject('สมัครสมาชิกสำเร็จ - ระบบจองศูนย์การเรียนรู้')
            ->html('
            <div style="font-family:Sarabun, sans-serif; padding:20px; background:#f4f8f4;">
                <div style="max-width:600px; margin:auto; background:white; padding:20px; border-radius:10px;">
                    <h2 style="color:#2e7d32;">🌿ยืนยันการสมัครสมาชิก เว็บแอปพลิเคชันสำหรับศูนย์การเรียนรู้ของสำนักวิจัยและส่งเสริมวิชาการการเกษตรมหาวิทยาลัยแม่โจ้ เรียบร้อยแล้ว</h2>

                    <p>เรียน คุณ <b>'.$this->user->fullname.'</b></p>

                    <p>
                        ในนามของสำนักวิจัยและส่งเสริมวิชาการการเกษตร มหาวิทยาลัยแม่โจ้ ขอขอบพระคุณที่ท่านได้ให้ความสนใจสมัครสมาชิกเว็บแอปพลิเคชันเพื่อการเรียนรู้ของเรา<br><br>
                        การสมัครสมาชิกของท่านเสร็จสมบูรณ์แล้ว ขณะนี้ท่านสามารถเข้าถึงองค์ความรู้ งานวิจัย และข่าวสารด้านเทคโนโลยีการเกษตรแม่นยำของเราได้ทันที เพื่อร่วมกันขับเคลื่อนภาคการเกษตรไทยให้ยั่งยืน<br><br>
                        ขอแสดงความนับถือ,<br>
                        ทีมงานพัฒนาระบบข้อมูลดิจิทัล<br>
                        สำนักวิจัยและส่งเสริมวิชาการการเกษตร มหาวิทยาลัยแม่โจ้
                    </p>

                    <hr>

                    <p><b>📌 รายละเอียดบัญชี</b></p>
                    <p>
                        Username: '.$this->user->email.' <br>
                        Password: '.$this->password.'
                    </p>

                    <div style="background:#fff3e0; padding:10px; border-radius:8px; margin-top:10px;">
                        🔒 แนะนำให้เปลี่ยนรหัสผ่านหลังเข้าสู่ระบบครั้งแรก
                    </div>

                    <p style="margin-top:15px;">
                        เจ้าหน้าที่จะติดต่อกลับเพื่อยืนยันรายละเอียด
                    </p>

                    <hr>

                    <p style="font-size:14px;">
                        ติดต่อ: สำนักวิจัยและส่งเสริมวิชาการการเกษตร มหาวิทยาลัยแม่โจ้ | ชั้น 3 อาคารเฉลิมพระเกียรติสมเด็จพระเทพรัตนราชสุดา (ศูนย์ไบโอเทคโนโลยี) | 63 หมู่ 4 ตำบลหนองหาร อำเภอสันทราย จังหวัดเชียงใหม่ 50290 <br>
                        📞 0 5387 3400-4 | ✉ raemju@gmail.com
                    </p>

                    <p style="color:#777;">ศูนย์การเรียนรู้</p>
                </div>
            </div>
            ');
    }
}
