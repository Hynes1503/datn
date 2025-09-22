@php
    // Fetch all buildings with their associated posts, including images, room, and user
    $buildings = App\Models\Building::with(['posts.images', 'posts.room', 'posts.user'])->get();

    // Define base coordinates for buildings
    $buildingCoordinates = [
        'A' => ['x' => 430, 'y' => 470], // Nhà A
        'B' => ['x' => 748, 'y' => 560], // Nhà B
        'K' => ['x' => 178, 'y' => 90],  // Nhà K
        'H' => ['x' => 450, 'y' => 90],  // Nhà H
        'I' => ['x' => 715, 'y' => 90],  // Nhà I
        'E' => ['x' => 180, 'y' => 270], // Nhà E
        'X' => ['x' => 390, 'y' => 285], // Nhà xe
        'TV' => ['x' => 556, 'y' => 330], // Thư viện
        'G' => ['x' => 685, 'y' => 260], // Nhà G
        'M' => ['x' => 748, 'y' => 370], // Nhà M
    ];
@endphp

<div class="p-4">
    <div class="bg-white rounded-lg shadow-md overflow-hidden" onclick="openMapPopup()" role="button" tabindex="0"
        aria-label="Open campus map">
        <div class="px-4 py-3 border-b border-gray-200">
            <h2 class="text-base font-semibold text-gray-800">Bản đồ EPU - Cơ sở 1</h2>
        </div>
        <img src="{{ asset('images/minmap.png') }}" alt="Mini map of Faculty of Electronics and Telecommunications"
            class="w-full">
    </div>
</div>

