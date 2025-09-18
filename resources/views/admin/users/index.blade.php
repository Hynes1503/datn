@extends('admin.layouts.app')

@section('title', 'Quản lý người dùng')
@section('page-title', 'Danh sách người dùng')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold">Người dùng</h2>
        <a href="{{ route('admin.users.create') }}"
           class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            + Thêm người dùng
        </a>
    </div>

    @if(session('success'))
        <div class="p-3 mb-4 text-green-700 bg-green-100 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="divide-y">
        @forelse($users as $user)
            <div class="flex items-center justify-between py-3">
                <div class="flex items-center gap-3">
                    <img src="{{ $user->avatar_url }}" alt="Avatar" class="w-10 h-10 rounded-full">
                    <div>
                        <div class="font-semibold">{{ $user->name }}</div>
                        <div class="text-sm text-gray-500">{{ '@'.$user->mention }} | {{ $user->role }}</div>
                    </div>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('admin.users.edit', $user) }}"
                       class="px-3 py-1 text-sm bg-yellow-500 text-white rounded hover:bg-yellow-600">
                        Sửa
                    </a>
                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                          onsubmit="return confirm('Bạn có chắc muốn xóa người dùng này?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="px-3 py-1 text-sm bg-red-600 text-white rounded hover:bg-red-700">
                            Xóa
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-gray-500 py-3">Chưa có người dùng nào.</p>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $users->links() }}
    </div>
</div>
@endsection
