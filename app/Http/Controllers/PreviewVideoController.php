<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class PreviewVideoController extends Controller
{
    public function index()
    {
        // วิดีโอ 4 ช่องหลัก
        $fixed = DB::table('mju_previewvideo')
            ->whereNotNull('position')
            ->orderBy('position')
            ->get();

        // วิดีโอเสริม
        $extra = DB::table('mju_previewvideo')
            ->whereNull('position')
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'fixed' => $fixed,
            'extra' => $extra
        ]);
    }
}
