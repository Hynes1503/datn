<div class="p-4">
    <div class="bg-white rounded-lg shadow-md overflow-hidden" onclick="openMapPopup()" style="cursor: pointer">
        <img src="{{ asset('images/minmap.png') }}" alt="Mini map">
    </div>
</div>

<div id="mapPopup" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg max-w-6xl w-[60%] h-[85%] p-4 relative">
        <button onclick="closeMapPopup()"
            class="absolute top-2 right-2 bg-red-500 text-white px-3 py-1 rounded-full">X</button>

        <div class="w-full h-full overflow-auto">
            <svg viewBox="0 0 900 760" width="100%" height="100%" preserveAspectRatio="xMidYMid meet"
                aria-label="Bản đồ khu vực Khoa Điện tử Viễn thông - Trường Đại học Điện lực Hà Nội">
                <rect x="0" y="0" width="900" height="760" fill="#ffffff" />

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

                <g class="building">
                    <rect x="82" y="60" width="200" height="60" fill="#3b82f6" rx="6" />
                    <text x="178" y="93" text-anchor="middle" font-size="15" fill="#fff">NHÀ K</text>
                </g>

                <g class="building">
                    <rect x="350" y="60" width="200" height="60" fill="#3b82f6" rx="6" />
                    <text x="450" y="83" text-anchor="middle" font-size="15" fill="#fff">NHÀ H</text>
                    <text x="450" y="103" text-anchor="middle" font-size="12" fill="#fff">KÝ TÚC XÁ SV</text>
                </g>

                <g class="building">
                    <rect x="620" y="60" width="200" height="60" fill="#3b82f6" rx="6" />
                    <text x="715" y="93" text-anchor="middle" font-size="15" fill="#fff">NHÀ I</text>
                </g>

                <g class="building">
                    <rect x="70" y="140" width="220" height="200" fill="#000080" rx="6" />
                    <text x="180" y="250" text-anchor="middle" font-size="22" fill="#fff">NHÀ E</text>
                </g>

                <g class="building">
                    <rect x="315" y="190" width="150" height="160" fill="none" stroke="#000"
                        stroke-width="3" stroke-dasharray="8 6" rx="8" />
                    <rect x="315" y="190" width="150" height="160" fill="#ffffff" rx="6" />
                    <text x="390" y="265" text-anchor="middle" font-size="16" fill="#000">NHÀ XE</text>
                </g>

                <g class="building">
                    <rect x="60" y="420" width="725" height="60" fill="#0b4a6f" rx="4" />
                    <rect x="420" y="390" width="20" height="80" fill="#0b4a6f" />
                    <rect x="400" y="450" width="60" height="80" fill="#0b4a6f" />
                    <rect x="400" y="375" width="60" height="20" fill="#0b4a6f" />
                    <text x="430" y="455" text-anchor="middle" font-size="14" fill="#fff">NHÀ A</text>
                </g>

                <g class="building">
                    <rect x="515" y="195" width="80" height="225" fill="#b91c1c" rx="4" />
                    <text x="556" y="310" text-anchor="middle" font-size="14" fill="#fff">THƯ VIỆN</text>
                </g>

                <g class="building">
                    <rect x="595" y="195" width="190" height="85" fill="#1f2937" rx="4" />
                    <text x="685" y="240" text-anchor="middle" font-size="14" fill="#fff">NHÀ G</text>
                </g>

                <g class="building">
                    <rect x="705" y="280" width="80" height="140" fill="#10b981" rx="4" />
                    <text x="748" y="350" text-anchor="middle" font-size="14" fill="#fff">NHÀ M</text>
                </g>

                <g class="building">
                    <rect x="705" y="480" width="80" height="120" fill="#f59e0b" rx="4" />
                    <text x="748" y="540" text-anchor="middle" font-size="14" fill="#fff">NHÀ B</text>
                </g>

                <g class="building">
                    <rect x="320" y="630" width="60" height="60" fill="#7f1d1d" rx="4" />
                    <text x="350" y="665" text-anchor="middle" font-size="12" fill="#fff">BẢO VỆ</text>
                </g>

                <text x="420" y="720" text-anchor="middle" font-size="23" fill="#b91c1c">CỔNG CHÍNH</text>
                <text x="840" y="720" text-anchor="middle" font-size="23" fill="#b91c1c">CỔNG PHỤ</text>
            </svg>
        </div>
    </div>
</div>

<style>
    .building {
        transition: transform 0.2s ease, filter 0.2s ease;
        cursor: pointer;
        transform-box: fill-box;
        transform-origin: center;
    }

    .building:hover {
        transform: scale(1.1);
        filter: brightness(1.1);
    }
</style>


<script>
    function openMapPopup() {
        document.getElementById("mapPopup").classList.remove("hidden");
    }

    function closeMapPopup() {
        document.getElementById("mapPopup").classList.add("hidden");
    }
</script>
