<?php

use App\Http\Controllers\Api\Chat\MessageController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});




Route::middleware(['auth'])->group(function () {
    Route::get('/chat/{recipient}', [MessageController::class, 'show'])->name('chat');
});


Route::prefix('api')->middleware('auth:sanctum')->group(function () {
    Route::post('/online', [MessageController::class, 'setOnline']);
    Route::post('/offline', [MessageController::class, 'setOffline']);
    Route::prefix('chat')->group(function () {
        Route::get('/messages/{user}', [MessageController::class, 'index']);
        // Route::post('/messages/{user}', [MessageController::class, 'store']);
        Route::post('/messages/{user}/read', [MessageController::class, 'markAsRead']);
        Route::post('/typing', [MessageController::class, 'typing']);


    });
});
Route::get('/users', [MessageController::class, 'users']);
require __DIR__ . '/auth.php';
