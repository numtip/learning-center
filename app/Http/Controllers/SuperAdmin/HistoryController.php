<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\MjuHistory;

class HistoryController extends Controller
{
    public function index()
    {
        $history = MjuHistory::orderBy('year')->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'year' => $item->year,
                'title' => $item->title,
                'description' => $item->description,
                'imageUrl' => $item->image
                    ? 'data:image/jpeg;base64,' . base64_encode($item->image)
                    : null,
                'imageAlt' => $item->image_alt,
            ];
        });

        return Inertia::render('SuperAdmin/History/Index', [
            'historyData' => $history
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'year' => 'required',
            'title' => 'required',
            'description' => 'required',
            'image' => 'nullable|image'
        ]);

        $data = $request->only(['year','title','description','image_alt']);

        if ($request->hasFile('image')) {
            $data['image'] = file_get_contents($request->file('image'));
        }

        MjuHistory::create($data);

        return redirect()->back();
    }

    public function destroy($id)
    {
        MjuHistory::destroy($id);
        return redirect()->back();
    }
    public function update(Request $request, $id)
{
    $request->validate([
        'year' => 'required',
        'title' => 'required',
        'description' => 'required',
        'image' => 'nullable|image'
    ]);

    $history = MjuHistory::findOrFail($id);

    $data = $request->only(['year','title','description','image_alt']);

    if ($request->hasFile('image')) {
        $data['image'] = file_get_contents($request->file('image'));
    }

    $history->update($data);

    return redirect()->back();
}

}
