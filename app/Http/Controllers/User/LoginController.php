<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required'],
        ]);

        $field = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'student_id';

        if (Auth::attempt([$field => $request->login, 'password' => $request->password])) {
            $request->session()->regenerate();

            return redirect()->route('home')->with('success', 'Đăng nhập thành công!');
        }

        return back()->withErrors([
            'login' => 'Email hoặc MSSV hoặc mật khẩu không đúng.',
        ])->onlyInput('login');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Đăng xuất thành công!');
    }
}
