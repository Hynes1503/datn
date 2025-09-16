{{-- Followers Count --}}
<div class="mb-4">
    <a href="#" id="show-followers" class="text-black hover:underline">
        ● {{ $user->followers()->count() }} người theo dõi
    </a>
</div>

{{-- Followers Modal --}}
<div id="followers-modal" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl border border-gray-200 shadow-lg w-full max-w-lg max-h-[80vh] overflow-y-auto">

        {{-- Header --}}
        <div class="flex justify-between items-center p-4 border-b">
            <h2 class="text-lg font-bold">Người theo dõi</h2>
            <button type="button" id="close-followers-modal"
                class="text-gray-500 hover:text-gray-700 text-2xl leading-none">&times;</button>
        </div>

        {{-- Followers list --}}
        <div id="followers-list" class="divide-y">
            <!-- Followers will be dynamically loaded here -->
        </div>
    </div>
</div>

<style>
    #followers-list img {
        transition: transform 0.2s;
    }

    #followers-list img:hover {
        transform: scale(1.05);
    }

    .follow-toggle {
        transition: background-color 0.3s, color 0.3s;
        background-color: #000;
        color: #fff;
    }

    .follow-toggle:hover {
        background-color: #111;
    }
</style>

<script>
    // Mở modal followers
    document.getElementById('show-followers').addEventListener('click', function(event) {
        event.preventDefault();
        const modal = document.getElementById('followers-modal');
        const followersList = document.getElementById('followers-list');

        modal.classList.remove('hidden');

        fetch('{{ route('users.followers', $user->mention) }}')
            .then(response => response.json())
            .then(data => {
                followersList.innerHTML = '';

                if (data.followers.length === 0) {
                    followersList.innerHTML = '<p class="p-4 text-gray-500">Không có người theo dõi.</p>';
                    return;
                }

                // Giả sử mention của người dùng hiện tại được truyền vào từ server
                const currentUserMention = '{{ auth()->user()->mention ?? '' }}';

                data.followers.forEach(follower => {
                    const label = follower.is_mutual ? 'Bạn bè' : 'Theo dõi lại';

                    // ✅ Avatar fallback
                    const avatarUrl = follower.avatar ?
                        '/storage/' + follower.avatar :
                        '/images/default-avatar.png';

                    // Kiểm tra xem follower có phải là người dùng hiện tại không
                    const isCurrentUser = follower.mention === currentUserMention;

                    // Chỉ thêm nút follow/unfollow nếu không phải là người dùng hiện tại
                    const followButtonHtml = isCurrentUser ? '' : `
                        <button type="button" 
                            class="follow-toggle px-4 py-1.5 text-sm rounded-full" 
                            data-user-mention="${follower.mention}" 
                            data-is-mutual="${follower.is_mutual}">
                            ${label}
                        </button>
                    `;

                    const followerHtml = `
                        <div class="flex items-center justify-between p-4 hover:bg-gray-50 transition">
                            <a href="/${follower.mention}" class="flex items-center gap-3">
                                <img src="${avatarUrl}" 
                                     alt="${follower.mention}" 
                                     class="h-10 w-10 rounded-full object-cover border">
                                <div class="flex flex-col">
                                    <span class="font-semibold text-gray-900">${follower.name ?? ''}</span>
                                    <span class="text-gray-500 text-sm">@${follower.mention}</span>
                                </div>
                            </a>
                            ${followButtonHtml}
                        </div>
                    `;
                    followersList.innerHTML += followerHtml;
                });
            })
            .catch(error => {
                console.error('Error fetching followers:', error);
                followersList.innerHTML = '<p class="p-4 text-red-500">Đã có lỗi xảy ra.</p>';
            });
    });

    // Optimistic follow/unfollow (UI đổi ngay)
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('follow-toggle')) {
            event.preventDefault();

            const button = event.target;
            const userMention = button.getAttribute('data-user-mention');
            const isMutual = button.getAttribute('data-is-mutual') === 'true';
            const url = isMutual ? `/unfollow/${userMention}` : `/follow/${userMention}`;
            const newIsMutual = !isMutual;

            // ✅ Đổi UI ngay lập tức
            button.textContent = newIsMutual ? 'Bạn bè' : 'Theo dõi lại';
            button.setAttribute('data-is-mutual', newIsMutual.toString());

            // 🚀 Gửi request song song
            fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        // ❌ rollback nếu server báo lỗi
                        button.textContent = isMutual ? 'Bạn bè' : 'Theo dõi lại';
                        button.setAttribute('data-is-mutual', isMutual.toString());
                    }
                });
        }
    });

    // Đóng modal
    document.getElementById('close-followers-modal').addEventListener('click', function() {
        document.getElementById('followers-modal').classList.add('hidden');
    });

    // Đóng modal khi click ngoài khung
    document.getElementById('followers-modal').addEventListener('click', function(event) {
        if (event.target === this) {
            this.classList.add('hidden');
        }
    });
</script>
