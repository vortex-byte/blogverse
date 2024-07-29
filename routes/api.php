<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\PostController;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\TagController;
use App\Http\Middleware\EnsureTokenIsValid;

Route::controller(RegisterController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login')->name('login');
    Route::get('user/{id}', 'user');
    Route::get('author/{id}', 'author');
});

Route::controller(PostController::class)->group(function () {
    Route::get('posts', 'index');
    Route::get('post/search', 'search');
    Route::get('post/{slug}', 'show');
});

Route::controller(TagController::class)->group(function () {
    Route::get('tags', 'index');
    Route::get('tag/{slug}', 'show');
});

Route::middleware(EnsureTokenIsValid::class)->group(function () {
    Route::post('post', [PostController::class, 'store']);
    Route::put('post/{post}', [PostController::class, 'update']);
    Route::delete('post/{post}', [PostController::class, 'destroy']);
});