<div id="mapPopup" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50" role="dialog"
    aria-label="Campus Map">
    <div class="bg-white rounded-lg shadow-lg max-w-6xl w-[60%] h-[85%] p-4 relative">
        <button onclick="closeMapPopup()"
            class="absolute top-2 right-2 bg-red-500 text-white px-3 py-1 rounded-full hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500"
            aria-label="Close map">
            X
        </button>

        <div class="w-full h-full overflow-auto">
            <svg viewBox="0 0 900 760" width="100%" height="100%" preserveAspectRatio="xMidYMid meet"
                aria-labelledby="mapTitle">
                <title id="mapTitle">Bản đồ Trường Đại học Điện lực Hà Nội</title>

                <rect x="0" y="0" width="900" height="760" fill="#ffffff" />
                <!-- Thêm phần chú thích -->
                <g class="legend" transform="translate(-150, 620)" aria-label="Map Legend">
                    <rect x="0" y="0" width="200" height="120" fill="#f8f8f8" stroke="#000" stroke-width="2"
                        rx="6" />
                    <text x="10" y="20" font-size="14" font-weight="700" fill="#000">Chú thích</text>

                    <!-- Tòa nhà học thuật -->
                    <rect x="10" y="30" width="20" height="20" fill="#00000" rx="4" />
                    <text x="35" y="45" font-size="12" fill="#000">Tòa nhà</text>

                    <!-- Biểu tượng tòa nhà có bài đăng -->
                    <text x="20" y="110" font-family="FontAwesome" font-size="16" fill="#ff0000"
                        text-anchor="middle">&#xf111;</text>
                    <text x="35" y="110" font-size="12" fill="#000">Tòa nhà có bài đăng</text>
                </g>
                <!-- Map lines -->
                <line x1="30" y1="30" x2="30" y2="580" stroke="black" stroke-width="4"
                    stroke-dasharray="6 6" />
                <line x1="30" y1="580" x2="100" y2="690" stroke="black" stroke-width="4"
                    stroke-dasharray="6 6" />
                <line x1="100" y1="690" x2="380" y2="690" stroke="black" stroke-width="4"
                    stroke-dasharray="6 6" />
                <line x1="465" y1="690" x2="800" y2="690" stroke="black" stroke-width="4"
                    stroke-dasharray="6 6" />
                <line x1="800" y1="690" x2="800" y2="187" stroke="black" stroke-width="4"
                    stroke-dasharray="6 6" />
                <line x1="800" y1="187" x2="500" y2="187" stroke="black" stroke-width="4"
                    stroke-dasharray="6 6" />
                <line x1="500" y1="450" x2="500" y2="187" stroke="black" stroke-width="4"
                    stroke-dasharray="6 6" />
                <line x1="880" y1="690" x2="880" y2="30" stroke="black" stroke-width="4"
                    stroke-dasharray="6 6" />
                <line x1="30" y1="30" x2="880" y2="30" stroke="black" stroke-width="4"
                    stroke-dasharray="6 6" />
                <line x1="880" y1="145" x2="750" y2="145" stroke="black" stroke-width="4"
                    stroke-dasharray="6 6" />
                <line x1="500" y1="145" x2="700" y2="145" stroke="black" stroke-width="4"
                    stroke-dasharray="6 6" />
                <line x1="450" y1="145" x2="317" y2="145" stroke="black" stroke-width="4"
                    stroke-dasharray="6 6" />

                <text x="450" y="20" text-anchor="middle" font-size="20" font-weight="700" fill="#FF0000">
                    KHOA ĐIỆN TỬ VIỄN THÔNG - ĐH ĐIỆN LỰC HÀ NỘI
                </text>

                <!-- Buildings -->
                @foreach ($buildings as $building)
                    @php
                        $coords = $buildingCoordinates[$building->code] ?? ['x' => 0, 'y' => 0];
                        $hasPosts = $building->posts->count() > 0;
                        $buildingData = json_encode([
                            'name' => $building->name,
                            'posts' => $building->posts->map(function ($post) {
                                $media = $post->media->first();
                                $mediaUrl = $media ? asset('storage/' . $media->media_path) : asset('images/placeholder.webp');
                                $mediaType = $media && strpos($media->media_path, '.mp4') !== false ? 'video' : 'image';
                                $username = Str::slug($post->user->mention ?? 'anonymous');
                                $postUrl = route('posts.show', ['user' => $username, 'post' => $post->slug]);
                                return [
                                    'media' => $mediaUrl,
                                    'mediaType' => $mediaType,
                                    'url' => $postUrl,
                                    'room' => $post->room ? e($post->room->name) : 'No Room',
                                ];
                            })->toArray(),
                        ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
                    @endphp
                    <g class="building" aria-label="Building {{ $building->name }}"
                        @if($hasPosts)
                            onclick="openBuildingPopup({{ $buildingData }})"
                            onkeydown="if(event.key === 'Enter') openBuildingPopup({{ $buildingData }})"
                            role="button" tabindex="0"
                        @endif>
                        @if ($building->code === 'K')
                            <rect x="82" y="60" width="200" height="60" fill="#3b82f6" rx="6" />
                            <text x="178" y="93" text-anchor="middle" font-size="15" fill="#fff">NHÀ K</text>
                            @if ($hasPosts)
                                <text x="272" y="68" font-family="FontAwesome" font-size="16" fill="#ff0000"
                                    text-anchor="middle" dominant-baseline="middle">&#xf111;</text>
                            @endif
                        @elseif ($building->code === 'H')
                            <rect x="350" y="60" width="200" height="60" fill="#3b82f6" rx="6" />
                            <text x="450" y="83" text-anchor="middle" font-size="15" fill="#fff">NHÀ H</text>
                            <text x="450" y="103" text-anchor="middle" font-size="12" fill="#fff">KÝ TÚC XÁ SV</text>
                            @if ($hasPosts)
                                <text x="540" y="68" font-family="FontAwesome" font-size="16" fill="#ff0000"
                                    text-anchor="middle" dominant-baseline="middle">&#xf111;</text>
                            @endif
                        @elseif ($building->code === 'I')
                            <rect x="620" y="60" width="200" height="60" fill="#3b82f6" rx="6" />
                            <text x="715" y="93" text-anchor="middle" font-size="15" fill="#fff">NHÀ I</text>
                            @if ($hasPosts)
                                <text x="810" y="68" font-family="FontAwesome" font-size="16" fill="#ff0000"
                                    text-anchor="middle" dominant-baseline="middle">&#xf111;</text>
                            @endif
                        @elseif ($building->code === 'E')
                            <rect x="70" y="140" width="220" height="200" fill="#000080" rx="6" />
                            <text x="180" y="250" text-anchor="middle" font-size="22" fill="#fff">NHÀ E</text>
                            @if ($hasPosts)
                                <text x="280" y="148" font-family="FontAwesome" font-size="16" fill="#ff0000"
                                    text-anchor="middle" dominant-baseline="middle">&#xf111;</text>
                            @endif
                        @elseif ($building->code === 'X')
                            <rect x="315" y="190" width="150" height="160" fill="none" stroke="#000"
                                stroke-width="3" stroke-dasharray="8 6" rx="8" />
                            <rect x="315" y="190" width="150" height="160" fill="#ffffff" rx="6" />
                            <text x="390" y="265" text-anchor="middle" font-size="16" fill="#000">NHÀ XE</text>
                            @if ($hasPosts)
                                <text x="460" y="198" font-family="FontAwesome" font-size="16" fill="#ff0000"
                                    text-anchor="middle" dominant-baseline="middle">&#xf111;</text>
                            @endif
                        @elseif ($building->code === 'A')
                            <rect x="60" y="420" width="725" height="60" fill="#0b4a6f" rx="4" />
                            <rect x="420" y="390" width="20" height="80" fill="#0b4a6f" />
                            <rect x="400" y="450" width="60" height="80" fill="#0b4a6f" />
                            <rect x="400" y="375" width="60" height="20" fill="#0b4a6f" />
                            <text x="430" y="455" text-anchor="middle" font-size="14" fill="#fff">NHÀ A</text>
                            @if ($hasPosts)
                                <text x="775" y="428" font-family="FontAwesome" font-size="16" fill="#ff0000"
                                    text-anchor="middle" dominant-baseline="middle">&#xf111;</text>
                            @endif
                        @elseif ($building->code === 'TV')
                            <rect x="515" y="195" width="80" height="225" fill="#b91c1c" rx="4" />
                            <text x="556" y="310" text-anchor="middle" font-size="14" fill="#fff">THƯ VIỆN</text>
                            @if ($hasPosts)
                                <text x="590" y="203" font-family="FontAwesome" font-size="16" fill="#ff0000"
                                    text-anchor="middle" dominant-baseline="middle">&#xf111;</text>
                            @endif
                        @elseif ($building->code === 'G')
                            <rect x="595" y="195" width="190" height="85" fill="#1f2937" rx="4" />
                            <text x="685" y="240" text-anchor="middle" font-size="14" fill="#fff">NHÀ G</text>
                            @if ($hasPosts)
                                <text x="780" y="203" font-family="FontAwesome" font-size="16" fill="#ff0000"
                                    text-anchor="middle" dominant-baseline="middle">&#xf111;</text>
                            @endif
                        @elseif ($building->code === 'M')
                            <rect x="705" y="280" width="80" height="140" fill="#10b981" rx="4" />
                            <text x="748" y="350" text-anchor="middle" font-size="14" fill="#fff">NHÀ M</text>
                            @if ($hasPosts)
                                <text x="780" y="288" font-family="FontAwesome" font-size="16" fill="#ff0000"
                                    text-anchor="middle" dominant-baseline="middle">&#xf111;</text>
                            @endif
                        @elseif ($building->code === 'B')
                            <rect x="705" y="480" width="80" height="120" fill="#f59e0b" rx="4" />
                            <text x="748" y="540" text-anchor="middle" font-size="14" fill="#fff">NHÀ B</text>
                            @if ($hasPosts)
                                <text x="780" y="488" font-family="FontAwesome" font-size="16" fill="#ff0000"
                                    text-anchor="middle" dominant-baseline="middle">&#xf111;</text>
                            @endif
                        @endif
                    </g>
                @endforeach

                <text x="420" y="720" text-anchor="middle" font-size="23" fill="#b91c1c">CỔNG CHÍNH</text>
                <text x="840" y="720" text-anchor="middle" font-size="23" fill="#b91c1c">CỔNG PHỤ</text>
            </svg>
        </div>
    </div>
</div>

<!-- Building Posts Popup -->
<div id="buildingPopup" class="hidden fixed bg-white rounded-lg shadow-lg p-4 z-[60]" role="dialog"
    aria-label="Building Posts">
    <button onclick="closeBuildingPopup()"
        class="absolute top-2 right-2 bg-red-500 text-white px-3 py-1 rounded-full hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500"
        aria-label="Close building posts">
        X
    </button>
    <h3 id="buildingName" class="text-lg font-semibold mb-2"></h3>
    <div id="buildingPosts" class="flex flex-col gap-4 overflow-y-auto max-h-[80vh]"></div>
</div>

<style>
    .building {
        transition: transform 0.2s ease, filter 0.2s ease;
        cursor: pointer;
        transform-box: fill-box;
        transform-origin: center;
    }

    .building:hover, .building:focus {
        transform: scale(1.1);
        filter: brightness(1.1);
        outline: none;
    }

    #buildingPopup {
        text-align: center;
        pointer-events: auto;
        z-index: 60;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        transition: opacity 0.3s ease;
        width: 300px;
        max-height: 80vh;
    }

    #buildingPosts a {
        display: block;
        text-decoration: none;
        color: inherit;
        padding: 8px; /* Thêm padding cho mỗi bài đăng */
        border-bottom: 1px solid #e5e7eb; /* Đường viền dưới để phân tách */
    }

    #buildingPosts a:last-child {
        border-bottom: none; /* Bỏ đường viền dưới cho bài đăng cuối */
    }

    #buildingPosts .media-container {
        height: 128px; /* Chiều cao cố định */
        max-width: 100%; /* Đảm bảo không vượt quá container */
        overflow: hidden;
        border-radius: 0.25rem;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f8f8; /* Màu nền để lấp đầy khoảng trống nếu có */
    }

    #buildingPosts video, #buildingPosts img {
        width: auto;
        height: 100%;
        object-fit: contain; /* Hiển thị toàn bộ ảnh/video */
        border-radius: 0.25rem;
    }

    [role="button"] {
        cursor: pointer;
    }

    [role="button"]:focus {
        outline: 2px solid #3b82f6;
        outline-offset: 2px;
    }
