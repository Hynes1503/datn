<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>@yield('title')</title>
  <!-- Tailwind CDN for quick styling (remove if your project compiles Tailwind) -->
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    /* Image gallery rules: fixed height = 250px, width auto, horizontal scroll when many images */
    .post-images {
      display: flex;
      gap: 0.5rem;
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
      padding: 0.25rem 0.5rem;
      position: relative; /* For positioning the fade overlay */
    }

    .post-images::-webkit-scrollbar {
      height: 8px;
    }
    .post-images::-webkit-scrollbar-thumb {
      background: rgba(0,0,0,0.15);
      border-radius: 9999px;
    }

    .post-image {
      height: 250px; /* fixed height */
      width: auto;   /* width scales proportionally */
      max-width: 100%;
      border-radius: 0.5rem;
      object-fit: cover; /* if image aspect differs, it will be cropped to fill height */
      display: block;
      flex: 0 0 auto; /* prevents image from shrinking; preserves width */
    }

    /* Optional: make a thumbnail container to force consistent box with centered image */
    .image-box {
      height: 250px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
      background: #f8fafc;
      border-radius: 0.5rem;
      flex: 0 0 auto;
    }

    /* Black semi-transparent fade effect on the right */
    .post-images.fade-right::after {
      content: '';
      position: absolute;
      top: 0;
      right: 0;
      width: 50px;
      height: 100%;
      background: rgba(0, 0, 0, 0.3); /* Semi-transparent black layer */
      pointer-events: none; /* Prevent interaction with the fade overlay */
    }

    /* Remove fade when scrolled to the end */
    .post-images.scrolled-to-end::after {
      display: none;
    }

    /* Content truncation helpers */
    .line-clamp-3 {
      display: -webkit-box;
      -webkit-line-clamp: 3;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }
  </style>
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen flex">
  <!-- Vertical Navbar -->
  <nav class="fixed top-0 left-0 h-full w-16 bg-white shadow-sm z-10 flex flex-col items-center py-4">
    <!-- Logo -->
    <a href="{{ route('posts.index') }}" class="mb-8">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-900" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7m-9 5v6h4v-6m-7-9h14"/>
      </svg>
    </a>

    <!-- Navigation Icons -->
    <div class="flex flex-col items-center gap-6">
      <a href="{{ route('posts.index') }}" class="text-gray-600 hover:text-gray-900" title="Trang chủ">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7m-9 5v6h4v-6m-7-9h14"/>
        </svg>
      </a>
      <a href="{{ route('posts.create') }}" class="text-gray-600 hover:text-gray-900" title="Đăng bài">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
      </a>
      <a href="#" class="text-gray-600 hover:text-gray-900" title="Hồ sơ">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
        </svg>
      </a>
      <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="text-gray-600 hover:text-gray-900" title="Đăng xuất">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h3a3 3 0 013 3v1"/>
          </svg>
        </button>
      </form>
    </div>
  </nav>

  <!-- Content Area -->
  <div class="flex-1 flex justify-center p-4">
    <div class="w-full max-w-3xl">
      @yield('content')
    </div>
  </div>

  <script>
    // Add scroll event listeners to each post-images container
    document.querySelectorAll('.post-images').forEach(container => {
      // Check if the container is scrollable
      const isScrollable = container.scrollWidth > container.clientWidth;

      // Add or remove fade-right class based on scrollability
      if (!isScrollable) {
        container.classList.remove('fade-right');
      } else {
        container.classList.add('fade-right');
      }

      container.addEventListener('scroll', () => {
        // Check if scrolled to the end (within 1px tolerance)
        const isAtEnd = container.scrollLeft + container.clientWidth >= container.scrollWidth - 1;
        if (isAtEnd) {
          container.classList.add('scrolled-to-end');
        } else {
          container.classList.remove('scrolled-to-end');
        }
      });

      // Trigger scroll event on load to check initial state
      container.dispatchEvent(new Event('scroll'));
    });
  </script>
</body>
</html>