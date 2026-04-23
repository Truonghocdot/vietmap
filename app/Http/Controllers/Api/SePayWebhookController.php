<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Orders\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SePayWebhookController extends Controller
{
    public function __invoke(Request $request, OrderService $orders): JsonResponse
    {
        $webhook = $orders->processWebhook($request);

        if (! $webhook->is_valid) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized webhook.',
            ], 401);
        }

        return response()->json([
            'success' => true,
            'matched_order_number' => $webhook->matched_order_number,
        ]);
    }
}
