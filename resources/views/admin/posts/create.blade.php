@extends('admin.layouts.app')

@section('title', 'Create Post')
@section('page-title', 'Create New Post')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <form action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf

        {{-- Title --}}
        <div>
            <label class="block mb-1 font-medium">Title</label>
            <input type="text" name="title" id="title" value="{{ old('title') }}" 
                   class="w-full border rounded p-2" required>
            @error('title')
                <div class="text-red-500 text-sm">{{ $message }}</div>
            @enderror
        </div>

        {{-- Content --}}
        <div>
            <label class="block mb-1 font-medium">Content</label>
            <textarea name="content" id="content" rows="5" 
                      class="w-full border rounded p-2">{{ old('content') }}</textarea>
            @error('content')
                <div class="text-red-500 text-sm">{{ $message }}</div>
            @enderror
        </div>

        {{-- Hashtags --}}
        <div>
            <label class="block mb-1 font-medium">Hashtags (comma-separated, e.g., tag1,tag2)</label>
            <input type="text" name="hashtag" id="hashtag" value="{{ old('hashtag') }}" 
                   class="w-full border rounded p-2" placeholder="e.g., tag1,tag2">
            @error('hashtag')
                <div class="text-red-500 text-sm">{{ $message }}</div>
            @enderror
        </div>

        {{-- Room --}}
        <div>
            <label class="block mb-1 font-medium">Room</label>
            <select name="room_id" id="room_id" class="w-full border rounded p-2">
                <option value="">-- Select Room --</option>
                @foreach ($buildings as $building)
                    <optgroup label="{{ $building->name }}">
                        @foreach ($building->floors as $floor)
                            @foreach ($floor->rooms as $room)
                                <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                    {{ $floor->name ?? 'Floor ' . $floor->floor_number }} - {{ $room->name ?? $room->room_number }}
                                </option>
                            @endforeach
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
            @error('room_id')
                <div class="text-red-500 text-sm">{{ $message }}</div>
            @enderror
        </div>

        {{-- Media Upload --}}
        <div>
            <label class="block mb-1 font-medium">Add Images or Videos</label>
            <input type="file" name="media[]" id="media" multiple 
                   accept="image/jpeg,image/png,image/gif,image/webp,video/mp4,video/avi,video/mov,video/webm" 
                   class="w-full border rounded p-2">
            <p class="mt-1 text-sm text-gray-500">Supported formats: JPG, PNG, GIF, WebP, MP4, AVI, MOV, WebM (max 50MB each)</p>
            @error('media.*')
                <div class="text-red-500 text-sm">{{ $message }}</div>
            @enderror
        </div>

        {{-- Media Preview --}}
        <div id="media-preview" class="flex gap-2 overflow-x-auto"></div>

        {{-- Buttons --}}
        <div>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                <i class="fa-solid fa-save mr-1"></i> Create Post
            </button>
            <a href="{{ route('admin.posts.index') }}" class="ml-2 text-gray-600 hover:underline">Cancel</a>
        </div>
    </form>
</div>

<script>
    // JavaScript for media preview
    document.getElementById('media').addEventListener('change', function(event) {
        const preview = document.getElementById('media-preview');
        preview.innerHTML = ''; // Clear previous previews
        const files = event.target.files;

        for (const file of files) {
            const fileType = file.type.split('/')[0];
            const box = document.createElement('div');
            box.className = 'flex-shrink-0 bg-gray-100 rounded-lg p-1';

            if (fileType === 'image') {
                const img = document.createElement('img');
                img.className = 'h-24 rounded border border-gray-300 object-cover';
                img.src = URL.createObjectURL(file);
                box.appendChild(img);
            } else if (fileType === 'video') {
                const video = document.createElement('video');
                video.className = 'h-24 rounded border border-gray-300 object-cover';
                video.src = URL.createObjectURL(file);
                video.controls = true;
                box.appendChild(video);
            }

            preview.appendChild(box);
        }
    });
</script>
@endsection