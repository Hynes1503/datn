@extends('layouts.app')

@section('title', 'Chỉnh sửa hồ sơ')

@section('content')
    <div class="max-w-2xl mx-auto bg-white rounded-2xl shadow-sm p-6">
        @if (session('success'))
            <div class="mb-6 p-4 bg-green-100 text-green-700 rounded">
                {{ session('success') }}
                @php
                    session()->forget('success'); // Clear success message after displaying
                @endphp
            </div>
        @endif

        <form action="{{ route('users.update', $user->mention) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <header class="mb-6 flex items-center gap-6">
                <div>
                    <img id="avatar-preview" src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('images/default-avatar.png') }}" alt="{{ $user->name }}"
                        class="h-20 w-20 rounded-full object-cover">
                    <input type="file" name="avatar" id="avatar" accept="image/*" class="mt-2 block text-sm">
                    @error('avatar')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between">
                        <div>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                class="text-2xl font-bold border p-2 rounded w-full mb-1">
                            @error('name')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                            <div class="flex items-center gap-2">
                                <input type="text" name="mention" value="{{ old('mention', $user->mention) }}"
                                    class="text-gray-600 border p-2 rounded w-full">
                                <input type="hidden" name="profile_visibility[mention]" value="{{ $user->profile_visibility['mention'] ?? false ? '1' : '0' }}"
                                    id="visibility-mention">
                                <i class="fa-solid {{ $user->profile_visibility['mention'] ?? false ? 'fa-eye' : 'fa-eye-slash' }} text-gray-600 cursor-pointer"
                                    data-visibility-field="mention"></i>
                            </div>
                            @error('mention')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </header>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                {{-- Ngày sinh --}}
                <div>
                    <label class="block text-sm font-medium">Ngày sinh</label>
                    <div class="flex items-center gap-2">
                        <input type="date" name="dob" value="{{ old('dob', $user->dob?->format('Y-m-d')) }}"
                            class="border p-2 rounded w-full">
                        <input type="hidden" name="profile_visibility[dob]" value="{{ $user->profile_visibility['dob'] ?? false ? '1' : '0' }}"
                            id="visibility-dob">
                        <i class="fa-solid {{ $user->profile_visibility['dob'] ?? false ? 'fa-eye' : 'fa-eye-slash' }} text-gray-600 cursor-pointer"
                            data-visibility-field="dob"></i>
                    </div>
                    @error('dob')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Lớp --}}
                <div>
                    <label class="block text-sm font-medium">Lớp</label>
                    <div class="flex items-center gap-2">
                        <input type="text" name="class" value="{{ old('class', $user->class) }}"
                            class="border p-2 rounded w-full">
                        <input type="hidden" name="profile_visibility[class]" value="{{ $user->profile_visibility['class'] ?? false ? '1' : '0' }}"
                            id="visibility-class">
                        <i class="fa-solid {{ $user->profile_visibility['class'] ?? false ? 'fa-eye' : 'fa-eye-slash' }} text-gray-600 cursor-pointer"
                            data-visibility-field="class"></i>
                    </div>
                    @error('class')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Ngành học --}}
                <div>
                    <label class="block text-sm font-medium">Ngành học</label>
                    <div class="flex items-center gap-2">
                        <input type="text" name="major" value="{{ old('major', $user->major) }}"
                            class="border p-2 rounded w-full">
                        <input type="hidden" name="profile_visibility[major]" value="{{ $user->profile_visibility['major'] ?? false ? '1' : '0' }}"
                            id="visibility-major">
                        <i class="fa-solid {{ $user->profile_visibility['major'] ?? false ? 'fa-eye' : 'fa-eye-slash' }} text-gray-600 cursor-pointer"
                            data-visibility-field="major"></i>
                    </div>
                    @error('major')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Khóa học --}}
                <div>
                    <label class="block text-sm font-medium">Khóa học</label>
                    <div class="flex items-center gap-2">
                        <input type="text" name="course" value="{{ old('course', $user->course) }}"
                            class="border p-2 rounded w-full">
                        <input type="hidden" name="profile_visibility[course]" value="{{ $user->profile_visibility['course'] ?? false ? '1' : '0' }}"
                            id="visibility-course">
                        <i class="fa-solid {{ $user->profile_visibility['course'] ?? false ? 'fa-eye' : 'fa-eye-slash' }} text-gray-600 cursor-pointer"
                            data-visibility-field="course"></i>
                    </div>
                    @error('course')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- MSSV (read-only) --}}
                <div>
                    <label class="block text-sm font-medium">Mã số sinh viên</label>
                    <div class="flex items-center gap-2">
                        <input type="text" name="student_id" value="{{ old('student_id', $user->student_id) }}"
                            class="border p-2 rounded w-full bg-gray-100" disabled>
                        <input type="hidden" name="profile_visibility[student_id]" value="{{ $user->profile_visibility['student_id'] ?? false ? '1' : '0' }}"
                            id="visibility-student_id">
                        <i class="fa-solid {{ $user->profile_visibility['student_id'] ?? false ? 'fa-eye' : 'fa-eye-slash' }} text-gray-600 cursor-pointer"
                            data-visibility-field="student_id"></i>
                    </div>
                </div>

                {{-- Email (read-only) --}}
                <div>
                    <label class="block text-sm font-medium">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                        class="border p-2 rounded w-full bg-gray-100" disabled>
                </div>
            </div>

            {{-- Followers Section --}}
            {{-- @include('layouts._followers') --}}

            {{-- Nút lưu --}}
            <div class="flex justify-center">
                <button type="submit" class="px-4 py-2 bg-black text-white rounded hover:bg-gray-700">
                    Lưu thay đổi
                </button>
            </div>
        </form>
    </div>

    <script>
        // Avatar preview
        document.getElementById('avatar').addEventListener('change', function(event) {
            const file = event.target.files[0];
            const preview = document.getElementById('avatar-preview');

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            } else {
                preview.src = "{{ $user->avatar ? asset('storage/' . $user->avatar) : '/default-avatar.png' }}";
            }
        });

        // Toggle visibility icons
        document.querySelectorAll('[data-visibility-field]').forEach(icon => {
            icon.addEventListener('click', function() {
                const field = this.getAttribute('data-visibility-field');
                const input = document.getElementById(`visibility-${field}`);
                const isPublic = input.value === '1';

                // Toggle input value
                input.value = isPublic ? '0' : '1';

                // Toggle icon
                this.classList.toggle('fa-eye', !isPublic);
                this.classList.toggle('fa-eye-slash', isPublic);
            });
        });
    </script>
@endsection
