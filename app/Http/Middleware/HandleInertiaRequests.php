<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
public function share(Request $request): array
    {
        $assetBase = rtrim((string) (config('app.asset_url') ?: config('app.url')), '/');

        return array_merge(parent::share($request), [
            'assetBase' => $assetBase,
            'logoUrl' => asset('HeroSection/06.png'),
            'auth' => [
                'user' => $request->user(),
            ],
            // 🔔 ดึงแจ้งเตือน 10 รายการล่าสุดสำหรับ Admin ทุกระดับ
            'notifications' => fn () => $request->user()
                ? $request->user()->notifications()->latest()->take(10)->get()->map(function($n) {
                    return [
                        'id' => $n->id,
                        'data' => $n->data,
                        'read_at' => $n->read_at,
                        'created_at' => $n->created_at->diffForHumans(),
                    ];
                })
                : [],
            'unreadCount' => fn () => $request->user()
                ? $request->user()->unreadNotifications()->count()
                : 0,
        ]);
    }
}
