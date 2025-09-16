@php
    // Fetch all buildings with their associated posts, including images, room, and user
    $buildings = App\Models\Building::with(['posts.images', 'posts.room', 'posts.user'])->get();

    // Define base coordinates for location dots
    $buildingCoordinates = [
        'A' => ['x' => 430, 'y' => 470], // Nhà A
        'B' => ['x' => 748, 'y' => 560], // Nhà B
        'K' => ['x' => 178, 'y' => 90], // Nhà K
        'H' => ['x' => 450, 'y' => 90], // Nhà H
        'I' => ['x' => 715, 'y' => 90], // Nhà I
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
            <h2 class="text-base font-semibold text-gray-800">Bản đồ EPU</h2>
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
                    <rect x="0" y="0" width="200" height="100" fill="#f8f8f8" stroke="#000" stroke-width="2"
                        rx="6" />
                    <text x="10" y="20" font-size="14" font-weight="700" fill="#000">Chú thích</text>

                    <!-- Tòa nhà học thuật -->
                    <rect x="10" y="30" width="20" height="20" fill="#00000" rx="4" />
                    <text x="35" y="45" font-size="12" fill="#000">Tòa nhà</text>

                    <!-- Biểu tượng bài đăng -->
                    <text x="20" y="80" font-family="FontAwesome" font-size="20" fill="#ff0000"
                        text-anchor="middle">&#xf3c5;</text>
                    <text x="35" y="80" font-size="12" fill="#000">Vị trí bài đăng/Đồ vật</text>
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
                <g class="building" aria-label="Building K">
                    <rect x="82" y="60" width="200" height="60" fill="#3b82f6" rx="6" />
                    <text x="178" y="93" text-anchor="middle" font-size="15" fill="#fff">NHÀ K</text>
                </g>
                <g class="building" aria-label="Building H - Student Dormitory">
                    <rect x="350" y="60" width="200" height="60" fill="#3b82f6" rx="6" />
                    <text x="450" y="83" text-anchor="middle" font-size="15" fill="#fff">NHÀ H</text>
                    <text x="450" y="103" text-anchor="middle" font-size="12" fill="#fff">KÝ TÚC XÁ SV</text>
                </g>
                <g class="building" aria-label="Building I">
                    <rect x="620" y="60" width="200" height="60" fill="#3b82f6" rx="6" />
                    <text x="715" y="93" text-anchor="middle" font-size="15" fill="#fff">NHÀ I</text>
                </g>
                <g class="building" aria-label="Building E">
                    <rect x="70" y="140" width="220" height="200" fill="#000080" rx="6" />
                    <text x="180" y="250" text-anchor="middle" font-size="22" fill="#fff">NHÀ E</text>
                </g>
                <g class="building" aria-label="Parking Area">
                    <rect x="315" y="190" width="150" height="160" fill="none" stroke="#000"
                        stroke-width="3" stroke-dasharray="8 6" rx="8" />
                    <rect x="315" y="190" width="150" height="160" fill="#ffffff" rx="6" />
                    <text x="390" y="265" text-anchor="middle" font-size="16" fill="#000">NHÀ XE</text>
                </g>
                <g class="building" aria-label="Building A">
                    <rect x="60" y="420" width="725" height="60" fill="#0b4a6f" rx="4" />
                    <rect x="420" y="390" width="20" height="80" fill="#0b4a6f" />
                    <rect x="400" y="450" width="60" height="80" fill="#0b4a6f" />
                    <rect x="400" y="375" width="60" height="20" fill="#0b4a6f" />
                    <text x="430" y="455" text-anchor="middle" font-size="14" fill="#fff">NHÀ A</text>
                </g>
                <g class="building" aria-label="Library">
                    <rect x="515" y="195" width="80" height="225" fill="#b91c1c" rx="4" />
                    <text x="556" y="310" text-anchor="middle" font-size="14" fill="#fff">THƯ VIỆN</text>
                </g>
                <g class="building" aria-label="Building G">
                    <rect x="595" y="195" width="190" height="85" fill="#1f2937" rx="4" />
                    <text x="685" y="240" text-anchor="middle" font-size="14" fill="#fff">NHÀ G</text>
                </g>
                <g class="building" aria-label="Building M">
                    <rect x="705" y="280" width="80" height="140" fill="#10b981" rx="4" />
                    <text x="748" y="350" text-anchor="middle" font-size="14" fill="#fff">NHÀ M</text>
                </g>
                <g class="building" aria-label="Building B">
                    <rect x="705" y="480" width="80" height="120" fill="#f59e0b" rx="4" />
                    <text x="748" y="540" text-anchor="middle" font-size="14" fill="#fff">NHÀ B</text>
                </g>
                <g class="building" aria-label="Security Post">
                    <rect x="320" y="630" width="60" height="60" fill="#7f1d1d" rx="4" />
                    <text x="350" y="665" text-anchor="middle" font-size="12" fill="#fff">BẢO VỆ</text>
                </g>

                <text x="420" y="720" text-anchor="middle" font-size="23" fill="#b91c1c">CỔNG CHÍNH</text>
                <text x="840" y="720" text-anchor="middle" font-size="23" fill="#b91c1c">CỔNG PHỤ</text>

                <!-- Dynamic location dots for each post -->
                @foreach ($buildings as $building)
                    @php
                        // Fallback coordinates if building code not found
                        $coords = $buildingCoordinates[$building->code] ?? ['x' => 0, 'y' => 0];
                        $postCount = $building->posts->count();
                        $offset = 15; // Distance between icons
                    @endphp
                    @foreach ($building->posts as $index => $post)
                        @php
                            // Arrange icons in a horizontal line or grid
                            $xOffset = $index * $offset;
                            $yOffset = 0;
                            if ($postCount > 5) {
                                // For more than 5 posts, use a grid (2 rows)
                                $xOffset = ($index % 5) * $offset;
                                $yOffset = floor($index / 5) * $offset;
                            }
                            $media = $post->media->first();
                            $mediaUrl = $media
                                ? asset('storage/' . $media->media_path)
                                : asset('images/placeholder.webp');
                            $mediaType = $media && strpos($media->media_path, '.mp4') !== false ? 'video' : 'image';
                            $roomName = $post->room ? e($post->room->name) : 'No Room';
                            // Sanitize username for URL
                            $username = Str::slug($post->user->mention ?? 'anonymous');
                            $postUrl = route('posts.show', ['user' => $username, 'post' => $post->slug]);
                            // Sanitize data for JSON
                            $popupData = json_encode(
                                [
                                    'x' => $coords['x'] + $xOffset,
                                    'y' => $coords['y'] + $yOffset,
                                    'media' => $mediaUrl,
                                    'mediaType' => $mediaType,
                                    'room' => $roomName,
                                    'url' => $postUrl,
                                ],
                                JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP,
                            );
                        @endphp
                        <g class="location-dot" data-post-id="{{ $post->id }}" role="button" tabindex="0"
                            aria-label="View post at {{ $roomName }}"
                            onclick="openPreviewPopup({{ $popupData }})"
                            onkeydown="if(event.key === 'Enter') openPreviewPopup({{ $popupData }})">
                            <text x="{{ $coords['x'] + $xOffset }}" y="{{ $coords['y'] + $yOffset }}"
                                font-family="FontAwesome" font-size="20" fill="#ff0000" text-anchor="middle"
                                dominant-baseline="middle">
                                &#xf3c5; <!-- Unicode for fa-location-dot -->
                            </text>
                        </g>
                    @endforeach
                @endforeach
            </svg>
        </div>
    </div>
</div>

<!-- Preview Popup -->
<div id="previewPopup" class="hidden fixed bg-white rounded-lg shadow-lg p-2 z-[60]" role="dialog"
    aria-label="Post Preview">
    <a id="previewLink" href="#" class="block text-decoration-none text-inherit">
        <div id="previewMedia" class="w-32 h-32 object-cover rounded"></div>
        <p id="previewRoom" class="text-center text-sm font-medium mt-1"></p>
    </a>
</div>

<style>
    .location-dot {
        transition: transform 0.2s ease, filter 0.2s ease;
        cursor: pointer;
        transform-box: fill-box;
        transform-origin: center;
    }

    .location-dot:hover,
    .location-dot:focus {
        transform: scale(1.1);
        filter: brightness(1.1);
        outline: none;
    }

    #previewPopup {
        width: 145px;
        text-align: center;
        pointer-events: auto;
        z-index: 60;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        transition: opacity 0.3s ease;
    }

    #previewLink {
        display: block;
        text-decoration: none;
        color: inherit;
    }

    #previewMedia video,
    #previewMedia img {
        width: 100%;
        height: 100%;
        object-fit: cover;
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
        const mapPopup = document.getElementById("mapPopup");
        mapPopup.classList.remove("hidden");
        mapPopup.focus();
        closePreviewPopup();
    }

    function closeMapPopup() {
        document.getElementById("mapPopup").classList.add("hidden");
        closePreviewPopup();
    }

    function openPreviewPopup(data) {
        const previewPopup = document.getElementById("previewPopup");
        const previewMedia = document.getElementById("previewMedia");
        const popupWidth = 200;
        const popupHeight = 180;
        const marginRight = 20;

        // Position the popup on the right side, centered vertically
        const screenX = window.innerWidth - popupWidth - marginRight;
        const screenY = (window.innerHeight - popupHeight) / 2;

        previewPopup.style.left = `${screenX}px`;
        previewPopup.style.top = `${screenY}px`;
        previewPopup.classList.remove("hidden");

        // Clear previous media content
        previewMedia.innerHTML = '';

        // Create media element based on mediaType
        if (data.mediaType === 'video') {
            const video = document.createElement('video');
            video.src = data.media;
            video.controls = true;
            video.autoplay = false;
            video.muted = true; // Muted to avoid autoplay issues
            video.className = 'w-full h-full object-cover rounded';
            video.onerror = () => {
                const img = document.createElement('img');
                img.src = '{{ asset('images/placeholder.png') }}';
                img.alt = 'Placeholder image';
                img.className = 'w-full h-full object-cover rounded';
                previewMedia.innerHTML = '';
                previewMedia.appendChild(img);
            };
            previewMedia.appendChild(video);
        } else {
            const img = document.createElement('img');
            img.src = data.media;
            img.alt = 'Post preview image';
            img.className = 'w-full h-full object-cover rounded';
            img.onerror = () => {
                img.src = '{{ asset('images/placeholder.png') }}';
            };
            previewMedia.appendChild(img);
        }

        const previewLink = document.getElementById("previewLink");
        previewLink.href = data.url;
        document.getElementById("previewRoom").textContent = data.room;
    }

    function closePreviewPopup() {
        document.getElementById("previewPopup").classList.add("hidden");
        document.getElementById("previewMedia").innerHTML = '';
    }

    // Close preview when clicking outside
    document.addEventListener('click', function(event) {
        const previewPopup = document.getElementById("previewPopup");
        const isClickInsidePopup = previewPopup.contains(event.target);
        const isClickOnIcon = event.target.closest('.location-dot');
        if (!isClickInsidePopup && !isClickOnIcon) {
            closePreviewPopup();
        }
    });

    // Close popups on ESC key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeMapPopup();
            closePreviewPopup();
        }
    });

    // Add keyboard support for map trigger
    document.querySelector('[onclick="openMapPopup()"]').addEventListener('keydown', function(event) {
        if (event.key === 'Enter' || event.key === ' ') {
            event.preventDefault();
            openMapPopup();
        }
    });

    // Reposition popup on window resize
    window.addEventListener('resize', function() {
        const previewPopup = document.getElementById("previewPopup");
        if (!previewPopup.classList.contains("hidden")) {
            const popupWidth = 200;
            const popupHeight = 180;
            const marginRight = 40;
            previewPopup.style.left = `${window.innerWidth - popupWidth - marginRight}px`;
            previewPopup.style.top = `${(window.innerHeight - popupHeight) / 2}px`;
        }
    });
</script>
