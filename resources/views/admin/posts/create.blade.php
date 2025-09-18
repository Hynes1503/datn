@extends('admin.layouts.app')

@section('title', 'Create Post')
@section('page-title', 'Create New Post')

@section('content')
<style>
    /* CSS for form styling */
    .post-images {
        display: inline-flex;
        gap: 8px;
        max-width: 100%;
        overflow-x: auto;
    }

    .image-box {
        flex: 0 0 auto;
        background-color: #f3f3f3;
        border-radius: 10px;
        padding: 4px;
    }

    .post-image {
        border: 1px solid rgba(0, 0, 0, 0.2);
        border-radius: 8px;
        max-height: 150px;
        object-fit: cover;
    }

    .post-video {
        border: 1px solid rgba(0, 0, 0, 0.2);
        border-radius: 8px;
        max-height: 150px;
        object-fit: cover;
    }
</style>

<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Create New Post</h2>
        <a href="{{ route('admin.posts.index') }}" 
           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200">
            <i class="fa-solid fa-arrow-left mr-1"></i> Back to Posts
        </a>
    </div>

    <div class="bg-white shadow-md rounded-lg p-6 border-l-4 border-black">
        <form action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="space-y-4">
                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" 
                           class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500" 
                           required>
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Content -->
                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700">Content</label>
                    <textarea name="content" id="content" rows="5" 
                              class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('content') }}</textarea>
                    @error('content')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Hashtags -->
                <div>
                    <label for="hashtag" class="block text-sm font-medium text-gray-700">Hashtags (comma-separated)</label>
                    <input type="text" name="hashtag" id="hashtag" value="{{ old('hashtag') }}" 
                           class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @error('hashtag')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Room -->
                <div>
                    <label for="room_id" class="block text-sm font-medium text-gray-700">Room</label>
                    <select name="room_id" id="room_id" 
                            class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select a room</option>
                        @foreach ($rooms as $room)
                            <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                {{ $room->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('room_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Building -->
                <div>
                    <label for="building_id" class="block text-sm font-medium text-gray-700">Building</label>
                    <select name="building_id" id="building_id" 
                            class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select a building</option>
                        @foreach ($buildings as $building)
                            <option value="{{ $building->id }}" {{ old('building_id') == $building->id ? 'selected' : '' }}>
                                {{ $building->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('building_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Media Upload -->
                <div>
                    <label for="media" class="block text-sm font-medium text-gray-700">Add Media</label>
                    <input type="file" name="media[]" id="media" multiple 
                           accept="image/*,video/mp4" 
                           class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @error('media.*')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="mt-6">
                    <button type="submit" 
                            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition duration-200">
                        <i class="fa-solid fa-save mr-1"></i> Create Post
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection