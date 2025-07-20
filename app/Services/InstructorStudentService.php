<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class InstructorStudentService
{
    public function getStudentsForInstructor(int $instructorId, int $perPage = 10): LengthAwarePaginator
    {
        return User::whereHas('orders.items.course', function ($query) use ($instructorId) {
                $query->where('instructor_id', $instructorId);
            })
            ->with([
                'orders' => function ($query) use ($instructorId) {
                    $query->whereHas('items.course', function ($q) use ($instructorId) {
                        $q->where('instructor_id', $instructorId);
                    })
                    ->orderBy('created_at', 'desc');
                }
            ])
            ->withCount([
                'orders as total_orders' => function ($query) use ($instructorId) {
                    $query->whereHas('items.course', function ($q) use ($instructorId) {
                        $q->where('instructor_id', $instructorId);
                    });
                }
            ])
            ->paginate($perPage)
            ->through(function ($user) use ($instructorId) {
                return $this->formatStudentData($user, $instructorId);
            });
    }

    protected function formatStudentData(User $user, int $instructorId): array
    {
        $totalPaid = $this->getTotalPaidAmount($user->id, $instructorId);
        $lastOrder = $this->getLastOrder($user->id, $instructorId);

        return [
            'id' => $user->id,
            'full_name' => $user->first_name . ' ' . $user->last_name,
            'type' => $user->role,
            'joined_at' => $user->created_at->format('M d, Y'),
            'total_paid_amount' => $totalPaid,
            'last_order' => $lastOrder ? [
                'id' => $lastOrder->id,
                'date' => $lastOrder->created_at->format('M d, Y'),
                'amount' => $lastOrder->total,
                'status' => $lastOrder->payment_status
            ] : null
        ];
    }

    protected function getTotalPaidAmount(int $userId, int $instructorId): float
    {
        return Order::where('user_id', $userId)
            ->whereHas('items.course', function ($query) use ($instructorId) {
                $query->where('instructor_id', $instructorId);
            })
            ->where('payment_status', 'paid')
            ->sum('total');
    }

    protected function getLastOrder(int $userId, int $instructorId): ?Order
    {
        return Order::where('user_id', $userId)
            ->whereHas('items.course', function ($query) use ($instructorId) {
                $query->where('instructor_id', $instructorId);
            })
            ->orderBy('created_at', 'desc')
            ->first();
    }
}
