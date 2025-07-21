<?php

namespace App\Http\Controllers\Api\Dashboard;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\Course;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\InstructorReviewService;

class LmsAnalyticsController extends Controller
{
       protected $reviewService;

    public function __construct(InstructorReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
    }
    public function getSummaryMetrics()
    {
        // Calculate lifetime metrics
        $lifetimeCourseCommission = Order::where('payment_status', 'completed')
            ->sum('total') * 0.3; // Assuming 30% commission rate

        $lifetimeRequiredCommission = 200.00; // This might be a fixed value or calculated differently
        $lifetimePendingCommission = $lifetimeCourseCommission - $lifetimeRequiredCommission;

        // Calculate lifetime sales
        $lifetimeSales = Order::where('payment_status', 'completed')
            ->sum('total');

        return response()->json([
            'status' => 'success',
            'data' => [
                'commissions' => [
                    'lifetime_course_commission' => $lifetimeCourseCommission,
                    'lifetime_required_commission' => $lifetimeRequiredCommission,
                    'lifetime_pending_commission' => $lifetimePendingCommission,
                ],
                'sales' => [
                    'lifetime_sales' => $lifetimeSales,
                    'total_courses_sold' => OrderItem::count(),
                ]
            ]
        ]);
    }

    public function getSalesTrends(Request $request)
    {
        $days = $request->input('days', 30); // Default to 30 days
        $startDate = Carbon::now()->subDays($days);
        $endDate = Carbon::now();

        // Get daily sales data
        $dailySales = Order::where('payment_status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, SUM(total) as amount')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'period' => [
                    'start_date' => $startDate->format('Y-m-d'),
                    'end_date' => $endDate->format('Y-m-d'),
                ],
                'daily_sales' => $dailySales,
            ]
        ]);
    }

  public function getCoursePerformance(Request $request)
{
    $perPage = $request->input('per_page', 10);

    $courses = Course::withCount([
            'sections as total_chapters',
            'orderItems as total_orders',
            'enrollments as total_enrollments',
            'reviews as total_reviews'
        ])
        ->withSum('orderItems as total_revenue', 'final_price')
        ->withCount([
            'enrollments as total_certificates' => function($query) {
                $query->whereNotNull('completed_at');
            }
        ])
        ->withAvg('reviews', 'rating')
        ->orderBy('total_revenue', 'desc')
        ->paginate($perPage);

    return response()->json([
        'status' => 'success',
        'data' => $courses->map(function($course) {
            return [
                'course_id' => $course->id,
                'title' => $course->title,
                'price' => $course->price,
                'total_chapters' => $course->total_chapters,
                'total_orders' => $course->total_orders,
                'total_enrollments' => $course->total_enrollments,
                'total_certificates' => $course->total_certificates,
                'total_reviews' => $course->total_reviews,
                'average_rating' => round($course->reviews_avg_rating, 1),
                'total_revenue' => $course->total_revenue,
                'instructor' => $course->instructor->name ?? 'Unknown',
                'thumbnail_url' => $course->thumbnail_url
            ];
        }),
        'meta' => [
            'current_page' => $courses->currentPage(),
            'per_page' => $courses->perPage(),
            'total' => $courses->total(),
        ]
    ]);
}


  public function getReviewStats(Request $request)
    {
        try {
            $instructorId = $request->user()->id; // For authenticated instructor

            // If you need to get stats for any instructor (admin view)
            // $instructorId = $request->input('instructor_id', $request->user()->id);

            $stats = $this->reviewService->getRatingStats($instructorId);

            return response()->json([
                'status' => 'success',
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve review statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}