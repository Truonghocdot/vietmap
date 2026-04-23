<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\Orders\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class CheckoutController extends Controller
{
    public function create(Request $request, OrderService $orders): View
    {
        $package = $orders->resolvePackageFromRequest($request);
        $coupon = $orders->findApplicableCoupon(
            $request->string('coupon')->toString(),
            $package->price
        );
        $amounts = $orders->buildAmountSummary($package, $coupon);

        return view('checkout.confirm', [
            'package' => $package,
            'coupon' => $coupon,
            'amounts' => $amounts,
            'prefilledEmail' => $request->string('email')->toString(),
        ]);
    }

    public function store(Request $request, OrderService $orders): RedirectResponse
    {
        $package = $orders->resolvePackageFromRequest($request);

        $validated = $request->validate([
            'customer_email' => ['nullable', 'email'],
            'coupon_code' => ['nullable', 'string', 'max:50'],
        ]);

        $coupon = $orders->findApplicableCoupon(
            $validated['coupon_code'] ?? null,
            $package->price
        );

        try {
            $order = $orders->createPendingOrder(
                $package,
                $validated['customer_email'] ?? null,
                $coupon,
                $request->ip()
            );
        } catch (RuntimeException $exception) {
            return back()
                ->withInput()
                ->withErrors(['checkout' => $exception->getMessage()]);
        }

        return redirect()->route('checkout.show', $order->order_number);
    }

    public function show(string $order, OrderService $orders): View
    {
        $order = Order::query()
            ->where('order_number', $order)
            ->firstOrFail();

        $order = $orders->expirePendingOrderIfNeeded($order);

        return view('checkout.payment', [
            'order' => $order->loadMissing(['package', 'softwareKey']),
            'qrUrl' => $orders->buildSePayQrUrl($order),
            'bankCode' => (string) config('services.sepay.bank_code'),
            'accountNumber' => (string) config('services.sepay.account_number'),
            'accountName' => (string) config('services.sepay.account_name'),
        ]);
    }
}
