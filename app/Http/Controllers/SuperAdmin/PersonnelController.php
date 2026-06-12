<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Illuminate\Http\Request;
// นำเข้า Model ทั้ง 5 ตัว
use App\Models\MjuCultureAgricultureCenterPersonnel;
use App\Models\MjuBanpongPersonnel;
use App\Models\MjuAgricultureTheoryPersonnel;
use App\Models\MjuFarmDemoPersonnel;
use App\Models\MjuHempPersonnel; // << เพิ่ม Model ศูนย์กัญชง

class PersonnelController extends Controller
{
    public function index()
    {
        // รวบรวมข้อมูลจากทั้ง 5 ตาราง
        $data = collect()
            ->merge(MjuCultureAgricultureCenterPersonnel::all()->map(fn($p)=>$this->map($p,'culture')))
            ->merge(MjuBanpongPersonnel::all()->map(fn($p)=>$this->map($p,'banpong')))
            ->merge(MjuAgricultureTheoryPersonnel::all()->map(fn($p)=>$this->map($p,'theory')))
            ->merge(MjuFarmDemoPersonnel::all()->map(fn($p)=>$this->map($p,'farm')))
            ->merge(MjuHempPersonnel::all()->map(fn($p)=>$this->map($p,'hemp'))); // << เพิ่มการดึงข้อมูลกัญชง

        return Inertia::render('SuperAdmin/Personnel/Index', [
            'personnels' => $data->values()
        ]);
    }

    public function store(Request $request)
    {
        $model = $this->model($request->center);

        $data = [
            'personnel_name' => $request->name,
            'position' => $request->position,
            'center_name' => $request->center,
        ];

        if ($request->hasFile('image')) {
            $data['image'] = file_get_contents($request->file('image'));
        }

        $model::create($data);

        return redirect()->back();
    }

    public function update(Request $request, $id)
    {
        $model = $this->model($request->center);
        $row = $model::findOrFail($id);

        if ($request->hasFile('image')) {
            $row->image = file_get_contents($request->file('image'));
        }

        $row->personnel_name = $request->name;
        $row->position = $request->position;
        $row->center_name = $request->center;
        $row->save();

        return redirect()->back();
    }

    public function destroy($id)
    {
        // ค้นหาและลบจากทั้ง 5 ตาราง
        $models = [
            MjuCultureAgricultureCenterPersonnel::class,
            MjuBanpongPersonnel::class,
            MjuAgricultureTheoryPersonnel::class,
            MjuFarmDemoPersonnel::class,
            MjuHempPersonnel::class, // << เพิ่มในรายการลบ
        ];

        foreach ($models as $model) {
            if ($row = $model::find($id)) {
                $row->delete();
                break;
            }
        }

        return redirect()->back();
    }

    private function map($p, $center)
    {
        return [
            'id' => $p->id,
            'name' => $p->personnel_name,
            'position' => $p->position,
            'center' => $p->center_name,
            // แปลง BLOB เป็น Base64 เพื่อแสดงผลบนหน้าเว็บ
            'image' => $p->image ? 'data:image/jpeg;base64,' . base64_encode($p->image) : null,
        ];
    }

    private function model($center)
    {
        // ตรวจสอบชื่อศูนย์ให้ตรงกับที่ส่งมาจาก Vue
        return match($center) {
            'ศูนย์วัฒนธรรมเกษตรล้านนา' => MjuCultureAgricultureCenterPersonnel::class,
            'ศูนย์เกษตรทฤษฎีใหม่' => MjuAgricultureTheoryPersonnel::class,
            'ศูนย์ฟาร์มสาธิต' => MjuFarmDemoPersonnel::class,
            'ศูนย์บ้านโป่ง' => MjuBanpongPersonnel::class,
            'ศูนย์การเรียนรู้กัญชง' => MjuHempPersonnel::class, // << เพิ่มเงื่อนไขศูนย์ที่ 5
        };
    }
}
