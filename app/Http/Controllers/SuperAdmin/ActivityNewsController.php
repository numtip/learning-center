<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ActivityNewsController extends Controller
{
    public function index()
    {
        $news = DB::table('mju_activity_news')
            ->orderByDesc('id')
            ->get()
            ->map(function ($row) {
                return [
                    'id' => $row->id,
                    'title' => $row->title,
                    'description' => $row->description,
                    'image1' => $row->image1 ? 'data:image/jpeg;base64,' . base64_encode($row->image1) : null,
                ];
            });

        return Inertia::render('SuperAdmin/ActivityManager', [
            'news' => $news
        ]);
    }

    public function store(Request $request)
    {
        DB::table('mju_activity_news')->insert([
            'title' => $request->title,
            'description' => $request->description,
            'image1' => $request->file('image1')? file_get_contents($request->file('image1')):null,
            'image2' => $request->file('image2')? file_get_contents($request->file('image2')):null,
            'image3' => $request->file('image3')? file_get_contents($request->file('image3')):null,
            'image4' => $request->file('image4')? file_get_contents($request->file('image4')):null,
            'image5' => $request->file('image5')? file_get_contents($request->file('image5')):null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back();
    }

    public function update(Request $request, $id)
    {
        $data = [
            'title' => $request->title,
            'description' => $request->description,
            'updated_at' => now()
        ];

        foreach(['image1','image2','image3','image4','image5'] as $img){
            if($request->file($img)){
                $data[$img] = file_get_contents($request->file($img));
            }
        }

        DB::table('mju_activity_news')->where('id',$id)->update($data);
        return back();
    }

    public function destroy($id)
    {
        DB::table('mju_activity_news')->where('id',$id)->delete();
        return back();
    }
}
