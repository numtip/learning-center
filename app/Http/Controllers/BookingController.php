<?php

namespace App\Http\Controllers;

use App\Models\CenterBooking;
use App\Models\User;
use App\Models\MjuCalendarBlock;
use App\Notifications\NewBookingNotification;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // ตารางกิจกรรมของแต่ละศูนย์
        $centerTables = [
            'ศูนย์เรียนรู้วัฒนธรรมเกษตรล้านนา' => 'mju_culture_agriculture_center_activities',
            'ศูนย์การเรียนรู้เกษตรทฤษฎีใหม่' => 'mju_agriculture_theory_activities',
            'ศูนย์เรียนรู้พัฒนาบ้านโปง' => 'mju_banpong_activities',
            'ศูนย์ทดสอบและพัฒนากัญชง' => 'mju_hemp_research_and_development_center_activities',
            'ฟาร์มสาธิตสำนักวิจัยฯ' => 'mju_farm_demo_activities',
        ];

        $activitiesByCenter = [];
        foreach ($centerTables as $centerName => $table) {
            if (Schema::hasTable($table)) {
                $activities = DB::table($table)->pluck('activity_name')->toArray();
                $activitiesByCenter[$centerName] = $activities;
            } else {
                $activitiesByCenter[$centerName] = [];
            }
        }

        if ($user && ($user->hasRole('admin') || $user->hasRole('superadmin'))) {
            $bookings = CenterBooking::orderBy('created_at', 'desc')->get();
        } else {
            $bookings = CenterBooking::where('user_id', $user->id ?? 0)->orderBy('created_at', 'desc')->get();
        }

        $userData = $user ? [
            'name' => $user->name, 'fullname' => $user->fullname,
            'organization' => $user->organization, 'email' => $user->email, 'phone' => $user->phone,
        ] : null;

        // ดึงข้อมูลวันหยุดและคิวเต็ม
        $blockedDates = [];
        if (Schema::hasTable('mju_calendar_blocks')) {
            $blocks = MjuCalendarBlock::get(['blocked_date', 'reason']);
            foreach ($blocks as $block) {
                $dateString = Carbon::parse($block->blocked_date)->format('Y-m-d');
                $blockedDates[$dateString] = $block->reason;
            }
        }

        $plotFees = [];
        if (Schema::hasTable('mju_demo_plot_fees')) {
            $plotFees = DB::table('mju_demo_plot_fees')->get();
        }

        return Inertia::render('Service/Booking', [
            'bookings' => $bookings,
            'centers' => array_keys($centerTables),
            'activitiesByCenter' => $activitiesByCenter,
            'userData' => $userData,
            'bookedDates' => (object)$blockedDates,
            'plotFees' => $plotFees
        ]);
    }

    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'org' => 'nullable|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|numeric|digits_between:9,10',
            'visit_date' => 'required|date|after_or_equal:today', // ดักห้ามจองอดีต
            'centers' => 'required|array|min:1',
            'activities' => 'required|array',
            'level' => 'required|string',
            'people' => 'required|integer|min:1|max:200', // จำนวนคนห้ามมั่ว
            'package' => 'required|string'
        ], [
            'visit_date.after_or_equal' => 'ไม่สามารถจองวันที่ในอดีตได้',
            'phone.digits_between' => 'เบอร์โทรศัพท์ต้องเป็นตัวเลข 9-10 หลัก',
            'people.min' => 'จำนวนคนต้องมีอย่างน้อย 1 คน',
            'people.max' => 'รับจำนวนสูงสุดไม่เกิน 200 คนต่อกลุ่ม',
        ]);


        $userId = Auth::id();
        $visitDate = $request->visit_date;

        // 2. ดัก User จองซ้ำในวันเดียวกัน
        $existingBooking = CenterBooking::where('user_id', $userId)
            ->where('visit_date', $visitDate)
            ->whereIn('status', ['รอยืนยัน', 'อนุมัติ'])
            ->first();

        if ($existingBooking) {
            return back()->withErrors(['message' => 'คุณมีคิวจองในวันที่เลือกอยู่แล้ว ไม่สามารถจองซ้ำได้']);
        }


        $isBlocked = MjuCalendarBlock::where('blocked_date', $visitDate)->first();
        if ($isBlocked) {
            return back()->withErrors(['message' => 'วันที่คุณเลือกถูกปิดรับจองหรือเป็นวันหยุดไปแล้ว']);
        }


        $currentPeopleCount = CenterBooking::where('visit_date', $visitDate)
            ->whereIn('status', ['รอยืนยัน', 'อนุมัติ'])
            ->sum('people');

        if (($currentPeopleCount + $request->people) > 200) {
            $available = 200 - $currentPeopleCount;
            return back()->withErrors(['message' => "จำนวนคนเกินโควต้าที่รับได้ในวันนี้ (เหลือคิวว่าง $available คน)"]);
        }

        // 5. คัดกรองและตรวจสอบความถูกต้องของศูนย์และกิจกรรม
        $cleanActivities = [];
        foreach ($request->centers as $center) {
            // เช็คว่าศูนย์ที่เลือก มีการเลือกกิจกรรมมาด้วยหรือไม่
            if (!isset($request->activities[$center]) || count($request->activities[$center]) === 0) {
                return back()->withErrors(['message' => "คุณไม่ได้เลือกกิจกรรมสำหรับศูนย์: $center"]);
            }
            // ตัดกิจกรรมซ้ำทิ้ง (ป้องกันผู้ใช้แก้ไข Payload)
            $cleanActivities[$center] = array_values(array_unique($request->activities[$center]));
        }

        // คำนวณราคา
        $rateTable = [
            'nofood' => ['primary' => 20, 'secondary' => 30, 'public' => 50, 'smallgroup' => 100],
            'food' => ['primary' => 255, 'secondary' => 295, 'public' => 350, 'smallgroup' => 450]
        ];

        $package = $request->input('package', 'nofood');
        $level = $request->input('level');
        $pricePerPerson = $rateTable[$package][$level] ?? 0;
        $centerCount = count($request->centers);
        $totalPrice = $pricePerPerson * $request->people * $centerCount;
        $booking = CenterBooking::create([
            'user_id' => $userId,
            'name' => $request->name,
            'org' => $request->org,
            'email' => $request->email,
            'phone' => $request->phone,
            'visit_date' => $visitDate,
            'centers' => $request->centers,
            'activities' => $cleanActivities,
            'level' => $level,
            'people' => $request->people,
            'food_package' => $package,
            'price_per_person' => $pricePerPerson,
            'total_price' => $totalPrice,
            'status' => 'รอยืนยัน'
        ]);

        // ส่งแจ้งเตือนไปยัง Admin (สามารถต่อยอดเป็น Email ได้ในอนาคต)
        try {
            $admins = User::whereHas('roles', function($q) {
                $q->whereIn('name', ['admin', 'superadmin']);
            })->get();

            foreach ($admins as $admin) {
                $admin->notify(new NewBookingNotification());
            }
        } catch (\Exception $e) {
            \Log::error('Booking Notification Error: ' . $e->getMessage());
        }

        return redirect()->back();
    }

    public function updateStatus(Request $request, $id)
    {
        $booking = CenterBooking::findOrFail($id);
        $booking->status = $request->status;
        $booking->save();

        return redirect()->back();
    }

    // ฟังก์ชันใหม่: สำหรับผู้ใช้ยกเลิกการจอง หรือ แอดมินลบการจองทิ้ง
    public function destroy($id)
    {
        $booking = CenterBooking::findOrFail($id);
        $user = Auth::user();

        if ($user) {
            if ($user->hasRole('admin') || $user->hasRole('superadmin')) {
                // ถ้าเป็น Admin ให้ลบข้อมูลออกจากฐานข้อมูลเลย
                $booking->delete();
            } else if ($user->id === $booking->user_id) {
                // ถ้าเป็น User ธรรมดา และจองเป็นของตัวเอง
                if ($booking->status === 'รอยืนยัน') {
                    // อนุญาตให้ยกเลิกได้แค่ตอนที่ยังไม่โดนอนุมัติ
                    $booking->status = 'ยกเลิก';
                    $booking->save();
                } else {
                    return back()->withErrors(['message' => 'ไม่สามารถยกเลิกการจองที่ได้รับการอนุมัติไปแล้วได้']);
                }
            }
        }

        return redirect()->back();
    }

    public function history()
    {
        $user = Auth::user();

        if ($user && ($user->hasRole('admin') || $user->hasRole('superadmin'))) {
            $bookings = CenterBooking::orderBy('created_at', 'desc')->get();
        } else {
            $bookings = CenterBooking::where('user_id', $user->id ?? 0)->orderBy('created_at', 'desc')->get();
        }

        return Inertia::render('Service/History', [
            'bookings' => $bookings
        ]);
    }
}
