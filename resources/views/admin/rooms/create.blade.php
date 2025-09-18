@extends('admin.layouts.app')

@section('title', 'thêm phòng học')
@section('page-title', 'Quản lý phòng học')

@section('content')
    <div class="max-w-lg mx-auto bg-white p-6 rounded shadow">
        <h2 class="text-xl font-bold mb-4">Thêm Phòng</h2>

        @if (session('success'))
            <div class="bg-green-100 text-green-700 p-2 rounded mb-3">{{ session('success') }}</div>
        @endif

        <form action="{{ route('admin.rooms.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="block text-sm">Thuộc Tầng</label>
                <select name="floor_id" class="w-full border p-2 rounded" required>
                    <option value="">-- Chọn Tầng --</option>
                    @foreach ($buildings as $building)
                        <optgroup label="{{ $building->name }}">
                            @foreach ($building->floors as $floor)
                                <option value="{{ $floor->id }}">
                                    {{ $floor->name ?? 'Tầng ' . $floor->floor_number }}
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>


            <div class="mb-3">
                <label class="block text-sm">Số phòng</label>
                <input type="text" name="room_number" class="w-full border p-2 rounded" required>
            </div>

            <div class="mb-3">
                <label class="block text-sm">Tên phòng</label>
                <input type="text" name="name" class="w-full border p-2 rounded">
            </div>

            <div class="mb-3">
                <label class="block text-sm">Sức chứa</label>
                <input type="number" name="capacity" class="w-full border p-2 rounded">
            </div>

            <div class="mb-3">
                <label class="block text-sm">Loại phòng</label>
                <input type="text" name="type" class="w-full border p-2 rounded"
                    placeholder="VD: Học lý thuyết, Thí nghiệm...">
            </div>

            <div class="mb-3">
                <label class="block text-sm">Mô tả</label>
                <textarea name="description" class="w-full border p-2 rounded"></textarea>
            </div>

            <button type="submit" class="bg-black text-white px-4 py-2 rounded">Lưu</button>
        </form>
    </div>
@endsection
