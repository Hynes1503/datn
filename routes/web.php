<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\MainController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OCRController;
use App\Http\Controllers\User\LoginController;
use App\Http\Controllers\User\PostController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\Admin\AdminController;

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

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ================= POSTS =================
Route::middleware('auth.custom')->group(function () {
    Route::get('/home', [PostController::class, 'home'])->name('home');
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
});

// ================= USERS =================
Route::prefix('{user:mention}')->group(function () {
    Route::get('/', [UserController::class, 'show'])->name('users.show');

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
});