<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Orders\OrderService;
use Illuminate\Http\JsonResponse;

class OrderStatusController extends Controller
{
    public function show(Order $order, OrderService $orders): JsonResponse
    {
        $order = $orders->expirePendingOrderIfNeeded($order)->loadMissing(['package', 'softwareKey']);

        return response()->json([
            'success' => true,
            'order' => [
                'order_number' => $order->order_number,
                'payment_status' => $order->payment_status,
                'fulfillment_status' => $order->fulfillment_status,
                'amount' => $order->amount,
                'paid_at' => $order->paid_at?->toIso8601String(),
                'fulfilled_at' => $order->fulfilled_at?->toIso8601String(),
                'expires_at' => $order->expires_at?->toIso8601String(),
                'package' => [
                    'name' => $order->package?->name,
                    'duration_label' => $order->package?->duration_label,
                ],
                'payment' => [
                    'qr_url' => $orders->buildSePayQrUrl($order),
                    'bank_code' => config('services.sepay.bank_code'),
                    'account_name' => config('services.sepay.account_name'),
                    'account_number' => config('services.sepay.account_number'),
                    'transfer_content' => $order->order_number,
                ],
                'credentials' => $order->fulfillment_status === Order::FULFILLMENT_FULFILLED && $order->softwareKey ? [
                    'label' => $order->softwareKey->label,
                    'username' => $order->softwareKey->username,
                    'password' => $order->softwareKey->password,
                    'license_key' => $order->softwareKey->license_key,
                    'notes' => $order->softwareKey->notes,
                    'extra_data' => $order->softwareKey->extra_data,
                ] : null,
            ],
        ]);
    }
}
