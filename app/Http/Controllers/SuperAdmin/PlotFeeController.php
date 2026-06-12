<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PlotFeeController extends Controller
{
    public function index()
    {
        return Inertia::render('SuperAdmin/PlotFees/Index', [
            'fees' => DB::table('mju_demo_plot_fees')
                ->orderBy('topic')
                ->orderBy('title')
                ->get()
        ]);
    }

    public function store(Request $request)
    {
        DB::table('mju_demo_plot_fees')->insert(
            $request->only([
                'topic','title',
                'detail1','price1',
                'detail2','price2',
                'detail3','price3',
                'detail4','price4',
            ])
        );

        return back();
    }

    public function update(Request $request, $id)
    {
        DB::table('mju_demo_plot_fees')
            ->where('id', $id)
            ->update(
                $request->only([
                    'topic','title',
                    'detail1','price1',
                    'detail2','price2',
                    'detail3','price3',
                    'detail4','price4',
                ])
            );

        return back();
    }

    public function destroy($id)
    {
        DB::table('mju_demo_plot_fees')->where('id', $id)->delete();
        return back();
    }
}
