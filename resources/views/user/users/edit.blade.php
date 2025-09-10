@extends('layouts.app')

@section('title', 'Chỉnh sửa hồ sơ')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-2xl shadow p-6">
    <h1 class="text-2xl font-bold mb-6">Chỉnh sửa hồ sơ</h1>

    @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('users.update', $user->mention) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Avatar --}}
        <div class="mb-4">
            <label class="block text-sm font-medium">Ảnh đại diện</label>
            <input type="file" name="avatar" id="avatar" accept="image/*" class="mt-2">
            @error('avatar')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
            <div class="mt-2">
                @if($user->avatar)
                    <img id="avatar-preview" src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" class="h-16 w-16 rounded-full object-cover">
                @else
                    <img id="avatar-preview" src="" alt="Avatar Preview" class="h-16 w-16 rounded-full object-cover hidden">
                @endif
            </div>
        </div>

        {{-- Name --}}
        <div class="mb-4">
            <label class="block text-sm font-medium">Họ và tên</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" 
                   class="border p-2 rounded w-full">
            @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Mention --}}
        <div class="mb-4">
            <label class="block text-sm font-medium">Mention</label>
            <input type="text" name="mention" value="{{ old('mention', $user->mention) }}" 
                   class="border p-2 rounded w-full">
            @error('mention')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
            <label class="inline-flex items-center mt-2">
                <input type="checkbox" name="profile_visibility[mention]" value="1"
                    {{ ($user->profile_visibility['mention'] ?? false) ? 'checked' : '' }}>
                <span class="ml-2 text-sm text-gray-600">Hiển thị công khai</span>
            </label>
        </div>

        {{-- Ngày sinh --}}
        <div class="mb-4">
            <label class="block text-sm font-medium">Ngày sinh</label>
            <input type="date" name="dob" value="{{ old('dob', $user->dob?->format('Y-m-d')) }}" 
                   class="border p-2 rounded w-full">
            @error('dob')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
            <label class="inline-flex items-center mt-2">
                <input type="checkbox" name="profile_visibility[dob]" value="1"
                    {{ ($user->profile_visibility['dob'] ?? false) ? 'checked' : '' }}>
                <span class="ml-2 text-sm text-gray-600">Hiển thị công khai</span>
            </label>
        </div>

        {{-- Lớp --}}
        <div class="mb-4">
            <label class="block text-sm font-medium">Lớp</label>
            <input type="text" name="class" value="{{ old('class', $user->class) }}" 
                   class="border p-2 rounded w-full">
            @error('class')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
            <label class="inline-flex items-center mt-2">
                <input type="checkbox" name="profile_visibility[class]" value="1"
                    {{ ($user->profile_visibility['class'] ?? false) ? 'checked' : '' }}>
                <span class="ml-2 text-sm text-gray-600">Hiển thị công khai</span>
            </label>
        </div>

        {{-- Ngành học --}}
        <div class="mb-4">
            <label class="block text-sm font-medium">Ngành học</label>
            <input type="text" name="major" value="{{ old('major', $user->major) }}" 
                   class="border p-2 rounded w-full">
            @error('major')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
            <label class="inline-flex items-center mt-2">
                <input type="checkbox" name="profile_visibility[major]" value="1"
                    {{ ($user->profile_visibility['major'] ?? false) ? 'checked' : '' }}>
                <span class="ml-2 text-sm text-gray-600">Hiển thị công khai</span>
            </label>
        </div>

        {{-- Khóa học --}}
        <div class="mb-4">
            <label class="block text-sm font-medium">Khóa học</label>
            <input type="text" name="course" value="{{ old('course', $user->course) }}" 
                   class="border p-2 rounded w-full">
            @error('course')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
            <label class="inline-flex items-center mt-2">
                <input type="checkbox" name="profile_visibility[course]" value="1"
                    {{ ($user->profile_visibility['course'] ?? false) ? 'checked' : '' }}>
                <span class="ml-2 text-sm text-gray-600">Hiển thị công khai</span>
            </label>
        </div>

        {{-- MSSV (read-only) --}}
        <div class="mb-4">
            <label class="block text-sm font-medium">Mã số sinh viên</label>
            <input type="text" name="student_id" value="{{ old('student_id', $user->student_id) }}" 
                   class="border p-2 rounded w-full bg-gray-100" disabled>
            <label class="inline-flex items-center mt-2">
                <input type="checkbox" name="profile_visibility[student_id]" value="1"
                    {{ ($user->profile_visibility['student_id'] ?? false) ? 'checked' : '' }}>
                <span class="ml-2 text-sm text-gray-600">Hiển thị công khai</span>
            </label>
        </div>

        {{-- Email (read-only) --}}
        <div class="mb-4">
            <label class="block text-sm font-medium">Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" 
                   class="border p-2 rounded w-full bg-gray-100" disabled>
        </div>

        {{-- Nút lưu --}}
        <div class="flex justify-end">
            <button type="submit" 
                    class="px-4 py-2 bg-black text-white rounded hover:bg-gray-700">
                Lưu thay đổi
            </button>
        </div>
    </form>
</div>

<script>
    document.getElementById('avatar').addEventListener('change', function(event) {
        const file = event.target.files[0];
        const preview = document.getElementById('avatar-preview');
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            // If no file is selected, revert to default or hide preview
            @if($user->avatar)
                preview.src = "{{ asset('storage/' . $user->avatar) }}";
            @else
                preview.src = "";
                preview.classList.add('hidden');
            @endif
        }
    });
</script>
@endsection