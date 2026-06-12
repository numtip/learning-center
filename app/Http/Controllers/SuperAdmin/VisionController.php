<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MjuVision;
use Inertia\Inertia;

class VisionController extends Controller
{
    public function index()
    {
        $visions = MjuVision::all()->map(function ($item) {
            return [
                'id' => $item->id,
                'title' => $item->title,
                'description' => $item->description,
                'image' => $item->image
                    ? 'data:image/jpeg;base64,' . base64_encode($item->image)
                    : null,
            ];
        });

        return Inertia::render('SuperAdmin/Vision/Index', [
            'visions' => $visions
        ]);
    }

    public function store(Request $request)
    {
        $image = $request->file('image')
            ? file_get_contents($request->file('image'))
            : null;

        MjuVision::create([
            'title' => $request->title,
            'description' => $request->description,
            'image' => $image
        ]);

        return redirect()->back();
    }

    public function update(Request $request, $id)
    {
        $vision = MjuVision::findOrFail($id);

        if ($request->hasFile('image')) {
            $vision->image = file_get_contents($request->file('image'));
        }

        $vision->title = $request->title;
        $vision->description = $request->description;
        $vision->save();

        return redirect()->back();
    }

    public function destroy($id)
    {
        MjuVision::findOrFail($id)->delete();
        return redirect()->back();
    }
}
