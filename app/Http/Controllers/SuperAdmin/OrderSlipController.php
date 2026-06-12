<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class OrderSlipController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('mju_orders')
            ->select('id', 'order_no', 'slip_image', 'status', 'created_at', 'tracking_no')
            ->orderByDesc('created_at');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        return Inertia::render('SuperAdmin/OrderSlips/Index', [
            'orders' => $query->paginate(10)
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $order = DB::table('mju_orders')->where('id', $id)->first();

        if (!$order) {
            return back()->withErrors(['error' => 'ไม่พบข้อมูลออเดอร์']);
        }

        $validTransitions = [
            'waiting_payment' => ['verifying', 'cancelled'],
            'verifying' => ['paid'],
            'paid' => ['shipped']
        ];

        if (isset($validTransitions[$order->status]) && in_array($request->status, $validTransitions[$order->status])) {
            DB::table('mju_orders')->where('id', $id)->update([
                'status' => $request->status,
                'tracking_no' => $request->tracking_no,
                'updated_at' => now(),
            ]);
        }

        return back();
    }

    public function updateTracking(Request $request, $id)
    {
        $request->validate([
            'tracking_no' => 'required|string|min:6|max:100'
        ]);

        $order = DB::table('mju_orders')->where('id', $id)->first();

        if ($order && $order->status === 'paid') {
            DB::table('mju_orders')->where('id', $id)->update([
                'tracking_no' => $request->tracking_no,
                'status' => 'shipped',
                'updated_at' => now()
            ]);
            return response()->json(['success' => true]);
        }

        return response()->json(['error' => 'Order is not paid or not found'], 400);
    }

    // --- ฟังก์ชันสำหรับอัปโหลด/แก้ไขสลิปแทนลูกค้า ---
    public function updateSlip(Request $request, $id)
    {
        // ตรวจสอบความถูกต้องของไฟล์รูปภาพ
        $request->validate([
            'slip_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // รองรับสูงสุด 5MB
        ]);

        $order = DB::table('mju_orders')->where('id', $id)->first();

        if (!$order) {
            return back()->withErrors(['error' => 'ไม่พบข้อมูลออเดอร์']);
        }

        if ($request->hasFile('slip_image')) {
            // ลบสลิปเก่าออก (ถ้ามี) เพื่อไม่ให้เปลืองพื้นที่ Server
            if ($order->slip_image && Storage::disk('public')->exists($order->slip_image)) {
                Storage::disk('public')->delete($order->slip_image);
            }

            // อัปโหลดไฟล์ใหม่ไปที่โฟลเดอร์ storage/app/public/slips
            $path = $request->file('slip_image')->store('slips', 'public');

            // อัปเดตข้อมูลลงฐานข้อมูล และเปลี่ยนสถานะกลับมาเป็น 'รอตรวจสอบ'
            DB::table('mju_orders')->where('id', $id)->update([
                'slip_image' => $path,
                'status' => 'verifying',
                'updated_at' => now(),
            ]);

            return back()->with('success', 'อัปเดตสลิปสำเร็จ');
        }

        return back()->withErrors(['slip_image' => 'ไม่สามารถอัปโหลดไฟล์ได้']);
    }
}
