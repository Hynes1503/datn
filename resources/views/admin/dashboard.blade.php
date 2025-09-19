@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Tổng quan hệ thống')

@section('content')
    {{-- Card thống kê chính --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        {{-- User --}}
        <a href="{{ route('admin.users.index') }}">
            <div class="bg-white rounded-2xl shadow p-6 flex items-center space-x-4">
                <div class="p-3 bg-blue-100 text-blue-600 rounded-xl">
                    <i class="fa-solid fa-users text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Tổng số User</p>
                    <h3 class="text-2xl font-bold">{{ $totalUsers }}</h3>
                </div>
            </div>
        </a>

        {{-- Post --}}
        <a href="{{ route('admin.posts.index') }}">
            <div class="bg-white rounded-2xl shadow p-6 flex items-center space-x-4">
                <div class="p-3 bg-green-100 text-green-600 rounded-xl">
                    <i class="fa-solid fa-file-alt text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Bài viết</p>
                    <h3 class="text-2xl font-bold">{{ $totalPosts }}</h3>
                </div>
            </div>
        </a>

        {{-- Comment --}}
        <a href="{{ route('admin.comments.index') }}">
            <div class="bg-white rounded-2xl shadow p-6 flex items-center space-x-4">
                <div class="p-3 bg-purple-100 text-purple-600 rounded-xl">
                    <i class="fa-solid fa-comments text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Comment</p>
                    <h3 class="text-2xl font-bold">{{ $totalComments }}</h3>
                </div>
            </div>
        </a>

        {{-- Notification --}}
        <div class="bg-white rounded-2xl shadow p-6 flex items-center space-x-4">
            <div class="p-3 bg-red-100 text-red-600 rounded-xl">
                <i class="fa-solid fa-bell text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500">Thông báo chưa đọc</p>
                <h3 class="text-2xl font-bold">{{ $unreadNotifications }}</h3>
            </div>
        </div>
    </div>

    {{-- Chart & bảng --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Biểu đồ bài viết --}}
        <div class="bg-white rounded-2xl shadow p-6 col-span-2">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold">Thống kê bài viết</h2>
                <form method="GET" action="{{ route('admin.dashboard') }}">
                    <select name="type" onchange="this.form.submit()" class="border rounded px-2 py-1 text-sm">
                        <option value="day" {{ $type === 'day' ? 'selected' : '' }}>Ngày</option>
                        <option value="month" {{ $type === 'month' ? 'selected' : '' }}>Tháng</option>
                        <option value="year" {{ $type === 'year' ? 'selected' : '' }}>Năm</option>
                    </select>
                </form>
            </div>
            <canvas id="postsChart" class="w-full h-64"></canvas>
        </div>

        {{-- User mới --}}
        <div class="bg-white rounded-2xl shadow p-6">
            <h2 class="text-lg font-semibold mb-4">User mới</h2>
            <ul class="divide-y divide-gray-100">
                @foreach ($latestUsers as $user)
                    <li class="py-2 flex items-center justify-between">
                        <a href="{{ route('admin.users.show', $user->mention) }}">
                            <span>{{ $user->name }}</span>
                        </a>
                        <span class="text-xs text-gray-500">{{ $user->created_at->diffForHumans() }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    {{-- Biểu đồ bổ sung --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        <div class="bg-white rounded-2xl shadow p-6">
            <h2 class="text-lg font-semibold mb-4">Top bài viết nhiều view</h2>
            <canvas id="topPostsChart" class="w-full h-64"></canvas>
        </div>
        <div class="bg-white rounded-2xl shadow p-6">
            <h2 class="text-lg font-semibold mb-4">User đăng ký theo tháng</h2>
            <canvas id="usersPerMonthChart" class="w-full h-64"></canvas>
        </div>
    </div>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Bài viết
        new Chart(document.getElementById('postsChart'), {
            type: 'line',
            data: {
                labels: @json($labels),
                datasets: [{
                    label: 'Bài viết',
                    data: @json($data),
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59,130,246,0.2)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Top bài viết theo view
        new Chart(document.getElementById('topPostsChart'), {
            type: 'bar',
            data: {
                labels: @json($topPosts['labels']),
                datasets: [{
                    data: @json($topPosts['data']),
                    backgroundColor: '#6366f1'
                }]
            },
            options: {
                indexAxis: 'y',
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // User đăng ký theo tháng
        new Chart(document.getElementById('usersPerMonthChart'), {
            type: 'line',
            data: {
                labels: @json($usersPerMonth['labels']),
                datasets: [{
                    data: @json($usersPerMonth['data']),
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16,185,129,0.2)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    </script>
@endsection
