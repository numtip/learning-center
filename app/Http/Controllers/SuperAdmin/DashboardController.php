<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Carbon\Carbon; // 🔥 นำเข้า Carbon เพื่อจัดการวันที่

class DashboardController extends Controller
{
    public function index()
    {
        // ===============================
        // จำนวนผู้ใช้ / ออเดอร์ / การจอง
        // ===============================
        $totalUsers = DB::table('users')->count();
        $totalOrders = DB::table('mju_orders')->count();
        $totalBookings = DB::table('center_bookings')->count();

        // ===============================
        // 🌟 (ข้อ 25) สถิติ: ผู้เข้าชมวันนี้
        // คำนวณจาก visit_date = today และสถานะเป็น อนุมัติ หรือ เสร็จสิ้น
        // ===============================
        $todayVisitors = DB::table('center_bookings')
            ->whereDate('visit_date', Carbon::today())
            ->whereIn('status', ['อนุมัติ', 'เสร็จสิ้น'])
            ->sum('people');

        // ===============================
        // รายได้จากการขายสินค้า (แยกตามศูนย์)
        // ===============================
        $centerIncomeRaw = DB::table('mju_order_items as oi')
            ->join('mju_orders as o', 'oi.order_id', '=', 'o.id')
            ->whereIn('o.status', ['paid','shipped'])
            ->select(
                'oi.category',
                DB::raw('SUM(oi.qty * oi.price) as total_income')
            )
            ->groupBy('oi.category')
            ->get();

        $centerIncome = [];

        foreach ($centerIncomeRaw as $row) {
            // ตัดคำว่า "สินค้าของ" และตัดช่องว่างหน้า-หลังออก
            $centerName = trim(str_replace('สินค้าของ', '', $row->category));

            $centerIncome[] = [
                'center_name' => $centerName,
                'total_income' => (float) $row->total_income
            ];
        }

        // ===============================
        // 🌟 (ข้อ 3) สถิติการจองกิจกรรมและรายได้
        // 📌 แก้ไขให้นับเฉพาะสถานะ "เสร็จสิ้น" เท่านั้น
        // ===============================
        $rows = DB::table('center_bookings')
            ->where('status', 'เสร็จสิ้น') // 🔥 เปลี่ยนจาก ['อนุมัติ','เสร็จสิ้น'] เป็น 'เสร็จสิ้น'
            ->get();

        $labels = [];
        $people = [];
        $counts = [];
        $totalIncome = 0;

        $map = [];

        foreach ($rows as $row) {
            $centers = json_decode($row->centers, true);

            if (!is_array($centers)) {
                continue;
            }

            foreach ($centers as $center) {
                // เผื่อมีช่องว่างในชื่อศูนย์
                $centerTrimmed = trim($center);

                if (!isset($map[$centerTrimmed])) {
                    $map[$centerTrimmed] = [
                        'people' => 0,
                        'count' => 0
                    ];
                }

                $map[$centerTrimmed]['people'] += (int) $row->people;
                $map[$centerTrimmed]['count'] += 1;
            }

            $totalIncome += (float) $row->total_price;
        }

        foreach ($map as $centerName => $data) {
            $labels[] = $centerName;
            $people[] = $data['people'];
            $counts[] = $data['count'];
        }

        // ===============================
        // Monthly (สำรองไว้ใช้กราฟ)
        // ===============================
        $monthly = [
            'labels' => [1,2,3,4,5,6,7,8,9,10,11,12],
            'orders' => array_fill(0,12,0),
            'bookings' => array_fill(0,12,0),
        ];

        // ===============================
        // ส่งข้อมูลไป Vue
        // ===============================
        return Inertia::render('SuperAdmin/Dashboard', [
            'totalUsers'    => $totalUsers,
            'totalOrders'   => $totalOrders,
            'totalBookings' => $totalBookings,
            'todayVisitors' => (int) $todayVisitors, 
            'monthly'       => $monthly,
            'centerIncome'  => $centerIncome,
            'bookingStats'  => [
                'labels' => $labels,
                'people' => $people,
                'counts' => $counts,
                'totalIncome' => $totalIncome
            ]
        ]);
    }
}
