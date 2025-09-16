<div class="p-4">
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-4 py-3 border-b border-gray-200">
            <h2 class="text-base font-semibold text-gray-800">Gợi ý kết nối</h2>
        </div>
        <div class="divide-y divide-gray-100">
            @if ($suggestedUsers->isNotEmpty())
                @foreach ($suggestedUsers as $user)
                    <div class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition">
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                            class="w-10 h-10 rounded-full object-cover">

                        <div class="flex-1">
                            <a href="{{ route('users.show', $user->mention) }}"
                                class="text-sm font-medium text-gray-900 hover:underline">
                                {{ $user->name }}
                            </a>
                            <div class="text-xs text-gray-500">
                                {{ '@' . $user->mention }}
                            </div>
                        </div>

                        <div>
                            <button type="button"
                                class="follow-toggle w-8 h-8 flex items-center justify-center rounded-full border border-gray-400 text-gray-700 hover:bg-gray-100 transition transform duration-200"
                                data-user-mention="{{ $user->mention }}"
                                data-following="{{ Auth::user()->isFollowing($user) ? 'true' : 'false' }}">
                                @if (!Auth::user()->isFollowing($user))
                                    <i class="fa-solid fa-user-plus"></i>
                                @else
                                    <i class="fa-solid fa-user-minus"></i>
                                @endif
                            </button>
                        </div>
                    </div>
                @endforeach
            @else
                <p class="px-4 py-3 text-sm text-gray-500">Không tìm thấy gợi ý phù hợp.</p>
            @endif
        </div>
    </div>
</div>

<script>
    document.addEventListener('click', function(event) {
        if (event.target.closest('.follow-toggle')) {
            event.preventDefault();

            const button = event.target.closest('.follow-toggle');
            const userMention = button.getAttribute('data-user-mention');
            const isFollowing = button.getAttribute('data-following') === 'true';
            const url = isFollowing ? `/unfollow/${userMention}` : `/follow/${userMention}`;

            // 🔥 Animation: scale nhỏ rồi to lại
            button.classList.add('scale-75', 'opacity-50');
            setTimeout(() => {
                button.innerHTML = isFollowing ?
                    '<i class="fa-solid fa-user-plus"></i>' :
                    '<i class="fa-solid fa-user-minus"></i>';
                button.setAttribute('data-following', (!isFollowing).toString());
                button.classList.remove('scale-75', 'opacity-50');
                button.classList.add('scale-100', 'opacity-100');
            }, 150);

            // 🚀 Gửi request
            fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    },
                })
                .then(res => res.json())
                .then(data => {
                    if (!data.success) {
                        // rollback nếu server báo lỗi
                        button.innerHTML = isFollowing ?
                            '<i class="fa-solid fa-user-minus"></i>' :
                            '<i class="fa-solid fa-user-plus"></i>';
                        button.setAttribute('data-following', isFollowing.toString());
                    }
                })
                // .catch(() => {
                //     button.innerHTML = isFollowing ?
                //         '<i class="fa-solid fa-user-minus"></i>' :
                //         '<i class="fa-solid fa-user-plus"></i>';
                //     button.setAttribute('data-following', isFollowing.toString());
                // });
        }
    });
</script>
