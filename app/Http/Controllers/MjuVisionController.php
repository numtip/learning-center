<?php

namespace App\Http\Controllers;

use App\Models\MjuVision;
use Inertia\Inertia;

class MjuVisionController extends Controller
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

        return Inertia::render('About/Vision', [
            'visions' => $visions,
        ]);
    }
}
