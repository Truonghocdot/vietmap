<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Services\Orders\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function active(): JsonResponse
    {
        $coupons = Coupon::query()
            ->active()
            ->orderBy('code')
            ->get([
                'id',
                'code',
                'description',
                'discount_type',
                'discount_value',
                'min_order_amount',
                'max_discount_amount',
                'max_uses',
                'used_count',
                'starts_at',
                'ends_at',
            ]);

        return response()->json([
            'coupons' => $coupons,
        ]);
    }

    public function validateCoupon(Request $request, OrderService $orders): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string'],
            'price' => ['required', 'integer', 'min:0'],
        ]);

        $coupon = $orders->findApplicableCoupon(
            $validated['code'],
            (int) $validated['price']
        );

        if (! $coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Mã giảm giá không hợp lệ hoặc đã hết hiệu lực.',
            ]);
        }

        return response()->json([
            'success' => true,
            'coupon' => $coupon,
            'discount_amount' => $coupon->calculateDiscount((int) $validated['price']),
        ]);
    }
}
