<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Student\CheckoutRequest;
use App\Services\OrderService;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    use ApiResponse;

    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function store(CheckoutRequest $request)
    {
        $order = $this->orderService->processCheckout(Auth::id(), $request->validated());

        return $this->success([
            'order' => $order,
            'items' => $order->items,
        ], 'Checkout successful');
    }
}
