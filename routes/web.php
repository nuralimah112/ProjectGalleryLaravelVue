<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\CommentController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Welcome Route
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Dashboard Route - Modified to use PhotoController
Route::get('/dashboard', [PhotoController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile settings routes
    Route::get('/setting', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/setting', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/setting', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/upload-photo', [ProfileController::class, 'updatePhoto'])->name('profile.uploadPhoto');

    // Photos upload route
    Route::get('/photos/upload', function () {
        return Inertia::render('Photos/UploadPhoto');
    })->name('photos.upload');

    Route::get('/profile/{name}', [ProfileController::class, 'show'])->name('profile.show');
    Route::delete('/photos/{photo}', [PhotoController::class, 'destroy'])->name('photos.destroy');
    Route::put('/photos/{photo}', [PhotoController::class, 'update'])->name('photos.update');
    // Route to like/unlike photo
    Route::post('/photos/{photo}/like', [PhotoController::class, 'like'])->middleware('auth');


    // Menambahkan route untuk melihat foto detail
Route::get('/photos/{photo}', [PhotoController::class, 'photoshow'])->name('photos.show');

Route::get('/photos/{photo}/comments', [CommentController::class, 'index']); // Retrieve comments for a photo
Route::post('/photos/{photo}/comments', [CommentController::class, 'store']); // Add a comment
Route::delete('/comments/{comment}', [CommentController::class, 'destroy']); // Delete a comment

// Route to delete a user (only accessible to admin users)
Route::delete('/account/{id}', [ProfileController::class, 'destroyUser'])->name('account.destroy')->middleware('auth');

// Route to delete a photo (only accessible to admin users or the user who uploaded it)
Route::delete('/photos/{photo}', [PhotoController::class, 'destroyPhoto'])->name('photos.destroy')->middleware('auth');
// Add this route to your routes/web.php or routes/api.php
Route::get('/users', [ProfileController::class, 'getUsers'])->middleware('auth');


});

// Photos routes
Route::post('/photos', [PhotoController::class, 'store'])->name('photos.store')->middleware('auth');


// Removed duplicate profile.show route from AccountController
Route::get('/account/{id}', [AccountController::class, 'show'])->name('account');

// Test route
Route::get('/test-upload', function () {
    return 'Upload route is reachable.';
});

// Include auth routes
require __DIR__.'/auth.php';
