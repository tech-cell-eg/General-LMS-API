<?php

namespace App\Services;

use App\Models\Coupon;
use Illuminate\Support\Facades\DB;

class CouponService
{
    public function getPaginatedCouponsWithStats(array $filters)
    {
        $totalCoupons = Coupon::count();
        $totalRedeemed = Coupon::sum('redemptions');
        $redeemedAmount = Coupon::sum(DB::raw('amount * redemptions'));

        $coupons = Coupon::filter($filters)
            ->orderBy('priority', 'desc')
            ->paginate($filters['per_page'] ?? 15);

        return [
            'coupons' => $coupons,
            'stats' => [
                'total_coupons' => $totalCoupons,
                'total_redeemed' => $totalRedeemed,
                'redeemed_amount' => $redeemedAmount
            ]
        ];
    }

    public function createCoupon(array $data)
    {
        return Coupon::create($data);
    }

    public function findCoupon($id)
    {
        return Coupon::findOrFail($id);
    }

    public function updateCoupon($id, array $data)
    {
        $coupon = $this->findCoupon($id);
        $coupon->update($data);
        return $coupon;
    }

    public function deleteCoupon($id)
    {
        $coupon = $this->findCoupon($id);
        $coupon->delete();
    }
}
