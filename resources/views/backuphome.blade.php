<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Threads-like - Laravel Blade</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-100">
    <div class="max-w-6xl mx-auto grid grid-cols-12 gap-4 p-4">

        <!-- LEFT NAV -->
        <aside class="col-span-12 md:col-span-3 hidden md:block">
            <nav class="space-y-4 sticky top-4">
                <div class="p-4 bg-white dark:bg-gray-800 rounded-2xl shadow-sm">
                    <h1 class="text-xl font-bold">Threads Clone</h1>
                </div>
                <ul class="space-y-2">
                    <li class="p-3 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-800">Home</li>
                    <li class="p-3 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-800">Explore</li>
                    <li class="p-3 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-800">Notifications</li>
                    <li class="p-3 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-800">Bookmarks</li>
                </ul>
                <div class="p-4 bg-white dark:bg-gray-800 rounded-2xl shadow-sm">
                    <div class="flex items-center space-x-3">
                        <img src="{{ $user['avatar'] }}" alt="avatar" class="w-12 h-12 rounded-full" />
                        <div>
                            <div class="font-semibold">{{ $user['name'] }}</div>
                            <div class="text-sm text-gray-500">@{{ $user['username'] }}</div>
                        </div>
                    </div>
                </div>
            </nav>
        </aside>

        <!-- FEED / MAIN -->
        <main class="col-span-12 md:col-span-6">
            <!-- Composer -->
            <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-sm mb-4">
                <form method="POST" action="{{ route('threads.post') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="flex space-x-3">
                        <img src="{{ $user['avatar'] }}" class="w-12 h-12 rounded-full" alt="avatar" />
                        <div class="flex-1">
                            <textarea name="content" rows="3" class="w-full bg-transparent focus:outline-none"
                                placeholder="What's happening?"></textarea>
                            <div class="flex justify-between items-center mt-2">
                                <div class="text-sm text-gray-500">Add mock images (demo)</div>
                                <input type="file" name="media" accept="image/*" />
                            </div>
                            <div class="mt-3 text-right">
                                <button class="px-4 py-2 bg-blue-500 text-white rounded-lg">Post</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Posts -->
            <div id="feed" class="space-y-4">
                @foreach ($posts as $post)
                    <article class="bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-sm">
                        <div class="flex space-x-3">
                            <img src="{{ $post['author']['avatar'] }}" class="w-12 h-12 rounded-full" />
                            <div class="flex-1">
                                <div class="flex justify-between">
                                    <div>
                                        <div class="font-semibold">
                                            {{ $post['author']['name'] }}
                                            <span class="text-sm text-gray-500">@{{ $post['author']['username'] }} •
                                                {{ $post['created_at'] }}</span>
                                        </div>
                                        <div class="mt-2">{{ $post['content'] }}</div>
                                    </div>
                                    <div class="text-gray-400">•••</div>
                                </div>

                                @if (!empty($post['media']))
                                    <div class="mt-3">
                                        <img src="{{ $post['media'] }}" class="w-full rounded-xl" />
                                    </div>
                                @endif

                                <div class="mt-3 flex items-center justify-between text-sm text-gray-500">
                                    <div class="flex items-center space-x-4">
                                        <form method="POST" action="{{ route('threads.like', $post['id']) }}">
                                            @csrf
                                            <button class="flex items-center space-x-1">
                                                <span>{{ $post['likes'] }}</span><span>Like</span>
                                            </button>
                                        </form>
                                        <button onclick="openReplyModal({{ $post['id'] }})">Reply
                                            ({{ count($post['replies']) }})</button>
                                        <button>Repost</button>
                                    </div>
                                    <div>Share</div>
                                </div>

                                <!-- Replies preview -->
                                @if (count($post['replies']) > 0)
                                    <div class="mt-3 border-t pt-3">
                                        @foreach (array_slice($post['replies'], 0, 2) as $reply)
                                            <div class="flex items-start space-x-3 mb-3">
                                                <img src="{{ $reply['author']['avatar'] }}"
                                                    class="w-8 h-8 rounded-full" />
                                                <div>
                                                    <div class="text-sm font-semibold">
                                                        {{ $reply['author']['name'] }}
                                                        <span
                                                            class="text-xs text-gray-400">@{{ $reply['author']['username'] }}</span>
                                                    </div>
                                                    <div class="text-sm">{{ $reply['content'] }}</div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach

                <div class="text-center mt-4">
                    <form method="POST" action="{{ route('threads.loadMore') }}">
                        @csrf
                        <button class="px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded-lg">Load more</button>
                    </form>
                </div>
            </div>
        </main>

        <!-- RIGHT SIDEBAR -->
        <aside class="col-span-12 md:col-span-3 hidden md:block">
            <div class="space-y-4 sticky top-4">
                <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-sm">
                    <input type="text" placeholder="Search"
                        class="w-full bg-gray-100 dark:bg-gray-700 p-2 rounded-lg" />
                </div>

                <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-sm">
                    <h3 class="font-semibold mb-2">Trending</h3>
                    <ul class="space-y-2 text-sm text-gray-500">
                        @foreach ($trends as $t)
                            <li>#{{ $t }}</li>
                        @endforeach
                    </ul>
                </div>

                <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-sm">
                    <h3 class="font-semibold mb-2">Who to follow</h3>
                    @foreach ($whoToFollow as $f)
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center space-x-3">
                                <img src="{{ $f['avatar'] }}" class="w-10 h-10 rounded-full" />
                                <div>
                                    <div class="font-semibold">{{ $f['name'] }}</div>
                                    <div class="text-sm text-gray-500">@{{ $f['username'] }}</div>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('threads.follow', $f['id']) }}">
                                @csrf
                                <button class="px-3 py-1 bg-blue-500 text-white rounded-lg">
                                    {{ $f['followed'] ? 'Following' : 'Follow' }}
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        </aside>
    </div>

    <!-- Reply modal -->
    <div id="replyModal" class="fixed inset-0 bg-black bg-opacity-40 hidden items-center justify-center">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl w-full max-w-xl">
            <h3 class="font-semibold mb-3">Reply</h3>
            <form id="replyForm" method="POST" action="{{ route('threads.reply', 0) }}">
                @csrf
                <input type="hidden" name="post_id" id="modalPostId" value="" />
                <textarea name="reply" rows="4" class="w-full bg-transparent focus:outline-none"
                    placeholder="Write a reply..."></textarea>
                <div class="mt-4 text-right">
                    <button type="button" onclick="closeReplyModal()"
                        class="mr-2 px-4 py-2 rounded-lg">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg">Send</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openReplyModal(postId) {
            document.getElementById('modalPostId').value = postId;
            let form = document.getElementById('replyForm');
            form.action = form.action.replace(/\\d+$/, postId);
            document.getElementById('replyModal').classList.remove('hidden');
            document.getElementById('replyModal').classList.add('flex');
        }

        function closeReplyModal() {
            document.getElementById('replyModal').classList.add('hidden');
            document.getElementById('replyModal').classList.remove('flex');
        }
    </script>
</body>

</html>
