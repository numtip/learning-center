<?php

namespace App\Http\Controllers;
use App\Models\MjuPersonnel;
use Inertia\Inertia;
use Illuminate\Http\Request;

class MjuPersonnelController extends Controller
{
   public function index()
{
    $per = MjuPersonnel::latest()->first();

    return Inertia::render('Personnel', [
        'personnel' => $per ? [
            'title' => $per->title,
            'image' => $per->image
                ? 'data:image/png;base64,' . base64_encode($per->image)
                : null,
        ] : null
    ]);
}
}
