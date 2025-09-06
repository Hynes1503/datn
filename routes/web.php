<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\MainController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OCRController;
use App\Http\Controllers\User\LoginController;
use App\Http\Controllers\User\PostController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\CommentController;

Route::get('/', [MainController::class, 'index'])->name('threads.index');
Route::post('/post', [MainController::class, 'post'])->name('threads.post');
Route::post('/post/{id}/like', [MainController::class, 'like'])->name('threads.like');
Route::post('/post/{id}/reply', [MainController::class, 'reply'])->name('threads.reply');
Route::post('/follow/{id}', [MainController::class, 'follow'])->name('threads.follow');
Route::post('/load-more', [MainController::class, 'loadMore'])->name('threads.loadMore');

Route::get('/register', [OCRController::class, 'showForm'])->name('auth.register');
Route::post('/register/submit', [OCRController::class, 'submit'])->name('register.submit');
Route::post('/register/upload', [OCRController::class, 'uploadImage'])->name('register.upload');
Route::get('/register/upload', function () {
    return redirect()->route('auth.register')->with('error', 'Truy cập không hợp lệ. Vui lòng tải lên thẻ sinh viên qua form.');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/', [PostController::class, 'home'])->middleware('auth')->name('home')->middleware('auth.custom');
Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
Route::post('/posts', [PostController::class, 'store'])->name('posts.store');

// Profile user (direct mention)
Route::get('/{user:mention}', [UserController::class, 'show'])->name('users.show');
Route::get('/{user:mention}/edit', [UserController::class, 'edit'])->name('users.edit')->middleware('auth');
Route::put('/{user:mention}', [UserController::class, 'update'])->name('users.update')->middleware('auth');
Route::delete('/{user:mention}', [UserController::class, 'destroy'])->name('users.destroy')->middleware('auth');

// Post của user
Route::prefix('{user:mention}/{post:slug}')->group(function () {
    Route::get('/', [PostController::class, 'show'])->name('posts.show');
    Route::get('/edit', [PostController::class, 'edit'])->name('posts.edit')->middleware('auth');
    Route::put('/', [PostController::class, 'update'])->name('posts.update')->middleware('auth');
    Route::delete('/', [PostController::class, 'destroy'])->name('posts.destroy')->middleware('auth');
    // Comment routes
    Route::post('/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
});