@extends('admin.layouts.app')

@section('title', 'Manage Posts')
@section('page-title', 'Posts')

@section('content')
<style>
    /* CSS for media gallery */
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

    /* CSS for dropdown menu */
    .dropdown-menu {
        display: none;
        position: absolute;
        right: 8px;
        top: 50%;
        transform: translateY(-50%);
        background-color: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        min-width: 120px;
        z-index: 10;
    }

    .dropdown-menu.show {
        display: block;
    }

    .dropdown-menu a,
    .dropdown-menu button {
        display: block;
        padding: 8px 16px;
        color: #4b5563;
        text-decoration: none;
        background: none;
        border: none;
        width: 100%;
        text-align: left;
        cursor: pointer;
    }

    .dropdown-menu a:hover,
    .dropdown-menu button:hover {
        background-color: #f3f3f3;
    }

    .dropdown-menu button.text-red-500:hover {
        background-color: #fee2e2;
    }
</style>

<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">All Posts</h2>
        <a href="{{ route('admin.posts.create') }}" 
           class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition duration-200">
            Create New Post
        </a>
    </div>

    <div class="space-y-4">
        @forelse ($posts as $post)
        <div class="bg-white shadow-md rounded-lg p-6 border-l-4 border-black relative">
            <div class="flex items-start space-x-4">
                <div class="flex-1">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900">{{ $post->title }}</h3>
                        <span class="text-sm text-gray-500">{{ $post->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="mt-2 text-gray-600">{{ Str::limit($post->content, 150) }}</p>
                    <div class="mt-3 flex space-x-4 text-sm text-gray-500">
                        <span>Hashtags: {{ $post->hashtag ?? 'None' }}</span>
                        <span>Likes: {{ $post->likes_count }}</span>
                        <span>Shares: {{ $post->shares }}</span>
                        <span>Views: {{ $post->views }}</span>
                    </div>
                    @if ($post->room)
                        <div class="mt-2 text-sm text-gray-500">
                            Room: {{ $post->room->name ?? 'N/A' }}
                        </div>
                    @endif
                    @if ($post->building)
                        <div class="mt-1 text-sm text-gray-500">
                            Building: {{ $post->building->name ?? 'N/A' }}
                        </div>
                    @endif
                </div>
            </div>
            @if ($post->media && $post->media->count())
                <div class="mt-4">
                    <div class="post-images">
                        @foreach ($post->media as $m)
                            <div class="image-box">
                                @if ($m->media_type === 'image')
                                    <img src="{{ asset('storage/' . $m->media_path) }}" alt="Ảnh bài viết"
                                         class="post-image" loading="lazy">
                                @elseif ($m->media_type === 'video')
                                    <video controls class="post-video">
                                        <source src="{{ asset('storage/' . $m->media_path) }}" type="video/mp4">
                                        Trình duyệt không hỗ trợ video.
                                    </video>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            <!-- Dropdown Trigger Icon -->
            <div class="absolute top-1/2 right-2 transform -translate-y-1/2">
                <button class="text-gray-600 hover:text-gray-800 focus:outline-none text-2xl" onclick="toggleDropdown('dropdown-{{ $post->slug }}')">
                    <i class="fa-solid fa-caret-left"></i>
                </button>
                <!-- Dropdown Menu -->
                <div id="dropdown-{{ $post->slug }}" class="dropdown-menu">
                    <a href="{{ route('admin.posts.show', $post->slug) }}" 
                       class="flex items-center text-sm">
                        <i class="fa-solid fa-eye mr-2"></i> View
                    </a>
                    <a href="{{ route('admin.posts.edit', $post->slug) }}" 
                       class="flex items-center text-sm">
                        <i class="fa-solid fa-pen-to-square mr-2"></i> Edit
                    </a>
                    <form action="{{ route('admin.posts.destroy', $post->slug) }}" method="POST" 
                          class="inline-block" onsubmit="return confirm('Are you sure you want to delete this post?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="flex items-center text-sm text-red-500">
                            <i class="fa-solid fa-trash mr-2"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white shadow-md rounded-lg p-6 text-center text-gray-500">
            No posts found.
        </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $posts->links('vendor.pagination.tailwind') }}
    </div>
</div>

<script>
function toggleDropdown(id) {
    const dropdown = document.getElementById(id);
    dropdown.classList.toggle('show');

    // Close other open dropdowns
    document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
        if (menu.id !== id) {
            menu.classList.remove('show');
        }
    });
}

// Close dropdown when clicking outside
document.addEventListener('click', (e) => {
    if (!e.target.closest('.absolute')) {
        document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
            menu.classList.remove('show');
        });
    }
});
</script>
@endsection