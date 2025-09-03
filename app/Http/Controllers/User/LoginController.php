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
        // Xác thực dữ liệu đầu vào
        $credentials = $request->validate([
            'login' => ['required', 'string'], // Đảm bảo trường là 'login'
            'password' => ['required'],
        ]);

        // Kiểm tra xem giá trị nhập vào là email hay MSSV
        $field = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'student_id';

        // Tìm user theo email hoặc MSSV
        $user = User::where($field, $request->login)->first();

        // Kiểm tra user tồn tại và mật khẩu đúng
        if ($user && Auth::attempt([$field => $request->login, 'password' => $request->password])) {
            // Tái tạo session
            $request->session()->regenerate();

            // Chuyển hướng đến trang home
            return redirect()->route('home')->with('success', 'Đăng nhập thành công!');
        }

        // Nếu đăng nhập thất bại
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