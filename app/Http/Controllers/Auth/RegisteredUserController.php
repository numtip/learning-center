<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\RegisterSuccessMail;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'fullname' => [
                'required','string','min:2','max:100',
                'regex:/^[A-Za-zก-๙\.\-\s]+$/u' // 🔥 เติม u (Unicode) ด้านหลังเพื่อแก้ปัญหา Regex ตรวจภาษาไทยแล้ว Error
            ],
            'organization' => 'required|string|max:255',
            'phone' => [
                'required','digits_between:9,10'
            ],
            'email' => 'required|string|lowercase|email|max:255|unique:users',
            'password' => [
                'required','confirmed','min:8',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
            ],
        ]);

        $weakPasswords = ['123456','password','12345678','qwerty'];
        if (in_array($request->password, $weakPasswords)) {
            return back()->withErrors([
                'password' => 'รหัสผ่านง่ายเกินไป'
            ]);
        }

        $plainPassword = $request->password;

        $user = User::create([
            'name' => $request->name,
            'fullname' => $request->fullname,
            'organization' => $request->organization,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // 🔥 ใส่ Try-catch เผื่อตาราง Role ใน Database ไม่มี จะได้ไม่กระทบการสมัครสมาชิก
        try {
            $memberRole = Role::where('name', 'member')->first();
            if ($memberRole) {
                $user->roles()->attach($memberRole->id);
            }
        } catch (\Exception $e) {
            \Log::error('Role Error: '.$e->getMessage());
        }

        // 🔥 ส่งเมล
        try {
            Mail::to($user->email)
                ->send(new RegisterSuccessMail($user, $plainPassword));
        } catch (\Exception $e) {
            \Log::error('Mail error: '.$e->getMessage());
        }

        event(new Registered($user));
        Auth::login($user);

        return redirect('/');
    }
}
