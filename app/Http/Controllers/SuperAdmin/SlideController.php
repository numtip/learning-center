<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class SlideController extends Controller
{
    public function index()
    {
        $slides = DB::table('mju_previewimage')
            ->orderBy('id')
            ->get()
            ->map(function ($row) {
                return [
                    'id' => $row->id,
                    'title' => $row->title,
                    'image' => 'data:image/jpeg;base64,' . base64_encode($row->image),
                ];
            });

        return Inertia::render('SuperAdmin/HomeSlides', [
            'slides' => $slides
        ]);
    }

    public function store(Request $request)
    {
        $binary = file_get_contents($request->file('image')->getRealPath());

        DB::table('mju_previewimage')->insert([
            'title' => $request->title,
            'image' => $binary,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return back();
    }

    public function destroy($id)
    {
        DB::table('mju_previewimage')->where('id', $id)->delete();
        return back();
    }
}
