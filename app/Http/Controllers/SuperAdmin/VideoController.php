<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class VideoController extends Controller
{
    // รายชื่อตารางทั้ง 5 ศูนย์
    protected $centers = [
        'mju_agriculture_theory_detail' => 'ศูนย์การเรียนรู้เกษตรทฤษฎีใหม่',
        'mju_banpong_detail' => 'โครงการพัฒนาบ้านโปง',
        'mju_culture_agriculture_center_detail' => 'ศูนย์เรียนรู้วัฒนธรรมเกษตรล้านนา',
        'mju_farm_demo_detail' => 'ฟาร์มสาธิตสำนักวิจัยและส่งเสริม',
        'mju_hemp_research_and_development_center_detail' => 'ศูนย์ทดสอบ วิจัยและพัฒนากัญชง'
    ];

    public function index()
    {
        $videos = [];

        // วนลูปดึงข้อมูล id=1 จากทั้ง 5 ตาราง
        foreach ($this->centers as $table => $name) {
            $record = DB::table($table)->where('id', 1)->first();

            $videos[] = [
                'table_name'  => $table,
                'center_name' => $record ? $record->center_name : $name,
                'video_url'   => $record ? $record->video_url : null,
                'youtube_id'  => ($record && $record->video_url) ? $this->extractYoutubeId($record->video_url) : null
            ];
        }

        // ชี้ไปที่ไฟล์ HomeVideoManager.vue
        return Inertia::render('SuperAdmin/HomeVideoManager', [
            'videos' => $videos
        ]);
    }

    // เพิ่ม หรือ อัปเดตวิดีโอ
    public function store(Request $request)
    {
        $request->validate([
            'table_name' => 'required|string',
            'youtube_url' => 'required|url'
        ]);

        $table = $request->table_name;

        // เช็คความปลอดภัย ตรวจสอบว่าเป็น 1 ใน 5 ตารางที่อนุญาตหรือไม่
        if (!array_key_exists($table, $this->centers)) {
            return back()->withErrors(['table_name' => 'ข้อมูลศูนย์ไม่ถูกต้อง']);
        }

        $youtubeId = $this->extractYoutubeId($request->youtube_url);

        if (!$youtubeId) {
            return back()->withErrors(['youtube_url' => 'ลิงก์ YouTube ไม่ถูกต้อง']);
        }

        // ทำการ Update คอลัมน์ video_url ที่ id = 1
        DB::table($table)->where('id', 1)->update([
            'video_url' => 'https://www.youtube.com/watch?v=' . $youtubeId,
            'updated_at' => now(),
        ]);

        return back();
    }

    // ลบวิดีโอ (เปลี่ยนเป็นเคลียร์ค่า URL ให้ว่าง)
    public function destroy($tableName)
    {
        if (array_key_exists($tableName, $this->centers)) {
            DB::table($tableName)->where('id', 1)->update([
                'video_url' => null,
                'updated_at' => now(),
            ]);
        }

        return back();
    }

    // ฟังก์ชันแยก YouTube ID
    private function extractYoutubeId($url)
    {
        if (!$url) return null;
        preg_match(
            '%(?:youtube\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i',
            $url,
            $matches
        );

        return $matches[1] ?? null;
    }
}
