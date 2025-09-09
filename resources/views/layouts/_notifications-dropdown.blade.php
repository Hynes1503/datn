<!-- resources/views/components/navbar/_notifications-dropdown.blade.php -->
<div class="relative">
    <a href="#" id="notificationBtn"
        class="text-gray-900 hover:scale-110 hover:bg-gray-100 p-2 rounded-full transition-all relative"
        title="Thông báo">
        <i class="fa-solid fa-bell"></i>
        @if (Auth::check() && Auth::user()->unreadNotifications->count() > 0)
            <span
                class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center">
                {{ Auth::user()->unreadNotifications->count() }}
            </span>
        @endif
    </a>

    @if (Auth::check())
        <div id="notificationMenu"
            class="absolute left-full bottom-0 mb-2 hidden bg-white border border-gray-300 rounded-xl shadow-lg w-80 max-h-96 overflow-y-auto z-50">
            <!-- Mũi tên -->
            <div class="absolute -left-3 top-8">
                <div class="w-0 h-0 border-y-[10px] border-y-transparent border-r-[12px] border-r-gray-300">
                </div>
                <div
                    class="absolute top-0 left-[1px] w-0 h-0 border-y-[9px] border-y-transparent border-r-[11px] border-r-white">
                </div>
            </div>

            <!-- Mark All as Read Button -->
            @if (Auth::user()->unreadNotifications->count() > 0)
                <form action="{{ route('notifications.markAllAsRead') }}" method="POST"
                    class="px-4 py-2 border-b border-gray-200">
                    @csrf
                    <button type="submit"
                        class="w-full text-left text-sm text-blue-600 hover:text-blue-800 font-medium transition-colors">
                        Đánh dấu tất cả là đã đọc
                    </button>
                </form>
            @endif

            <!-- Notifications List -->
            @if (Auth::user()->unreadNotifications->isEmpty())
                <div class="px-4 py-3 text-sm text-gray-500">
                    Không có thông báo mới
                </div>
            @else
                @foreach (Auth::user()->unreadNotifications as $notification)
                    <div
                        class="px-4 py-3 text-sm text-gray-700 hover:bg-gray-100 flex justify-between items-center border-b border-gray-100 last:border-b-0 transition-colors">
                        @if ($notification->type === App\Notifications\FollowNotification::class)
                            <a href="{{ route('users.show', $notification->data['follower_mention']) }}" class="flex-1">
                                {{ $notification->data['message'] }}<br>
                                <small class="text-gray-500">{{ $notification->created_at->diffForHumans() }}</small>
                            </a>
                        @elseif ($notification->type === App\Notifications\LikeNotification::class)
                            <a href="{{ route('posts.show', [
                                'user' => $notification->data['post_owner_mention'],
                                'post' => $notification->data['post_slug'],
                            ]) }}"
                                class="flex-1">
                                {{ $notification->data['message'] }}<br>
                                <small class="text-gray-500">{{ $notification->created_at->diffForHumans() }}</small>
                            </a>
                        @elseif ($notification->type === App\Notifications\CommentNotification::class)
                            <a href="{{ route('posts.show', [
                                'user' => $notification->data['post_owner_mention'],
                                'post' => $notification->data['post_slug'],
                            ]) }}#comment-{{ $notification->data['comment_id'] }}"
                                class="flex-1">
                                {{ $notification->data['message'] }}<br>
                                <small class="text-gray-500">{{ $notification->created_at->diffForHumans() }}</small>
                            </a>
                        @else
                            <div class="flex-1">
                                {{ $notification->data['message'] ?? 'Thông báo mới' }}<br>
                                <small class="text-gray-500">{{ $notification->created_at->diffForHumans() }}</small>
                            </div>
                        @endif

                        <form action="{{ route('notifications.markAsRead', $notification->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="text-blue-500 hover:text-blue-700 text-xs font-medium">
                                Đã đọc
                            </button>
                        </form>
                    </div>
                @endforeach
            @endif
        </div>
    @endif
</div>
