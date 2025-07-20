<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\CouponRequest;
use App\Http\Requests\Dashboard\UpdateCouponRequest;
use App\Services\CouponService;
use App\Traits\ApiResponse;

class CouponController extends Controller
{
    use ApiResponse;

    protected $couponService;

    public function __construct(CouponService $couponService)
    {
        $this->couponService = $couponService;
    }

    public function index(CouponRequest $request)
    {
        $result = $this->couponService->getPaginatedCouponsWithStats($request->all());

        return $this->success([
            'coupons' => $result['coupons'],
            'stats' => $result['stats']
        ], 'Coupons retrieved successfully');
    }

    public function store(CouponRequest $request)
    {
        $coupon = $this->couponService->createCoupon($request->validated());
        return $this->success($coupon, 'Coupon created successfully', 201);
    }

    public function show($id)
    {
        $coupon = $this->couponService->findCoupon($id);
        return $this->success($coupon, 'Coupon retrieved successfully');
    }

    public function update(UpdateCouponRequest $request, $id)
    {
        $coupon = $this->couponService->updateCoupon($id, $request->validated());
        return $this->success($coupon, 'Coupon updated successfully');
    }

    public function destroy($id)
    {
        $this->couponService->deleteCoupon($id);
        return $this->success(null, 'Coupon deleted successfully');
    }
}