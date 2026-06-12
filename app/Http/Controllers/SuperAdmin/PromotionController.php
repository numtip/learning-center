<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class PromotionController extends Controller
{
    public function index()
    {
        $promotions = Promotion::latest()->get();

        // 🔥 ชี้ไปที่โฟลเดอร์ SuperAdmin อย่างถูกต้องแล้ว
        return Inertia::render('SuperAdmin/Promotions/Index', [
            'promotions' => $promotions
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|max:2048', // กำหนดขนาดสูงสุด 2MB
            'status' => 'boolean',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',
        ]);

        if ($request->hasFile('image')) {
            // 🔥 ใช้วิธีอัปโหลดพื้นฐานของ Laravel (แก้ปัญหาล่ม 500)
            $path = $request->file('image')->store('promotions', 'public');
            $validated['image'] = '/storage/' . $path;
        }

        Promotion::create($validated);
        Cache::forget('active_promotions');

        return redirect()->back();
    }

    public function update(Request $request, Promotion $promotion)
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable',
            'status' => 'boolean',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',
        ]);

        if ($request->hasFile('image')) {
            // ลบรูปเก่าทิ้งก่อน (ถ้ามี)
            if ($promotion->image) {
                $oldPath = str_replace('/storage/', '', $promotion->image);
                Storage::disk('public')->delete($oldPath);
            }

            // 🔥 อัปโหลดรูปใหม่
            $path = $request->file('image')->store('promotions', 'public');
            $validated['image'] = '/storage/' . $path;
        } else {
            // ป้องกันปัญหารูปเก่าหาย
            unset($validated['image']);
        }

        $promotion->update($validated);
        Cache::forget('active_promotions');

        return redirect()->back();
    }

    public function destroy(Promotion $promotion)
    {
        // ลบรูปภาพออกจากเซิร์ฟเวอร์
        if ($promotion->image) {
            $oldPath = str_replace('/storage/', '', $promotion->image);
            Storage::disk('public')->delete($oldPath);
        }
        $promotion->delete();
        Cache::forget('active_promotions');

        return redirect()->back();
    }
}
