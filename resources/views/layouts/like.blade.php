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

<style>
    /* Tim bay */
    .floating-heart {
        position: absolute;
        font-size: 1.2rem;
        color: #ef4444; /* đỏ-500 */
        animation: floatUp 1s ease-out forwards;
        pointer-events: none;
    }

    @keyframes floatUp {
        0% {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
        50% {
            opacity: 0.8;
            transform: translateY(-30px) scale(1.3);
        }
        100% {
            opacity: 0;
            transform: translateY(-60px) scale(0.8);
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let isProcessing = false; // Prevent multiple rapid clicks

        document.addEventListener('submit', async function(event) {
            const form = event.target.closest('.like-form');
            if (!form || isProcessing) return; // Skip if not a like form or processing
            event.preventDefault();
            isProcessing = true;

            const button = form.querySelector('button');
            const likeIcon = button.querySelector('.like-icon');
            const likeCount = form.querySelector('.like-count');
            const url = form.getAttribute('action');
            const token = form.querySelector('input[name="_token"]').value;

            let count = parseInt(likeCount.textContent);
            const isLiked = likeIcon.classList.contains('fa-solid');

            // Optimistic UI update
            if (isLiked) {
                likeIcon.classList.remove('fa-solid', 'text-red-500');
                likeIcon.classList.add('fa-regular', 'text-gray-500');
                likeCount.textContent = count - 1;
            } else {
                likeIcon.classList.remove('fa-regular', 'text-gray-500');
                likeIcon.classList.add('fa-solid', 'text-red-500');
                likeCount.textContent = count + 1;

                // Create floating heart animation
                const heart = document.createElement('span');
                heart.innerHTML = '<i class="fa-solid fa-heart"></i>';
                heart.classList.add('floating-heart');
                const rect = button.getBoundingClientRect();
                heart.style.left = rect.width / 2 + 'px';
                heart.style.top = '-10px';
                button.style.position = 'relative';
                button.appendChild(heart);
                setTimeout(() => heart.remove(), 1000);
            }

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({})
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    throw new Error(data.error || 'Lỗi không xác định');
                }

                // Sync with server
                likeCount.textContent = data.likes_count;
            } catch (error) {
                console.error('Lỗi fetch:', error);
                alert('Có lỗi xảy ra, vui lòng thử lại.');

                // Rollback UI on error
                if (isLiked) {
                    likeIcon.classList.remove('fa-regular', 'text-gray-500');
                    likeIcon.classList.add('fa-solid', 'text-red-500');
                    likeCount.textContent = count;
                } else {
                    likeIcon.classList.remove('fa-solid', 'text-red-500');
                    likeIcon.classList.add('fa-regular', 'text-gray-500');
                    likeCount.textContent = count;
                }
            } finally {
                isProcessing = false; // Reset processing flag
            }
        });
    });
</script>