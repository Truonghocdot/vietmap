<?php

use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\OrderStatusController;
use App\Http\Controllers\Api\SePayWebhookController;
use Illuminate\Support\Facades\Route;

Route::get('/coupons/active', [CouponController::class, 'active']);
Route::get('/coupons/validate', [CouponController::class, 'validateCoupon']);
Route::get('/orders/{order:order_number}/status', [OrderStatusController::class, 'show']);
Route::post('/webhooks/sepay', SePayWebhookController::class);