</style>

<script>
    function openMapPopup() {
        document.getElementById("mapPopup").classList.remove("hidden");
        document.getElementById("mapPopup").focus();
        closeBuildingPopup();
    }

    function closeMapPopup() {
        document.getElementById("mapPopup").classList.add("hidden");
        closeBuildingPopup();
    }

    function openBuildingPopup(data) {
        const buildingPopup = document.getElementById("buildingPopup");
        const buildingName = document.getElementById("buildingName");
        const buildingPosts = document.getElementById("buildingPosts");
        const popupWidth = 300;
        const marginRight = 20;

        // Tính chiều cao của popup dựa trên số bài đăng, mỗi bài cao khoảng 160px
        const postHeight = 160; // Ước tính chiều cao mỗi bài (128px ảnh + padding + margin + border)
        const totalHeight = Math.min(data.posts.length * postHeight, window.innerHeight * 0.8);
        buildingPopup.style.left = `${window.innerWidth - popupWidth - marginRight}px`;
        buildingPopup.style.top = `${(window.innerHeight - totalHeight) / 2}px`;
        buildingPopup.classList.remove("hidden");

        buildingName.textContent = data.name;
        buildingPosts.innerHTML = '';

        data.posts.forEach(post => {
            const postContainer = document.createElement('a');
            postContainer.href = post.url;
            postContainer.className = 'block';
            const mediaContainer = document.createElement('div');
            mediaContainer.className = 'media-container';

            if (post.mediaType === 'video') {
                const video = document.createElement('video');
                video.src = post.media;
                video.controls = true;
                video.autoplay = false;
                video.muted = true;
                video.className = 'w-auto h-full object-contain rounded';
                video.onerror = () => {
                    const img = document.createElement('img');
                    img.src = '{{ asset('images/placeholder.png') }}';
                    img.alt = 'Placeholder image';
                    img.className = 'w-auto h-full object-contain rounded';
                    mediaContainer.innerHTML = '';
                    mediaContainer.appendChild(img);
                };
                mediaContainer.appendChild(video);
            } else {
                const img = document.createElement('img');
                img.src = post.media;
                img.alt = 'Post image';
                img.className = 'w-auto h-full object-contain rounded';
                img.onerror = () => {
                    img.src = '{{ asset('images/placeholder.png') }}';
                };
                mediaContainer.appendChild(img);
            }

            const roomText = document.createElement('p');
            roomText.textContent = post.room;
            roomText.className = 'text-center text-sm font-medium mt-1';

            postContainer.appendChild(mediaContainer);
            postContainer.appendChild(roomText);
            buildingPosts.appendChild(postContainer);
        });
    }

    function closeBuildingPopup() {
        document.getElementById("buildingPopup").classList.add("hidden");
        document.getElementById("buildingPosts").innerHTML = '';
    }

    document.addEventListener('click', function(event) {
        const buildingPopup = document.getElementById("buildingPopup");
        const isClickInsideBuilding = buildingPopup.contains(event.target);
        const isClickOnBuilding = event.target.closest('.building');
        if (!isClickInsideBuilding && !isClickOnBuilding) {
            closeBuildingPopup();
        }
    });

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeMapPopup();
            closeBuildingPopup();
        }
    });

    document.querySelector('[onclick="openMapPopup()"]').addEventListener('keydown', function(event) {
        if (event.key === 'Enter' || event.key === ' ') {
            event.preventDefault();
            openMapPopup();
        }
    });

    window.addEventListener('resize', function() {
        const buildingPopup = document.getElementById("buildingPopup");
        if (!buildingPopup.classList.contains("hidden")) {
            const popupWidth = 300;
            const marginRight = 20;
            const posts = document.querySelectorAll("#buildingPosts a").length;
            const postHeight = 160; // Ước tính chiều cao mỗi bài
            const totalHeight = Math.min(posts * postHeight, window.innerHeight * 0.8);
            buildingPopup.style.left = `${window.innerWidth - popupWidth - marginRight}px`;
            buildingPopup.style.top = `${(window.innerHeight - totalHeight) / 2}px`;
        }
    });
</script>