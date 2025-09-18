@extends('admin.layouts.app')

@section('title', 'Quản lý người dùng')
@section('page-title', 'Chi tiết người dùng')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex items-center gap-4">
        <img src="{{ $user->avatar_url }}" alt="Avatar" class="w-20 h-20 rounded-full">
        <div>
            <h2 class="text-xl font-semibold">{{ $user->name }}</h2>
            <p class="text-gray-500">{{ '@'.$user->mention }}</p>
            <p>Email: {{ $user->email }}</p>
            <p>Role: {{ $user->role }}</p>
        </div>
    </div>

    <div class="mt-6">
        <a href="{{ route('admin.users.edit', $user) }}" class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">
            Sửa
        </a>
        <a href="{{ route('admin.users.index') }}" class="ml-2 text-gray-600 hover:underline">Quay lại</a>
    </div>
</div>
@endsection
