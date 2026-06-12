<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\MjuCultureAgricultureCenterPersonnel;
use App\Models\MjuAgricultureTheoryPersonnel;
use App\Models\MjuBanpongPersonnel;
use App\Models\MjuHempResearchAndDevelopmentCenterPersonnel;
use App\Models\MjuFarmDemoPersonnel;

class AboutController extends Controller
{
    public function admin()
    {
        $sections = [
            [
                'title' => 'ศูนย์เรียนรู้วัฒนธรรมเกษตรล้านนา',
                'icon' => '🌿',
                'staff' => MjuCultureAgricultureCenterPersonnel::all()->map(fn($item) => $this->formatData($item))
            ],
            [
                'title' => 'ศูนย์การเรียนรู้เกษตรทฤษฎีใหม่',
                'icon' => '🚜',
                'staff' => MjuAgricultureTheoryPersonnel::all()->map(fn($item) => $this->formatData($item))
            ],
            [
                'title' => 'ศูนย์เรียนรู้พัฒนาบ้านโปง',
                'icon' => '🏡',
                'staff' => MjuBanpongPersonnel::all()->map(fn($item) => $this->formatData($item))
            ],
            [
                'title' => 'ศูนย์ทดสอบ วิจัยและพัฒนากัญชง',
                'icon' => '🧪',
                'staff' => MjuHempResearchAndDevelopmentCenterPersonnel::all()->map(fn($item) => $this->formatData($item))
            ],
            [
                'title' => 'ฟาร์มสาธิตสำนักวิจัยฯ',
                'icon' => '🌾',
                'staff' => MjuFarmDemoPersonnel::all()->map(fn($item) => $this->formatData($item))
            ],
        ];

        return Inertia::render('About/Addmin', [
            'sections' => $sections
        ]);
    }

    private function formatData($item)
    {
        return [
            'name'     => $item->personnel_name,
            'position' => $item->position,
            'image'    => $item->image ? 'data:image/jpeg;base64,' . base64_encode($item->image) : null,
        ];
    }
}
