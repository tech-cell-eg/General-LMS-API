<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Student\CartController;
use App\Http\Controllers\Api\Student\CategoryController;
use App\Http\Controllers\Api\Student\CheckoutController;
use App\Http\Controllers\Api\Student\CoursesController;
use App\Http\Controllers\Api\Student\InstructorController;
use App\Http\Controllers\Api\Student\MyCourseController;
use App\Http\Controllers\Api\Student\ReviewController;
use App\Http\Controllers\Api\Student\TestimonialController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'user']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::put('/profile/password', [AuthController::class, 'updatePassword']);
});

// Category Routes
Route::resource('categories', CategoryController::class)->only('index', 'show'); // Route
Route::get('courses', [CoursesController::class, 'index']);
Route::get('instructors', [InstructorController::class, 'index']);
Route::get('testimonials', [TestimonialController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('courses/{course}', [CoursesController::class, 'show']);
});

Route::middleware('auth:sanctum')->group(function () {
    // Cart routes
    Route::get('/cart', [CartController::class, 'getCart']);
    Route::post('/cart/add', [CartController::class, 'addToCart']);
    Route::delete('/cart/remove/{itemId}', [CartController::class, 'removeFromCart']);

    // Checkout route
    Route::post('/checkout', [CheckoutController::class, 'checkout']);
});

Route::middleware('auth:sanctum')->group(function () {
    // My Courses (Courses I'm enrolled in)
    Route::get('/my-courses', [MyCourseController::class, 'index']);

    // Course Details
    Route::get('/courses/{course}', [MyCourseController::class, 'show']);

    // Instructor Details
    Route::get('/instructors/{instructor}', [InstructorController::class, 'show']);

    // More courses by this instructor
    Route::get('/instructors/{instructor}/courses', [InstructorController::class, 'courses']);

    // My Reviews
    Route::get('/my-reviews', [ReviewController::class, 'myReviews']);
});
