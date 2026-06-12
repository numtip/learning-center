<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ShopController extends Controller
{
    public function products()
    {
        $products = DB::table('mju_shop_products')->get()->map(function ($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'size' => $p->size,     // ✅ เพิ่มบรรทัดนี้ เพื่อส่งปริมาณ (size) ไปให้ Vue
                'unit' => $p->unit,
                'price' => $p->price,
                'stock' => $p->stock,
                'description' => $p->description,
                'category' => $p->category,

                // ✅ ใช้ weight จาก DB โดยตรง
                'weight' => $p->weight ?? 1,

                'image' => $p->image
                    ? 'data:image/jpeg;base64,' . base64_encode($p->image)
                    : null,
            ];
        });

        $menuList = $products->groupBy('category')->map(function ($items, $cat) {
            return [
                'category' => $cat,
                'items' => $items->values()
            ];
        })->values();

        $promos = DB::table('mju_shop_promotion')->get()->map(function ($p) {
            return [
                'title' => $p->title,
                'image' => $p->image
                    ? 'data:image/jpeg;base64,' . base64_encode($p->image)
                    : null
            ];
        });

        return Inertia::render('Service/Products', [
            'menuList' => $menuList,
            'promoBanners' => $promos
        ]);
    }
}
