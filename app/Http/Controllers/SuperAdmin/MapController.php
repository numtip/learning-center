<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class MapController extends Controller
{
private function getConfig($centerId)
{
    $config = [
        1 => [
            'detail' => 'mju_agriculture_theory_detail',
            'activities' => 'mju_agriculture_theory_activities'
        ],
        2 => [
            'detail' => 'mju_farm_demo_detail',
            'activities' => 'mju_farm_demo_activities'
        ],
        3 => [
            'detail' => 'mju_culture_agriculture_center_detail',
            'activities' => 'mju_culture_agriculture_center_activities'
        ],
        4 => [
            'detail' => 'mju_banpong_detail',
            'activities' => 'mju_banpong_activities'
        ],
        5 => [
            'detail' => 'mju_hemp_research_and_development_center_detail',
            'activities' => 'mju_hemp_research_and_development_center_activities'
        ],
    ];

    if (!isset($config[$centerId])) abort(404);
    return $config[$centerId];
}
    public function editor($centerId)
    {
        $cfg = $this->getConfig($centerId);

        $detail = DB::table($cfg['detail'])->first();

        $activities = DB::table($cfg['activities'])
            ->select('id','activity_name','description')
            ->get();

        $markers = DB::table('mju_map_mark')
            ->where('center_id', $centerId)
            ->select('id','center_id','activity_id','title','description','x_percent','y_percent')
            ->get();

        return Inertia::render('SuperAdmin/MapEditor', [
            'centerId'   => (int)$centerId,
            'mapImage'   => $detail && $detail->map_image
                ? 'data:image/jpeg;base64,' . base64_encode($detail->map_image)
                : null,
            'activities' => $activities,
            'markers'    => $markers
        ]);
    }

    // เพิ่มหมุด
    public function store(Request $request)
    {
        DB::table('mju_map_mark')->insert([
            'center_id'   => $request->center_id,
            'activity_id' => $request->activity_id,
            'title'       => $request->title,
            'description' => $request->description,
            'x_percent'   => $request->x_percent,
            'y_percent'   => $request->y_percent,
            'created_at'  => now(),
            'updated_at'  => now()
        ]);

        return redirect()->back();
    }

    // แก้กิจกรรมของหมุด
    public function update(Request $request, $id)
    {
        $cfg = $this->getConfig($request->center_id);

        $activity = DB::table($cfg['activities'])
            ->where('id', $request->activity_id)
            ->first();

        DB::table('mju_map_mark')->where('id', $id)->update([
            'activity_id' => $request->activity_id,
            'title'       => $activity->activity_name,
            'description' => $activity->description,
            'updated_at'  => now()
        ]);

        return redirect()->back();
    }

    // ลบหมุด
    public function destroy($id)
    {
        DB::table('mju_map_mark')->where('id', $id)->delete();
        return redirect()->back();
    }
}
