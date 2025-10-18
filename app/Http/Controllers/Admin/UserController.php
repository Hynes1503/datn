<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }
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

        $baseMention = Str::slug($data['name']);
        do {
            $mention = $baseMention . rand(100, 999);
        } while (User::where('mention', $mention)->exists());
        $data['mention'] = $mention;

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        User::create($data);

        return redirect()->route('admin.users.index')->with('success', 'Thêm người dùng thành công!');
    }

    public function show(User $user)
    {
        $posts = $user->posts()
            ->with('media', 'room')
            ->orderBy('created_at', 'desc')
            ->paginate(10); // Paginate with 10 posts per page
        $report = Report::where('reportable_id', $user->id)
            ->where('reportable_type', User::class)
            ->orderBy('created_at', 'desc')
            ->first();
        return view('admin.users.show', compact('user', 'posts', 'report'));
    }
    public function updateStatus(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:pending,reviewed,resolved,rejected',
        ]);

        $user = User::findOrFail($request->user_id);
        $admin = Auth::user();

        if (!$admin || $admin->role !== 'Admin') {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền thực hiện hành động này.'
            ], 403);
        }

        $report = Report::where('reportable_id', $user->id)
            ->where('reportable_type', User::class)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($report) {
            $report->update(['status' => $request->status]);
        } else {
            Report::create([
                'reporter_id' => $admin->id,
                'reportable_id' => $user->id,
                'reportable_type' => User::class,
                'reason' => 'Tạo ra bởi admin',
                'status' => $request->status,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật trạng thái người dùng thành công.'
        ]);
    }
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

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

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'min:6|confirmed',
            ]);
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'Cập nhật người dùng thành công!');
    }

    public function destroy(User $user)
    {
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Xóa người dùng thành công!');
    }
}
