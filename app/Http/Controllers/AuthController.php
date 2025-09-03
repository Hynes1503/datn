<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Hiển thị form đăng ký
    public function showRegister()
    {
        return view('auth.register');
    }

    // Xử lý đăng ký
    public function register(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'dob'        => 'required|date',
            'class'      => 'required|string|max:50',
            'major'      => 'required|string|max:255',
            'course'     => 'required|string|max:50',
            'student_id' => 'required|string|unique:users,student_id',
            'email'      => 'required|email|unique:users,email|regex:/@epu\.edu\.vn$/',
            'password'   => 'required|min:6|confirmed',
            'avatar'     => 'nullable|image|max:2048',
        ]);

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
        }

        $user = User::create([
            'name'       => $request->name,
            'dob'        => $request->dob,
            'class'      => $request->class,
            'major'      => $request->major,
            'course'     => $request->course,
            'student_id' => $request->student_id,
            'avatar'     => $avatarPath,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
        ]);

        Auth::login($user);

        return redirect()->route('home')->with('success', 'Đăng ký thành công!');
    }

    // Hiển thị form đăng nhập
    public function showLogin()
    {
        return view('auth.login');
    }

    // Xử lý đăng nhập
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'student_id' => 'required|string',
            'password'   => 'required|string',
        ]);

        if (Auth::attempt(['student_id' => $credentials['student_id'], 'password' => $credentials['password']])) {
            $request->session()->regenerate();
            return redirect()->route('home');
        }

        return back()->withErrors([
            'student_id' => 'MSSV hoặc mật khẩu không chính xác.',
        ]);
    }

    // Đăng xuất
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
