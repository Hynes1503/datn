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
        // Validate input
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required'],
        ]);

        // Determine if login input is email or student_id
        $field = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'student_id';

        // Attempt to authenticate
        if (Auth::attempt([$field => $request->login, 'password' => $request->password])) {
            // Regenerate session to prevent session fixation
            $request->session()->regenerate();

            // Redirect to the authenticated user's profile
            return redirect()->route('home')->with('success', 'Đăng nhập thành công!');
        }

        // Return to login form with error message
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
