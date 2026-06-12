<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\CenterBooking;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = CenterBooking::orderBy('created_at', 'desc')->get();

        return Inertia::render('SuperAdmin/Booking', [
            'bookings' => $bookings
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string'
        ]);

        $booking = CenterBooking::findOrFail($id);
        $booking->status = $request->status;
        $booking->save();

        return back()->with('success', 'อัปเดตสถานะเรียบร้อย');
    }
}
