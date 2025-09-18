{{-- resources/views/admin/posts/edit.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Sửa bài viết')
@section('page-title', 'Sửa bài viết')

@section('content')
<div class="bg-white shadow rounded-lg p-6">
    @if ($errors->any())
        <div class="bg-red-100 text-red-700 p-3 rounded-lg mb-4">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.posts.update', $post) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block mb-1 font-semibold">Tiêu đề</label>
            <input type="text" name="title" value="{{ old('title', $post->title) }}"
                   class="w-full border rounded-lg p-2" required>
        </div>

        <div>
            <label class="block mb-1 font-semibold">Nội dung</label>
            <textarea name="content" rows="6" class="w-full border rounded-lg p-2" required>{{ old('content', $post->content) }}</textarea>
        </div>

        <div>
            <label class="block mb-1 font-semibold">Người đăng</label>
            <select name="user_id" class="w-full border rounded-lg p-2" required>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" @selected(old('user_id', $post->user_id)==$user->id)>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block mb-1 font-semibold">Tòa nhà</label>
                <select name="building_id" class="w-full border rounded-lg p-2">
                    <option value="">-- Không chọn --</option>
                    @foreach($buildings as $building)
                        <option value="{{ $building->id }}" @selected(old('building_id', $post->building_id)==$building->id)>
                            {{ $building->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block mb-1 font-semibold">Phòng</label>
                <select name="room_id" class="w-full border rounded-lg p-2">
                    <option value="">-- Không chọn --</option>
                    @foreach($rooms as $room)
                        <option value="{{ $room->id }}" @selected(old('room_id', $post->room_id)==$room->id)>
                            {{ $room->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label class="block mb-1 font-semibold">Hashtag</label>
            <input type="text" name="hashtag" value="{{ old('hashtag', $post->hashtag) }}"
                   class="w-full border rounded-lg p-2">
        </div>

        <div class="flex items-center space-x-2">
            <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Cập nhật
            </button>
            <a href="{{ route('admin.posts.index') }}" class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">
                Hủy
            </a>
        </div>
    </form>
</div>
@endsection
