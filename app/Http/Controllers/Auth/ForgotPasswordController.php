<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    public function showForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
        ], [
            'login.required' => 'Vui lòng nhập email hoặc MSSV',
        ]);

        $input = $request->input('login');
        $user = null;

        if (filter_var($input, FILTER_VALIDATE_EMAIL)) {
            $user = User::where('email', $input)->first();
        } else {
            $user = User::where('student_id', $input)->first();
        }

        if (!$user) {
            return back()->withErrors(['login' => 'Không tìm thấy tài khoản phù hợp']);
        }

        $status = Password::sendResetLink(['email' => $user->email]);

        if ($status === Password::RESET_LINK_SENT) {
            $email = $user->email;
            $parts = explode('@', $email);
            $namePart = $parts[0];
            $domainPart = $parts[1];

            if (strlen($namePart) > 4) {
                $hiddenName = substr($namePart, 0, 2)
                    . str_repeat('*', strlen($namePart) - 4)
                    . substr($namePart, -2);
            } else {
                $hiddenName = substr($namePart, 0, 1) . str_repeat('*', max(1, strlen($namePart) - 1));
            }

            $maskedEmail = $hiddenName . '@' . $domainPart;

            return back()->with('status', "Hệ thống đã gửi link đặt lại mật khẩu tới email: {$maskedEmail}");
        }

        return back()->withErrors(['login' => __($status)]);
    }

    public function showResetForm(Request $request, $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }
}
