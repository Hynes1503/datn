<!DOCTYPE html>
<html lang="vi" x-data="{ sidebarOpen: true }" x-cloak>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs" defer></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 text-gray-900 font-sans">

    {{-- Sidebar --}}
    @include('admin.layouts.partials.sidebar')

    {{-- Nội dung bên phải --}}
    <div class="flex flex-col min-h-screen transition-all duration-300"
         :class="sidebarOpen ? 'md:pl-64 pl-20' : 'md:pl-20 pl-20'">

        {{-- Topbar --}}
        @include('admin.layouts.partials.topbar')

        {{-- Content --}}
        <main class="flex-1 p-6">
            @yield('content')
        </main>

        {{-- Footer --}}
        @include('admin.layouts.partials.footer')
    </div>

</body>
</html>
