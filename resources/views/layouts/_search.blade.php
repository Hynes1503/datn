<!-- Nút mở popup -->
<a href="#" class="text-gray-900 hover:scale-110 hover:bg-gray-100 p-2 rounded-full transition-all" 
   title="Tìm kiếm" onclick="toggleSearchPopup(event)">
    <i class="fa-solid fa-magnifying-glass"></i>
</a>

<!-- Popup -->
<div class="search-popup" id="searchPopup">
    <form action="{{ route('search') }}" method="GET" class="search-container">
        <i class="fa-solid fa-magnifying-glass search-icon"></i>
        <input type="text" name="q" placeholder="Tìm kiếm..." required>
        <button type="button" class="close-btn" onclick="toggleSearchPopup(event)">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </form>
</div>

<!-- CSS -->
<style>
    /* Popup overlay */
    .search-popup {
        display: none;
        position: fixed;
        inset: 0;
        background-color: rgba(0, 0, 0, 0.5);
        justify-content: center;
        align-items: start;
        padding-top: 15vh; /* đẩy xuống một chút cho đẹp */
        z-index: 1000;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .search-popup.active {
        display: flex;
        opacity: 1;
    }

    /* Khung container */
    .search-container {
        background-color: #fff;
        width: 90%;
        max-width: 500px;
        border-radius: 9999px;
        padding: 12px 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        position: relative;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .search-icon {
        color: #888;
        font-size: 18px;
    }

    .search-container input {
        flex: 1;
        border: none;
        outline: none;
        font-size: 16px;
        background: transparent;
    }

    .search-container input::placeholder {
        color: #aaa;
    }

    .close-btn {
        background: none;
        border: none;
        font-size: 20px;
        color: #888;
        cursor: pointer;
        transition: color 0.2s;
    }

    .close-btn:hover {
        color: #000;
    }
</style>

<!-- JS -->
<script>
    function toggleSearchPopup(event) {
        event.preventDefault();
        const popup = document.getElementById('searchPopup');
        popup.classList.toggle('active');
    }

    // Click outside để đóng
    document.addEventListener('click', function(event) {
        const popup = document.getElementById('searchPopup');
        const searchContainer = document.querySelector('.search-container');

        if (popup.classList.contains('active') && !searchContainer.contains(event.target) && !event.target.closest('a[title="Tìm kiếm"]')) {
            popup.classList.remove('active');
        }
    });
</script>
