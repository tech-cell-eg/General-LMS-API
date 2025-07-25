<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Dashboard\CouponController;
use App\Http\Controllers\Api\Dashboard\InstructorCommisionController;
use App\Http\Controllers\Api\Dashboard\InstructorCoursesController;
use App\Http\Controllers\Api\Dashboard\InstructorReviewesController;
use App\Http\Controllers\Api\Dashboard\InstructorStudentsController;
use App\Http\Controllers\Api\Dashboard\LessonController;
use App\Http\Controllers\Api\Dashboard\LmsAnalyticsController;
use App\Http\Controllers\Api\Dashboard\ResourceController;
use App\Http\Controllers\Api\Dashboard\RevenueController;
use App\Http\Controllers\Api\Dashboard\ReviewController as DashboardReviewController;
use App\Http\Controllers\Api\Dashboard\SectionController;
use App\Http\Controllers\Api\Dashboard\SectionResourceController;
use App\Http\Controllers\Api\Dashboard\SectionSeoController;
use App\Http\Controllers\Api\Student\CartController;
use App\Http\Controllers\Api\Student\CategoryController;
use App\Http\Controllers\Api\Student\CheckoutController;
use App\Http\Controllers\Api\Student\CoursesController;
use App\Http\Controllers\Api\Student\InstructorController;
use App\Http\Controllers\Api\Student\MyCourseController;
use App\Http\Controllers\Api\Student\PopularCategoriesController;

use App\Http\Controllers\Api\Student\ReviewController;
use App\Http\Controllers\Api\Student\TestimonialController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\ProfileController;
use App\Http\Controllers\Api\Auth\PasswordController;



Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);

    Route::get('profile', [ProfileController::class, 'show']);
    Route::put('profile', [ProfileController::class, 'update']);

    Route::put('password', [PasswordController::class, 'update']);
    Route::post('password/forgot', [PasswordController::class, 'forgot']);
    Route::post('password/reset', [PasswordController::class, 'reset']);
});
// Category Routes
Route::resource('categories', CategoryController::class)->only('index', 'show'); // Route
Route::get('categories/popular', PopularCategoriesController::class);
Route::get('courses', [CoursesController::class, 'index']);
Route::get('instructors/{instructor}/courses', \App\Http\Controllers\Api\Student\InstructorCoursesController::class);
Route::get('instructors', [InstructorController::class, 'index']);
Route::get('testimonials', [TestimonialController::class, 'index']);


Route::middleware('auth:sanctum')->group(callback: function () {
    Route::get('courses/{course}', [CoursesController::class, 'show']);

    // Cart routes
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart/add', [CartController::class, 'store']);
    Route::delete('/cart/remove/{itemId}', [CartController::class, 'destroy']);

    // Checkout route
    Route::post('/checkout', [CheckoutController::class, 'store']);


    // My Courses (Courses I'm enrolled in)
    Route::get('/my-courses', [MyCourseController::class, 'index']);

    // Course Details
    Route::get('/courses/{course}', [MyCourseController::class, 'show']);

    // Instructor Details
    Route::get('/instructors/{instructor}', [InstructorController::class, 'show']);

    // More courses by this instructor
    Route::get('/instructors/{instructor}/courses', [InstructorController::class, 'courses']);

    // My Reviews
    Route::get('/my-reviews', [ReviewController::class, 'index']);

    Route::post('/reviews/instructor/store', [ReviewController::class, 'store']);

});


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/instructor/commissions', [InstructorCommisionController::class, 'index']);
    // Route::get('/instructor/courses', [InstructorCoursesController::class, 'index']);
    Route::get('/instructor/reviews', [InstructorReviewesController::class, 'index']);
    Route::get('/instructor/customers', [InstructorStudentsController::class, 'index']);

    Route::get('instructor/courses/{courseId}/sections', [SectionController::class, 'index']);
    Route::get('instructor/sections/{sectionId}', [SectionController::class, 'show']);
    Route::put('instructor/sections/{sectionId}', [SectionController::class, 'update']);
    Route::get('instructor/sections/{sectionId}/resources', [SectionResourceController::class, 'index']);
    Route::post('instructor/sections/{sectionId}/resources', [SectionResourceController::class, 'store']);
    Route::put('instructor/resources/{resourceId}', [SectionResourceController::class, 'update']);

    Route::get('instructor/sections/{sectionId}/seo', [SectionSeoController::class, 'show']);
    Route::put('instructor/sections/{sectionId}/seo', [SectionSeoController::class, 'update']);


    // Lessons routes
    Route::get('instructor/sections/{sectionId}/lessons', [LessonController::class, 'index']);
    Route::get('instructor/lessons/{lessonId}', [LessonController::class, 'show']);
    Route::put('instructor/lessons/{lessonId}', [LessonController::class, 'update']);

});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('instructor/coupons', [CouponController::class, 'index']);
    Route::post('instructor/coupons', [CouponController::class, 'store']);
    Route::get('instructor/coupons/{coupon}', [CouponController::class, 'show']);
    Route::put('instructor/coupons/{coupon}', [CouponController::class, 'update']);
    Route::delete('instructor/coupons/{coupon}', [CouponController::class, 'destroy']);
});

Route::prefix('instructor')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/courses', [InstructorCoursesController::class, 'index']);
    Route::post('/courses', [InstructorCoursesController::class, 'store']);
    Route::get('/courses/{id}', [InstructorCoursesController::class, 'show']);
    Route::put('/courses/{id}', [InstructorCoursesController::class, 'update']);
    Route::delete('/courses/{id}', [InstructorCoursesController::class, 'destroy']);
    Route::post('/courses/{id}/publish', [InstructorCoursesController::class, 'publish']);
    Route::post('/courses/{id}/draft', [InstructorCoursesController::class, 'draft']);
});


Route::middleware('auth:sanctum')->group(function () {
    Route::get('instructor/all/reviews', [DashboardReviewController::class, 'index']);
    Route::get('instructor/reviews/{review}', [DashboardReviewController::class, 'show']);
    Route::post('instructor/reviews/{review}/answer', [DashboardReviewController::class, 'answer']);
    Route::get('instructor/reviews/export/csv', [DashboardReviewController::class, 'export']);

    Route::prefix('analytics')->group(function () {
        Route::get('/summary', [LmsAnalyticsController::class, 'getSummaryMetrics']);
        Route::get('/sales-trends', [LmsAnalyticsController::class, 'getSalesTrends']);
        Route::get('/course-performance', [LmsAnalyticsController::class, 'getCoursePerformance']);
        Route::get('/review-stats', [LmsAnalyticsController::class, 'getReviewStats']);
    });
    Route::get('/revenue/analytics', [RevenueController::class, 'analytics']);
});
