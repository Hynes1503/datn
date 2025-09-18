@extends('admin.layouts.app')

@section('title', 'Quản lý người dùng')
@section('page-title', 'Chỉnh sửa người dùng')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <form action="{{ route('admin.users.update', $user) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block mb-1 font-medium">Tên</label>
            <input type="text" name="name" class="w-full border rounded p-2" value="{{ old('name', $user->name) }}">
            @error('name') <div class="text-red-500 text-sm">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="block mb-1 font-medium">Email</label>
            <input type="email" name="email" class="w-full border rounded p-2" value="{{ old('email', $user->email) }}">
            @error('email') <div class="text-red-500 text-sm">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="block mb-1 font-medium">Mật khẩu (để trống nếu không đổi)</label>
            <input type="password" name="password" class="w-full border rounded p-2">
            @error('password') <div class="text-red-500 text-sm">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="block mb-1 font-medium">Xác nhận mật khẩu</label>
            <input type="password" name="password_confirmation" class="w-full border rounded p-2">
        </div>

        <div>
            <label class="block mb-1 font-medium">Vai trò</label>
            <select name="role" class="w-full border rounded p-2">
                <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User</option>
                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
            @error('role') <div class="text-red-500 text-sm">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="block mb-1 font-medium">Ảnh đại diện</label>
            <input type="file" name="avatar" class="w-full border rounded p-2">
            @if($user->avatar_url)
                <img src="{{ $user->avatar_url }}" alt="Avatar" class="w-16 h-16 rounded-full mt-2">
            @endif
        </div>

        <div>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Cập nhật
            </button>
            <a href="{{ route('admin.users.index') }}" class="ml-2 text-gray-600 hover:underline">Hủy</a>
        </div>
    </form>
</div>
@endsection
