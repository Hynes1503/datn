<form action="{{ route('posts.like', [$post->user->mention, $post->slug]) }}" method="POST"
    class="like-form flex items-center gap-2">
    @csrf
    <button type="submit" class="focus:outline-none">
        @if (auth()->check() && $post->isLikedBy(auth()->user()))
            <i class="fa-solid fa-heart text-red-500 like-icon"></i>
        @else
            <i class="fa-regular fa-heart text-gray-500 like-icon"></i>
        @endif
    </button>
    <span class="like-count">{{ $post->likes()->count() }}</span>
</form>
