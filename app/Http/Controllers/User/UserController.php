<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Models\Post;
use App\Models\Report;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->paginate(10);
        return view('user.users.index', compact('users'));
    }

    public function create()
    {
        return view('user.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'dob' => $request->dob,
            'class' => $request->class,
            'major' => $request->major,
            'course' => $request->course,
            'student_id' => $request->student_id,
            'password' => Hash::make($request->password),
            'mention' => $request->mention,
        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully');
    }
    public function show($mention)
    {
        $user = User::where('mention', $mention)->firstOrFail();

        // Nếu là admin => lấy tất cả bài viết
        if (Auth::check() && Auth::user()->role === 'Admin') {
            $posts = $user->posts()->with(['images'])->latest()->get();
        } else {
            // Người thường => ẩn bài viết có report status = 'resolved'
            $posts = $user->posts()
                ->whereDoesntHave('reports', function ($query) {
                    $query->where('status', 'resolved');
                })
                ->with(['images'])
                ->latest()
                ->get();
        }

        return view('user.users.show', compact('user', 'posts'));
    }


    public function edit($mention)
    {
        $user = User::where('mention', $mention)->firstOrFail();
        return view('user.users.edit', compact('user'));
    }

    public function update(Request $request, $mention)
    {
        $user = User::where('mention', $mention)->firstOrFail();

        if (Auth::id() !== $user->id && Auth::user()->role !== 'Admin') {
            abort(403, 'Bạn không có quyền chỉnh sửa người dùng này.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'mention' => 'nullable|string|unique:users,mention,' . $user->id,
            'dob' => 'nullable|date',
            'class' => 'nullable|string|max:255',
            'major' => 'nullable|string|max:255',
            'course' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'profile_visibility.dob' => 'nullable|boolean',
            'profile_visibility.class' => 'nullable|boolean',
            'profile_visibility.major' => 'nullable|boolean',
            'profile_visibility.course' => 'nullable|boolean',
            'profile_visibility.student_id' => 'nullable|boolean',
        ]);

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $path;
        }

        if ($request->name !== $user->name) {
            $validated['mention'] = $request->mention;
        }

        $validated['profile_visibility'] = [
            'dob' => $request->input('profile_visibility.dob', false),
            'mention' => $request->input('profile_visibility.mention', false),
            'class' => $request->input('profile_visibility.class', false),
            'major' => $request->input('profile_visibility.major', false),
            'course' => $request->input('profile_visibility.course', false),
            'student_id' => $request->input('profile_visibility.student_id', false),
        ];

        $user->update($validated);

        return redirect()->route('users.show', $user->mention)
            ->with('success', 'Cập nhật thông tin thành công!');
    }


    public function destroy($mention)
    {
        $user = User::where('mention', $mention)->firstOrFail();
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully');
    }
}
