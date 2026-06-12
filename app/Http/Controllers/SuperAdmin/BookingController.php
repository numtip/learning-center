<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\CenterBooking;
use App\Models\MjuCalendarBlock; // 🔥 เพิ่มบรรทัดนี้
use Carbon\Carbon; // 🔥 เพิ่มบรรทัดนี้

class BookingController extends Controller
{
    public function index()
    {
        $bookings = CenterBooking::orderBy('created_at','desc')->get();
        return Inertia::render('SuperAdmin/Booking', [
            'bookings' => $bookings
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $booking = CenterBooking::findOrFail($id);
        $booking->status = $request->status;
        $booking->save();
        return back();
    }

    public function edit($id)
    {
        $booking = CenterBooking::findOrFail($id);
        return Inertia::render('SuperAdmin/BookingEdit', [
            'booking' => $booking
        ]);
    }

    public function update(Request $request, $id)
    {
        $booking = CenterBooking::findOrFail($id);
        $booking->update($request->all());
        return redirect('/admin/booking');
    }

    public function destroy($id)
    {
        CenterBooking::where('id',$id)->delete();
        return back();
    }

    public function calendar()
    {
        $allEvents = [];

        // 1. ดึงข้อมูลการจองเข้าชม (ประเภท 'booking')
        $bookings = CenterBooking::whereNotNull('visit_date')->get();
        foreach ($bookings as $b) {
            $allEvents[] = [
                'id' => $b->id,
                'type' => 'booking',
                'name' => $b->name,
                'people' => $b->people,
                'org' => $b->org,
                'phone' => $b->phone,
                'visit_date' => Carbon::parse($b->visit_date)->format('Y-m-d'), // บังคับ Format
                'status' => $b->status,
            ];
        }

        // 2. ดึงข้อมูลวันหยุด และ คิวเต็ม มารวมใน Array เดียวกัน
        $blocks = MjuCalendarBlock::all();
        foreach ($blocks as $block) {
            $allEvents[] = [
                'id' => 'block_' . $block->id, // สร้าง ID ชั่วคราวให้ Vue ใช้แสดงผล
                'type' => $block->reason, // ค่าจะเป็น 'full' หรือ 'holiday'
                'name' => $block->reason === 'holiday' ? 'วันหยุด' : 'คิวเต็ม',
                'visit_date' => Carbon::parse($block->blocked_date)->format('Y-m-d'), // บังคับ Format
            ];
        }

        return Inertia::render('SuperAdmin/BookingCalendar', [
            'bookings' => collect($allEvents)->toArray() // ส่งก้อนเดียว จบปิ๊ง!
        ]);
    }

    public function toggleBlockDate(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:full,holiday'
        ]);

        $exists = MjuCalendarBlock::where('blocked_date', $request->date)->first();

        if ($exists) {
            if ($exists->reason === $request->type) {
                $exists->delete(); // ถ้ากดซ้ำสถานะเดิม = ปลดล็อค
            } else {
                $exists->update(['reason' => $request->type]); // ถ้ากดเปลี่ยนสถานะ
            }
        } else {
            MjuCalendarBlock::create([
                'blocked_date' => $request->date,
                'reason' => $request->type
            ]);
        }
        return redirect()->back();
    }
}
