<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CenterAdminController extends Controller
{
    /**
     * ตั้งค่า Mapping ระหว่าง type (URL parameter) กับชื่อ Table ใน Database
     */
    private function getTableConfig($type)
    {
        $configs = [
            'lanna' => [
                'detail' => 'mju_culture_agriculture_center_detail',
                'history' => 'mju_history_lanna'
            ],
            'royal' => [
                'detail' => 'mju_agriculture_theory_detail',
                'history' => 'mju_history_theory'
            ],
            'farm' => [
                'detail' => 'mju_farm_demo_detail',
                'history' => 'mju_history_farm'
            ],
            'banpong' => [
                'detail' => 'mju_banpong_detail',
                'history' => 'mju_history_huayjo'
            ],
            'hemp' => [
                'detail' => 'mju_hemp_research_and_development_center_detail',
                'history' => 'mju_history_hemp'
            ],
        ];

        return $configs[$type] ?? null;
    }

    /**
     * 1. ดึงข้อมูลไปแสดงที่หน้า Vue ของ Admin (เทียบเท่าหน้า public แต่มีฟอร์มแก้ไข)
     */
    public function edit($type)
    {
        $config = $this->getTableConfig($type);
        if (!$config) abort(404, 'ไม่พบประเภทศูนย์การเรียนรู้นี้');

        $row = DB::table($config['detail'])->first();
        $detail = null;

        if ($row) {
            $detail = [
                'id' => $row->id,
                'center_name' => $row->center_name ?? '',
                'affiliation' => $row->affiliation ?? '',
                'history' => $row->history ?? '',
                'objective' => $row->objective ?? '',
                'image1' => $row->image1 ? 'data:image/jpeg;base64,' . base64_encode($row->image1) : null,
                'image2' => $row->image2 ? 'data:image/jpeg;base64,' . base64_encode($row->image2) : null,
                'image3' => $row->image3 ? 'data:image/jpeg;base64,' . base64_encode($row->image3) : null,
            ];
        }

        $historyData = DB::table($config['history'])
            ->orderBy('year', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'year' => $item->year,
                    'title' => $item->title,
                    'description' => $item->description,
                    'imageUrl' => $item->image ? 'data:image/jpeg;base64,' . base64_encode($item->image) : null,
                ];
            });

        // คืนค่าไปที่ SuperAdmin/CenterManage
        return Inertia::render('SuperAdmin/CenterManage', [
            'detail' => $detail,
            'historyData' => $historyData,
            'centerType' => $type,
        ]);
    }

    /**
     * 2. อัปเดตข้อมูลหลัก (Detail)
     */
    public function updateDetail(Request $request, $type)
    {
        $config = $this->getTableConfig($type);
        if (!$config) abort(404);

        $request->validate([
            'center_name' => 'required|string|max:255',
            'image1' => 'nullable|image|max:2048',
            'image2' => 'nullable|image|max:2048',
            'image3' => 'nullable|image|max:2048',
        ]);

        $updateData = [
            'center_name' => $request->center_name,
            'affiliation' => $request->affiliation,
            'history' => $request->history,
            'objective' => $request->objective,
        ];

        if ($request->hasFile('image1')) {
            $updateData['image1'] = file_get_contents($request->file('image1')->getRealPath());
        }
        if ($request->hasFile('image2')) {
            $updateData['image2'] = file_get_contents($request->file('image2')->getRealPath());
        }
        if ($request->hasFile('image3')) {
            $updateData['image3'] = file_get_contents($request->file('image3')->getRealPath());
        }

        $exists = DB::table($config['detail'])->first();
        if ($exists) {
            DB::table($config['detail'])->where('id', $exists->id)->update($updateData);
        } else {
            DB::table($config['detail'])->insert($updateData);
        }

        return back()->with('success', 'อัปเดตข้อมูลศูนย์สำเร็จ');
    }

    /**
     * 3. เพิ่ม Timeline ใหม่
     */
    public function storeHistory(Request $request, $type)
    {
        $config = $this->getTableConfig($type);
        if (!$config) abort(404);

        $request->validate([
            'year' => 'required|integer',
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048',
        ]);

        $insertData = [
            'year' => $request->year,
            'title' => $request->title,
            'description' => $request->description,
        ];

        if ($request->hasFile('image')) {
            $insertData['image'] = file_get_contents($request->file('image')->getRealPath());
        }

        DB::table($config['history'])->insert($insertData);

        return back()->with('success', 'เพิ่มประวัติสำเร็จ');
    }

    /**
     * 4. แก้ไข Timeline เดิม
     */
    public function updateHistory(Request $request, $type, $id)
    {
        $config = $this->getTableConfig($type);
        if (!$config) abort(404);

        $request->validate([
            'year' => 'required|integer',
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048',
        ]);

        $updateData = [
            'year' => $request->year,
            'title' => $request->title,
            'description' => $request->description,
        ];

        if ($request->hasFile('image')) {
            $updateData['image'] = file_get_contents($request->file('image')->getRealPath());
        }

        DB::table($config['history'])->where('id', $id)->update($updateData);

        return back()->with('success', 'แก้ไขประวัติสำเร็จ');
    }

    /**
     * 5. ลบ Timeline
     */
    public function destroyHistory($type, $id)
    {
        $config = $this->getTableConfig($type);
        if (!$config) abort(404);

        DB::table($config['history'])->where('id', $id)->delete();

        return back()->with('success', 'ลบประวัติสำเร็จ');
    }
}
