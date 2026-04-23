<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\Orders\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function search(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string'],
        ]);

        $order = Order::query()
            ->where('order_number', strtoupper(trim($validated['code'])))
            ->first();

        if (! $order) {
            return back()->withErrors([
                'code' => 'Không tìm thấy mã đơn hàng này.',
            ]);
        }

        return redirect()->route('orders.show', $order->order_number);
    }

    public function show(Order $order, OrderService $orders): View
    {
        $order = $orders->expirePendingOrderIfNeeded($order);

        return view('orders.show', [
            'order' => $order->loadMissing(['package', 'softwareKey']),
        ]);
    }

    public function history(Request $request): View
    {
        $orders = Order::query()
            ->with(['package'])
            ->where('customer_ip', $request->ip())
            ->where('created_at', '>=', now()->subDays(30))
            ->latest()
            ->get();

        return view('orders.history', [
            'orders' => $orders,
        ]);
    }
}
