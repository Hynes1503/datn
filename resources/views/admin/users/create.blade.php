@extends('admin.layouts.app')

@section('title', 'Quản lý người dùng')
@section('page-title', 'Thêm người dùng')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf

        {{-- Tên --}}
        <div>
            <label class="block mb-1 font-medium">Tên</label>
            <input type="text" name="name" class="w-full border rounded p-2" value="{{ old('name') }}">
            @error('name') <div class="text-red-500 text-sm">{{ $message }}</div> @enderror
        </div>

        {{-- Email --}}
        <div>
            <label class="block mb-1 font-medium">Email</label>
            <input type="email" name="email" class="w-full border rounded p-2" value="{{ old('email') }}">
            @error('email') <div class="text-red-500 text-sm">{{ $message }}</div> @enderror
        </div>

        {{-- Mật khẩu --}}
        <div>
            <label class="block mb-1 font-medium">Mật khẩu</label>
            <input type="password" name="password" class="w-full border rounded p-2">
            @error('password') <div class="text-red-500 text-sm">{{ $message }}</div> @enderror
        </div>

        {{-- Xác nhận mật khẩu --}}
        <div>
            <label class="block mb-1 font-medium">Xác nhận mật khẩu</label>
            <input type="password" name="password_confirmation" class="w-full border rounded p-2">
        </div>

        {{-- Ngày sinh --}}
        <div>
            <label class="block mb-1 font-medium">Ngày sinh</label>
            <input type="date" name="dob" class="w-full border rounded p-2" value="{{ old('dob') }}">
            @error('dob') <div class="text-red-500 text-sm">{{ $message }}</div> @enderror
        </div>

        {{-- Lớp --}}
        <div>
            <label class="block mb-1 font-medium">Lớp</label>
            <input type="text" name="class" class="w-full border rounded p-2" value="{{ old('class') }}">
            @error('class') <div class="text-red-500 text-sm">{{ $message }}</div> @enderror
        </div>

        {{-- Ngành --}}
        <div>
            <label class="block mb-1 font-medium">Ngành</label>
            <input type="text" name="major" class="w-full border rounded p-2" value="{{ old('major') }}">
            @error('major') <div class="text-red-500 text-sm">{{ $message }}</div> @enderror
        </div>

        {{-- Khóa học --}}
        <div>
            <label class="block mb-1 font-medium">Khóa học</label>
            <input type="text" name="course" class="w-full border rounded p-2" value="{{ old('course') }}">
            @error('course') <div class="text-red-500 text-sm">{{ $message }}</div> @enderror
        </div>

        {{-- MSSV --}}
        <div>
            <label class="block mb-1 font-medium">Mã số sinh viên</label>
            <input type="text" name="student_id" class="w-full border rounded p-2" value="{{ old('student_id') }}">
            @error('student_id') <div class="text-red-500 text-sm">{{ $message }}</div> @enderror
        </div>

        {{-- Vai trò --}}
        <div>
            <label class="block mb-1 font-medium">Vai trò</label>
            <select name="role" class="w-full border rounded p-2">
                <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User</option>
                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
            @error('role') <div class="text-red-500 text-sm">{{ $message }}</div> @enderror
        </div>

        {{-- Ảnh đại diện --}}
        <div>
            <label class="block mb-1 font-medium">Ảnh đại diện</label>
            <input type="file" name="avatar" class="w-full border rounded p-2">
            @error('avatar') <div class="text-red-500 text-sm">{{ $message }}</div> @enderror
        </div>

        {{-- Cài đặt hiển thị hồ sơ --}}
        {{-- <div>
            <label class="block mb-1 font-medium">Hiển thị hồ sơ</label>
            <select name="profile_visibility[]" multiple class="w-full border rounded p-2">
                <option value="public" {{ in_array('public', old('profile_visibility', [])) ? 'selected' : '' }}>Công khai</option>
                <option value="friends" {{ in_array('friends', old('profile_visibility', [])) ? 'selected' : '' }}>Bạn bè</option>
                <option value="private" {{ in_array('private', old('profile_visibility', [])) ? 'selected' : '' }}>Riêng tư</option>
            </select>
            @error('profile_visibility') <div class="text-red-500 text-sm">{{ $message }}</div> @enderror
        </div> --}}

        {{-- Nút --}}
        <div>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Lưu
            </button>
            <a href="{{ route('admin.users.index') }}" class="ml-2 text-gray-600 hover:underline">Hủy</a>
        </div>
    </form>
</div>
@endsection
