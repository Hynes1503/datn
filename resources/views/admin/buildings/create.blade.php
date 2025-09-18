@extends('admin.layouts.app')

@section('title', 'thêm tòa nhà')
@section('page-title', 'Thêm tòa nhà')

@section('content')
<div class="max-w-lg mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-xl font-bold mb-4">Thêm Tòa</h2>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-2 rounded mb-3">{{ session('success') }}</div>
    @endif

    <form action="{{ route('admin.buildings.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="block text-sm">Tên tòa</label>
            <input type="text" name="name" class="w-full border p-2 rounded" required>
        </div>

        <div class="mb-3">
            <label class="block text-sm">Mã tòa (VD: A, B, C)</label>
            <input type="text" name="code" class="w-full border p-2 rounded">
        </div>

        <div class="mb-3">
            <label class="block text-sm">Mô tả</label>
            <textarea name="description" class="w-full border p-2 rounded"></textarea>
        </div>

        <button type="submit" class="bg-black text-white px-4 py-2 rounded">Lưu</button>
    </form>
</div>
@endsection
