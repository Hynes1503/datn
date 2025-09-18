<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserController extends Controller
{
    // Danh sách user
    public function index()
    {
        $users = User::latest()->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    // Form thêm user
    public function create()
    {
        return view('admin.users.create');
    }

    // Lưu user mới
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'dob'      => 'nullable|date',
            'class'    => 'nullable|string|max:255',
            'major'    => 'nullable|string|max:255',
            'course'   => 'nullable|string|max:255',
            'student_id' => 'nullable|string|max:50',
            'role'     => 'required|string',
            'avatar'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->only([
            'name',
            'dob',
            'class',
            'major',
            'course',
            'student_id',
            'email',
            'role',
        ]);
        $data['password'] = Hash::make($request->password);

        // Generate mention thủ công
        $baseMention = Str::slug($data['name']);
        do {
            $mention = $baseMention . rand(100, 999);
        } while (User::where('mention', $mention)->exists());
        $data['mention'] = $mention;

        // Upload avatar nếu có
        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        User::create($data);

        return redirect()->route('admin.users.index')->with('success', 'Thêm người dùng thành công!');
    }

    // Xem chi tiết user
    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    // Form sửa user
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    // Cập nhật user
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'   => 'required|string|max:255',
            'email'  => 'required|email|unique:users,email,' . $user->id,
            'role'   => 'required|string',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->only([
            'name',
            'mention',
            'dob',
            'class',
            'major',
            'course',
            'student_id',
            'email',
            'role',
        ]);

        // nếu có đổi mật khẩu
        if ($request->filled('password')) {
            $request->validate([
                'password' => 'min:6|confirmed',
            ]);
            $data['password'] = Hash::make($request->password);
        }

        // nếu có upload avatar mới
        if ($request->hasFile('avatar')) {
            // xóa avatar cũ
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'Cập nhật người dùng thành công!');
    }

    // Xóa user
    public function destroy(User $user)
    {
        // Xóa avatar nếu có
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Xóa người dùng thành công!');
    }
}
