<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\MainController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OCRController;
use App\Http\Controllers\User\LoginController;

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

Route::get('/', function () {
    return view('home');
})->middleware('auth')->name('home')->middleware('auth.custom');;
