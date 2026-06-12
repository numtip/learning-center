<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index()
    {
        $products = DB::table('mju_shop_products')->get()->map(function ($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'size' => $p->size,
                'unit' => $p->unit,
                'price' => $p->price,
                'stock' => $p->stock,
                'description' => $p->description,
                'category' => $p->category,
                'weight' => $p->weight,
                'image' => $p->image ? base64_encode($p->image) : null,
            ];
        });

        return Inertia::render('SuperAdmin/Products/Index', [
            'products' => $products
        ]);
    }

    public function create()
    {
        return Inertia::render('SuperAdmin/Products/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'weight' => 'required|numeric'
        ]);

        $imageBlob = null;

        if ($request->hasFile('image')) {
            $imageBlob = file_get_contents($request->file('image')->getRealPath());
        }

        DB::table('mju_shop_products')->insert([
            'name' => $request->name,
            'size' => $request->size,
            'unit' => $request->unit,
            'price' => $request->price,
            'stock' => $request->stock,
            'description' => $request->description,
            'category' => $request->category,
            'weight' => $request->weight,
            'image' => $imageBlob,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect('/admin/products');
    }

    public function edit($id)
    {
        $product = DB::table('mju_shop_products')->where('id', $id)->first();
        if (!$product) abort(404);

        $imagePreview = null;

        if ($product->image) {
            $imagePreview = 'data:image/jpeg;base64,' . base64_encode($product->image);
        }

        return Inertia::render('SuperAdmin/Products/Edit', [
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'size' => $product->size,
                'unit' => $product->unit,
                'price' => $product->price,
                'stock' => $product->stock,
                'description' => $product->description,
                'category' => $product->category,
                'weight' => $product->weight,
                'image_preview' => $imagePreview
            ]
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'weight' => 'required|numeric'
        ]);

        $data = [
            'name' => $request->name,
            'size' => $request->size,
            'unit' => $request->unit,
            'price' => $request->price,
            'stock' => $request->stock,
            'description' => $request->description,
            'category' => $request->category,
            'weight' => $request->weight,
            'updated_at' => now(),
        ];

        if ($request->hasFile('image')) {
            $data['image'] = file_get_contents($request->file('image')->getRealPath());
        }

        DB::table('mju_shop_products')
            ->where('id', $id)
            ->update($data);

        return redirect('/admin/products');
    }

    public function destroy($id)
    {
        DB::table('mju_shop_products')->where('id', $id)->delete();
        return redirect('/admin/products');
    }
}
