<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ActivityController extends Controller
{
    private $tableMap = [
        1 => 'mju_agriculture_theory_activities',
        2 => 'mju_farm_demo_activities',
        3 => 'mju_culture_agriculture_center_activities',
        4 => 'mju_banpong_activities',
        5 => 'mju_hemp_research_and_development_center_activities', // ปรับชื่อให้ตรงตามรูป DB
    ];

    public function store(Request $request, $centerId)
    {
        if (!array_key_exists($centerId, $this->tableMap)) {
            abort(404, 'ไม่พบข้อมูลศูนย์การเรียนรู้นี้');
        }

        $table = $this->tableMap[$centerId];

        $imageData = null;
        if ($request->hasFile('image')) {
            $imageData = file_get_contents($request->file('image')->getRealPath());
        }

        // ตรวจสอบว่าตารางนี้ใช้โครงสร้างแบบ image เดียว หรือ image1-5
        $columns = DB::getSchemaBuilder()->getColumnListing($table);

        $insertData = [
            'activity_name'      => $request->activity_name,
            'description'        => $request->description,
            'location_name'      => $request->location_name,
            'location_link'      => $request->input('location_link', null),
            'instructor'         => $request->instructor,
            'duration'           => $request->duration,
            'recommended_time'   => $request->input('recommended_time', null),
            'participants_count' => $request->participants_count,
            'price'              => $request->input('price', null),
            'created_at'         => now(),
            'updated_at'         => now(),
        ];

        if (in_array('image', $columns)) {
            $insertData['image'] = $imageData;
        } elseif (in_array('image1', $columns)) {
            $insertData['image1'] = $imageData;
            $insertData['image2'] = null;
            $insertData['image3'] = null;
            $insertData['image4'] = null;
            $insertData['image5'] = null;
        }

        DB::table($table)->insert($insertData);

        return back();
    }

    public function show($centerId, $id)
    {
        if (!array_key_exists($centerId, $this->tableMap)) {
            abort(404, 'ไม่พบข้อมูลศูนย์การเรียนรู้นี้');
        }

        $table = $this->tableMap[$centerId];
        $activity = DB::table($table)->where('id', $id)->first();

        if ($activity) {
            // 1. จัดการกรณีมีคอลัมน์ image1 - image5
            for ($i = 1; $i <= 5; $i++) {
                $imgCol = "image{$i}";
                if (isset($activity->$imgCol) && !empty($activity->$imgCol)) {
                    $activity->$imgCol = 'data:image/jpeg;base64,' . base64_encode($activity->$imgCol);
                }
            }

            // 2. จัดการกรณีมีคอลัมน์ image (ตัวเดียว) ให้ Map เข้า image1 เพื่อให้ Vue แสดงผลได้
            if (isset($activity->image) && !empty($activity->image)) {
                $activity->image1 = 'data:image/jpeg;base64,' . base64_encode($activity->image);
            }
        }

        return Inertia::render('SuperAdmin/ActivityDetail', [
            'activity' => $activity,
            'centerKey' => (string)$centerId
        ]);
    }
}
