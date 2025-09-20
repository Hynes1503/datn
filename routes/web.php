<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\MainController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OCRController;
use App\Http\Controllers\User\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\User\PostController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\Admin\CommentController as AdminCommentController;
use App\Http\Controllers\ReportController;
// Route gốc trả về trang login
Route::get('/', function () {
    return view('auth.login');
})->name('welcome');

// ================= ADMIN =================
Route::prefix('admin')->group(function () {
    // Login không qua middleware
    Route::get('/login', [AdminController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminController::class, 'login'])->name('admin.login.submit');

    // Các route cần đăng nhập admin
    Route::middleware('auth.admin')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');
        Route::post('/logout', [AdminController::class, 'logout'])->name('admin.logout');
        Route::get('/dashboard/chart-data', [AdminController::class, 'chartData'])->name('admin.dashboard.chart');
        Route::get('/buildings/create', [RoomController::class, 'createBuilding'])->name('admin.buildings.create');
        Route::post('/buildings', [RoomController::class, 'storeBuilding'])->name('admin.buildings.store');

        Route::get('/floors/create', [RoomController::class, 'createFloor'])->name('admin.floors.create');
        Route::post('/floors', [RoomController::class, 'storeFloor'])->name('admin.floors.store');
        Route::get('/get-floors/{building}', action: [RoomController::class, 'getFloors'])->name('admin.getFloors');
        Route::get('/rooms/create', [RoomController::class, 'createRoom'])->name('admin.rooms.create');
        Route::post('/rooms', [RoomController::class, 'storeRoom'])->name('admin.rooms.store');
        Route::resource('users', AdminUserController::class, ['as' => 'admin']);
        Route::resource('posts', AdminPostController::class, ['as' => 'admin']);
        Route::resource('comments', AdminCommentController::class, ['as' => 'admin']);
        Route::resource('/reports', ReportController::class)->only(['index', 'show', 'destroy']);
        Route::patch('reports/update-status', [ReportController::class, 'updateStatus'])->name('reports.updateStatus');
    });
});

// ================= AUTH & REGISTER =================
Route::get('/register', [OCRController::class, 'showForm'])->name('auth.register');
Route::post('/register/submit', [OCRController::class, 'submit'])->name('register.submit');
Route::post('/register/upload', [OCRController::class, 'uploadImage'])->name('register.upload');
Route::get(
    '/register/upload',
    fn() => redirect()->route('auth.register')->with('error', 'Truy cập không hợp lệ. Vui lòng tải lên thẻ sinh viên qua form.')
);
Route::get('/forgot-password', [ForgotPasswordController::class, 'showForm'])->name('password.form');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('password.send');
Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [ForgotPasswordController::class, 'reset'])->name('password.update');
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/hashtag/{hashtag}', [PostController::class, 'byHashtag'])->name('posts.byHashtag');
Route::get('/lost_item', [PostController::class, 'lost_index'])->name('posts.lostItem');

// ================= POSTS =================
Route::middleware('auth.custom')->group(function () {
    Route::get('/get-floors/{building}', [LocationController::class, 'getFloors']);
    Route::get('/get-rooms/{floor}', [LocationController::class, 'getRooms']);
    Route::get('/home', [PostController::class, 'home'])->name('home');
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
});

Route::get('/search', action: [SearchController::class, 'index'])->name('search');
// routes/web.php
Route::get('/search/suggestions', [SearchController::class, 'suggestions'])->name('search.suggestions');
Route::resource('/reports', ReportController::class)->only('store');
// ================= USERS =================
Route::prefix('{user:mention}')->group(function () {
    Route::get('/', [UserController::class, 'show'])->name('users.show');
    Route::get('/followers', [FollowController::class, 'getFollowers'])->name('users.followers');

    // Chỉ owner mới được sửa, xoá
    Route::middleware(['auth.custom', 'auth.owner'])->group(function () {
        Route::get('/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/', [UserController::class, 'update'])->name('users.update');
        Route::delete('/', [UserController::class, 'destroy'])->name('users.destroy');
    });
});

// ================= POSTS WITH USER =================
Route::prefix('{user:mention}/{post:slug}')->group(function () {
    Route::get('/', [PostController::class, 'show'])->name('posts.show');

    // Chỉ owner mới được sửa, xoá
    Route::middleware(['auth.custom', 'auth.owner'])->group(function () {
        Route::get('/edit', [PostController::class, 'edit'])->name('posts.edit');
        Route::put('/', [PostController::class, 'update'])->name('posts.update');
        Route::delete('/', [PostController::class, 'destroy'])->name('posts.destroy');
    });

    // Like chỉ cần đăng nhập
    Route::post('/like', [PostController::class, 'toggleLike'])->name('posts.like')->middleware('auth.custom');

    // Comment
    Route::post('/comments', [CommentController::class, 'store'])->name('comments.store')->middleware('auth.custom');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy')->middleware('auth.custom');
    Route::put('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update')->middleware('auth.custom');
});

// routes/web.php

Route::middleware(['auth'])->group(function () {
    Route::post('/follow/{user}', [FollowController::class, 'follow'])->name('follow');
    Route::post('/unfollow/{user}', [FollowController::class, 'unfollow'])->name('unfollow');
    Route::post('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
});
