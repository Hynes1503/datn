<!-- resources/views/components/navbar.blade.php -->
<nav class="fixed top-0 left-0 h-full w-20 bg-white border-r border-gray-200 flex flex-col items-center py-6 z-50">
    @include('layouts._logo')
    @include('layouts._nav-items')
</nav>

@include('layouts._post-modal')

<style>
    /* Styling for modal */
    #postModal {
        z-index: 1000;
        position: fixed;
        inset: 0;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    #postModal.hidden {
        display: none;
    }

    .modal-form textarea {
        width: 100%;
        min-height: 100px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 12px;
        resize: vertical;
        font-size: 14px;
        color: #1f2937;
    }

    .modal-form textarea:focus {
        outline: none;
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .modal-form input[type="text"] {
        width: 100%;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 8px 12px;
        font-size: 14px;
        color: #1f2937;
        margin-top: 8px;
    }

    .modal-form input[type="text"]:focus {
        outline: none;
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .modal-form input[type="file"] {
        width: 100%;
        margin-top: 8px;
        font-size: 14px;
        color: #1f2937;
    }

    .modal-form button {
        margin-top: 12px;
        padding: 8px 16px;
        background-color: #2563eb;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: medium;
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .modal-form button:hover {
        background-color: #1d4ed8;
    }

    .modal-form button:disabled {
        background-color: #9ca3af;
        cursor: not-allowed;
    }

    .modal-form .cancel-button {
        background-color: #e5e7eb;
        color: #1f2937;
    }

    .modal-form .cancel-button:hover {
        background-color: #d1d5db;
    }

    #mediaPreview {
        display: flex;
        flex-direction: row;
        gap: 8px;
        overflow-x: auto;
        white-space: nowrap;
    }

    #mediaPreview img,
    #mediaPreview video {
        max-width: 100px;
        max-height: 100px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        flex-shrink: 0;
    }

    /* Styling for notifications dropdown */
    #notificationMenu {
        max-height: 24rem;
        overflow-y: auto;
        scrollbar-width: thin;
        scrollbar-color: #d1d5db #f3f4f6;
    }

    #notificationMenu::-webkit-scrollbar {
        width: 6px;
    }

    #notificationMenu::-webkit-scrollbar-track {
        background: #f3f4f6;
    }

    #notificationMenu::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 3px;
    }

    #notificationMenu .notification-item {
        border-bottom: 1px solid #e5e7eb;
    }

    #notificationMenu .notification-item:last-child {
        border-bottom: none;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const avatarBtn = document.getElementById('avatarMenuBtn');
        const avatarMenu = document.getElementById('avatarMenu');
        const notificationBtn = document.getElementById('notificationBtn');
        const notificationMenu = document.getElementById('notificationMenu');

        // Toggle avatar menu
        avatarBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            avatarMenu.classList.toggle('hidden');
            notificationMenu.classList.add('hidden'); // Close notification menu if open
        });

        // Toggle notification menu
        notificationBtn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            notificationMenu.classList.toggle('hidden');
            avatarMenu.classList.add('hidden'); // Close avatar menu if open
        });

        // Close menus when clicking outside
        document.addEventListener('click', (e) => {
            if (!avatarMenu.classList.contains('hidden') && !avatarMenu.contains(e.target) && e.target !== avatarBtn) {
                avatarMenu.classList.add('hidden');
            }
            if (!notificationMenu.classList.contains('hidden') && !notificationMenu.contains(e.target) && e.target !== notificationBtn) {
                notificationMenu.classList.add('hidden');
            }
        });

        // Modal logic
        const modal = document.getElementById('postModal');
        const openModalButtons = document.querySelectorAll('.open-post-modal, #openPostModal');
        const closeModalBtn = document.getElementById('closePostModal');
        const mediaInput = document.getElementById('mediaInput');
        const mediaPreview = document.getElementById('mediaPreview');

        openModalButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                modal.classList.remove('hidden');
            });
        });

        closeModalBtn.addEventListener('click', () => {
            modal.classList.add('hidden');
            mediaPreview.innerHTML = ''; // Clear preview when closing
        });

        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.add('hidden');
                mediaPreview.innerHTML = ''; // Clear preview when closing
            }
        });

        // Preview media
        mediaInput.addEventListener('change', (e) => {
            mediaPreview.innerHTML = ''; // Clear previous previews
            const files = e.target.files;
            for (const file of files) {
                if (file.type.startsWith('image/')) {
                    const img = document.createElement('img');
                    img.src = URL.createObjectURL(file);
                    mediaPreview.appendChild(img);
                } else if (file.type.startsWith('video/')) {
                    const video = document.createElement('video');
                    video.src = URL.createObjectURL(file);
                    video.controls = true;
                    mediaPreview.appendChild(video);
                }
            }
        });
    });
</script>