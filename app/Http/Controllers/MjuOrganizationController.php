<?php

namespace App\Http\Controllers;

use App\Models\MjuOrganization;
use Inertia\Inertia;

class MjuOrganizationController extends Controller
{
    public function index()
    {
        $org = MjuOrganization::latest()->first();

        return Inertia::render('About/Structure', [
            'image' => $org && $org->image
                ? 'data:image/png;base64,' . base64_encode($org->image)
                : null
        ]);
    }
}
