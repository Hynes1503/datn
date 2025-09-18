@extends('admin.layouts.app')

@section('title', 'thêm tầng')
@section('page-title', 'Thêm tầng tòa nhà')

@section('content')
<div class="max-w-lg mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-xl font-bold mb-4">Thêm Tầng</h2>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-2 rounded mb-3">{{ session('success') }}</div>
    @endif

    <form action="{{ route('admin.floors.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="block text-sm">Thuộc Tòa</label>
            <select name="building_id" class="w-full border p-2 rounded" required>
                <option value="">-- Chọn Tòa --</option>
                @foreach($buildings as $building)
                    <option value="{{ $building->id }}">{{ $building->name }} ({{ $building->code }})</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="block text-sm">Số tầng</label>
            <input type="number" name="floor_number" class="w-full border p-2 rounded" required>
        </div>

        <div class="mb-3">
            <label class="block text-sm">Tên tầng (VD: Tầng 1, Tầng 2)</label>
            <input type="text" name="name" class="w-full border p-2 rounded">
        </div>

        <div class="mb-3">
            <label class="block text-sm">Mô tả</label>
            <textarea name="description" class="w-full border p-2 rounded"></textarea>
        </div>

        <button type="submit" class="bg-black text-white px-4 py-2 rounded">Lưu</button>
    </form>
</div>
@endsection
