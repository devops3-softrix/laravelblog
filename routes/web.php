<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;

// Public routes
Route::get('/', [PostController::class, 'index'])->name('home');
Route::get('/post/{slug}', [PostController::class, 'show'])->name('post.show');
Route::post('/post/{post}/comment', [PostController::class, 'comment'])->name('post.comment');

// Auth routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register')->middleware('guest');
Route::post('/register', [AuthController::class, 'register'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Admin routes (auth protected)
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/',                            [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/posts',                       [AdminController::class, 'posts'])->name('posts');
    Route::get('/posts/create',                [AdminController::class, 'createPost'])->name('post.create');
    Route::post('/posts',                      [AdminController::class, 'storePost'])->name('post.store');
    Route::get('/posts/{post}/edit',           [AdminController::class, 'editPost'])->name('post.edit');
    Route::put('/posts/{post}',                [AdminController::class, 'updatePost'])->name('post.update');
    Route::delete('/posts/{post}',             [AdminController::class, 'deletePost'])->name('post.delete');
    Route::put('/posts/{post}/approve',        [AdminController::class, 'approvePost'])->name('post.approve');
    Route::put('/posts/{post}/reject',         [AdminController::class, 'rejectPost'])->name('post.reject');
    Route::get('/comments',                    [AdminController::class, 'comments'])->name('comments');
    Route::put('/comments/{comment}/approve',  [AdminController::class, 'approveComment'])->name('comment.approve');
    Route::delete('/comments/{comment}',       [AdminController::class, 'deleteComment'])->name('comment.delete');
    Route::get('/users',                       [AdminController::class, 'users'])->name('users');
    Route::put('/users/{user}/role',           [AdminController::class, 'updateUserRole'])->name('user.role');
});
