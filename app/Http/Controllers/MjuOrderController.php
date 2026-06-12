<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Models\MjuOrder;
use App\Models\MjuOrderItem;
use App\Models\User;
use App\Notifications\NewOrderNotification;
use App\Notifications\StockAlertNotification;
use Inertia\Inertia;

class MjuOrderController extends Controller
{
    private function notifyAdmins($notification)
    {
        $admins = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['admin', 'superadmin']);
        })->get();

        Notification::send($admins, $notification);
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:mju_shop_products,id',
            'items.*.qty' => 'required|integer|min:1',
            'shipping_address' => 'required|string',
            'customer_name' => 'required|string',
            'customer_phone' => 'required|digits:10'
        ]);

        $totalSubtotal = 0;
        $totalWeight = 0;
        $orderItemsData = [];

        foreach ($request->items as $item) {
            $product = DB::table('mju_shop_products')->where('id', $item['id'])->first();

            if (!$product) {
                return response()->json(['error' => 'ไม่พบข้อมูลสินค้า'], 404);
            }

            if ($product->stock < $item['qty']) {
                try {
                    $this->notifyAdmins(new StockAlertNotification($product->name, 'order_failed'));
                } catch (\Exception $e) {}

                return response()->json([
                    'error' => 'ขออภัย สินค้ารายการนี้หมดแล้ว ไม่สามารถดำเนินการชำระเงินได้'
                ], 400);
            }

            $subtotal = $product->price * $item['qty'];
            $totalSubtotal += $subtotal;
            $totalWeight += ($product->weight ?? 1) * $item['qty'];

            $productImage = null;
            if (!empty($product->image)) {
                $productImage = 'data:image/jpeg;base64,' . base64_encode($product->image);
            }

            $orderItemsData[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'price' => $product->price,
                'qty' => $item['qty'],
                'subtotal' => $subtotal,
                'product_image' => $productImage,
                'category' => $product->category ?? null
            ];
        }

        $rate = DB::table('mju_shipping_rates')
            ->where('weight_min', '<=', $totalWeight)
            ->where('weight_max', '>=', $totalWeight)
            ->first();

        $shippingFee = $rate ? $rate->price : 40;
        $grandTotal = $totalSubtotal + $shippingFee;

        DB::beginTransaction();
        try {
            $order = MjuOrder::create([
                'order_no' => 'ORD' . time(),
                'total' => $grandTotal,
                'shipping_fee' => $shippingFee,
                'shipping_address' => $request->shipping_address,
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'status' => 'waiting_payment'
            ]);

            foreach ($orderItemsData as $data) {
                $data['order_id'] = $order->id;
                MjuOrderItem::create($data);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'เกิดข้อผิดพลาดในการสร้างคำสั่งซื้อ'], 500);
        }

        try {
            $this->notifyAdmins(new NewOrderNotification($order));
        } catch (\Exception $e) {}

        return response()->json(MjuOrder::with('items')->find($order->id));
    }

    public function uploadSlip(Request $request, $id)
    {
        $request->validate([
            'slip' => 'required|mimes:jpeg,png,jpg|max:2048'
        ]);

        $order = MjuOrder::with('items')->findOrFail($id);

        foreach ($order->items as $item) {
            $product = DB::table('mju_shop_products')->where('id', $item->product_id)->first();
            if (!$product || $product->stock < $item->qty) {
                $order->update(['status' => 'cancelled']);
                try {
                    $this->notifyAdmins(new StockAlertNotification($item->product_name, 'order_failed'));
                } catch (\Exception $e) {}
                return response()->json([
                    'error' => 'ขออภัย สินค้ารายการนี้หมดแล้ว ไม่สามารถดำเนินการชำระเงินได้ คำสั่งซื้อถูกยกเลิก'
                ], 400);
            }
        }

        $path = $request->file('slip')->store('slips', 'public');

        $order->update([
            'slip_image' => $path,
            'status' => 'verifying'
        ]);

        return response()->json(['success' => true]);
    }

    public function index()
    {
        $orders = MjuOrder::with('items')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($orders);
    }

    public function adminIndex(Request $request)
    {
        $query = MjuOrder::with('items')->orderBy('created_at', 'desc');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate(10);

        return Inertia::render('Admin/Orders', [
            'orders' => $orders
        ]);
    }

    public function markShipped($id)
    {
        $order = MjuOrder::findOrFail($id);

        if ($order->status === 'paid') {
            $order->update(['status' => 'shipped']);
        }

        return back();
    }

    public function approvePayment($id)
    {
        $order = MjuOrder::with('items')->findOrFail($id);

        if ($order->status === 'verifying') {
            foreach ($order->items as $item) {
                $product = DB::table('mju_shop_products')->where('id', $item->product_id)->first();
                if (!$product || $product->stock < $item->qty) {
                    $order->update(['status' => 'cancelled']);
                    try {
                        $this->notifyAdmins(new StockAlertNotification($item->product_name, 'order_failed'));
                    } catch (\Exception $e) {}
                    return back()->withErrors(['error' => 'สินค้าบางรายการหมด ระบบได้ยกเลิกคำสั่งซื้อนี้แล้ว']);
                }
            }

            DB::beginTransaction();
            try {
                foreach ($order->items as $item) {
                    DB::table('mju_shop_products')->where('id', $item->product_id)->decrement('stock', $item->qty);
                    $updatedProduct = DB::table('mju_shop_products')->where('id', $item->product_id)->first();

                    if ($updatedProduct->stock <= 0) {
                        $this->notifyAdmins(new StockAlertNotification($updatedProduct->name, 'out_of_stock'));
                    } elseif ($updatedProduct->stock <= 5) {
                        $this->notifyAdmins(new StockAlertNotification($updatedProduct->name, 'low_stock'));
                    }
                }

                $order->update([
                    'status' => 'paid',
                    'updated_at' => now()
                ]);

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->withErrors(['error' => 'เกิดข้อผิดพลาดในการตัดสต็อก']);
            }
        }

        return back();
    }
}
