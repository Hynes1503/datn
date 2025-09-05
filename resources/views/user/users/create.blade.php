@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-md p-4">
    <h2 class="text-xl font-bold mb-4">Tạo User mới</h2>

    <form action="{{ route('users.store') }}" method="POST" class="space-y-4">
        @csrf
        <input type="text" name="name" placeholder="Name" class="w-full border rounded p-2">
        <input type="email" name="email" placeholder="Email" class="w-full border rounded p-2">
        <input type="password" name="password" placeholder="Password" class="w-full border rounded p-2">

        <input type="text" name="dob" placeholder="Date of Birth" class="w-full border rounded p-2">
        <input type="text" name="class" placeholder="Class" class="w-full border rounded p-2">
        <input type="text" name="major" placeholder="Major" class="w-full border rounded p-2">
        <input type="text" name="course" placeholder="Course" class="w-full border rounded p-2">
        <input type="text" name="student_id" placeholder="Student ID" class="w-full border rounded p-2">

        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg">Lưu</button>
    </form>
</div>
@endsection
