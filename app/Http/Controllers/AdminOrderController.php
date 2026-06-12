<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index()
    {
        // ดึงออเดอร์ทั้งหมดพร้อมรายการสินค้า
        $orders = DB::table('mju_orders')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($order) {
                $items = DB::table('mju_order_items')
                    ->where('order_id', $order->id)
                    ->get();

                $order->items = $items;
                return $order;
            });

        // สรุปรายได้แยกตามศูนย์ + รวมเลขออเดอร์
        $summaryByCenter = DB::select("
            SELECT
                p.category AS center_name,
                SUM(oi.qty * oi.price) AS total_amount,
                GROUP_CONCAT(DISTINCT o.order_no ORDER BY o.order_no SEPARATOR ', ') AS order_numbers
            FROM mju_orders o
            JOIN mju_order_items oi ON o.id = oi.order_id
            JOIN mju_shop_products p ON oi.product_id = p.id
            WHERE o.status IN ('paid','shipped')
            GROUP BY p.category
        ");

        return Inertia::render('Admin/Orders', [
            'orders' => $orders,
            'summaryByCenter' => $summaryByCenter
        ]);
    }

    public function markShipped($id)
    {
        DB::table('mju_orders')
            ->where('id', $id)
            ->update([
                'status' => 'shipped',
                'updated_at' => now()
            ]);

        return redirect()->back();
    }
}
