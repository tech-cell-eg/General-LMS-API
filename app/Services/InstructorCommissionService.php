<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class InstructorCommissionService
{
    public function getCommissionStats(int $instructorId)
    {
        return DB::table('order_items')
            ->join('courses', 'order_items.course_id', '=', 'courses.id')
            ->where('courses.instructor_id', $instructorId)
            ->selectRaw('SUM(order_items.final_price) as total_value')
            ->selectRaw('SUM(order_items.final_price * 0.7) as total_commission')
            ->selectRaw('SUM(CASE WHEN orders.payment_status = "paid" THEN order_items.final_price * 0.7 ELSE 0 END) as received_commission')
            ->selectRaw('SUM(CASE WHEN orders.payment_status != "paid" THEN order_items.final_price * 0.7 ELSE 0 END) as pending_commission')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->first();
    }

    public function getCommissionHistory(int $instructorId, int $perPage = 10)
    {
        return DB::table('order_items')
            ->join('courses', 'order_items.course_id', '=', 'courses.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->where('courses.instructor_id', $instructorId)
            ->select([
                'orders.id as order_id',
                'users.first_name as customer_first_name',
                'users.last_name as customer_last_name',
                'courses.title as course',
                'order_items.final_price',
                'orders.payment_status',
                'orders.created_at as date',
                DB::raw('order_items.final_price * 0.7 as commission')
            ])
            ->orderBy('orders.created_at', 'desc')
            ->paginate($perPage);
    }
}
