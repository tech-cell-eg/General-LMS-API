<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RevenueController extends Controller
{
    public function analytics(Request $request)
    {
        $user = $request->user();

        // Calculate analytics for the last 30 days by default
        $days = $request->input('days', 30);
        $startDate = Carbon::now()->subDays($days);
        $endDate = Carbon::now();

        // Total Profits (sum of all completed order totals)
        $totalProfits = Order::where('payment_status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total');

        // Last Transaction Amount
        $lastTransaction = Order::where('payment_status', 'completed')
            ->orderBy('created_at', 'desc')
            ->first();

        // Debit (sum of refunded amounts - you'll need to implement refund logic)
        $debit = 0; // Placeholder - implement your refund logic here

        // Recent Transactions
        $transactions = Order::with(['user', 'items.course'])
            ->where('payment_status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function ($order) {
                return [
                    'customer' => $order->user->first_name . ' ' . $order->user->last_name,
                    'date' => $order->created_at->format('Y-m-d H:i:s'),
                    'type' => 'Credit',
                    'amount' => $order->total,
                    'courses' => $order->items->map(function ($item) {
                        return $item->course->title;
                    })->implode(', ')
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => [
                'analytics' => [
                    'total_profits' => $totalProfits,
                    'last_transaction_amount' => $lastTransaction ? $lastTransaction->total : 0,
                    'debit' => $debit,
                    'period' => [
                        'start_date' => $startDate->format('Y-m-d'),
                        'end_date' => $endDate->format('Y-m-d')
                    ]
                ],
                'transactions' => $transactions
            ]
        ]);
    }
}
