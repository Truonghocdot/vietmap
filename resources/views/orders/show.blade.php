@extends('layouts.storefront')

@section('title', 'Đơn hàng ' . $order->order_number)

@section('content')
    <div class="grid two">
        <section class="card">
            <div class="card-body">
                <span class="eyebrow">Tra cứu đơn</span>
                <h1 class="page-title">{{ $order->order_number }}</h1>
                <p class="page-subtitle">
                    Trang này dùng để khách tra cứu lại đơn hàng sau khi đã thanh toán hoặc khi cần lấy lại thông tin tài khoản đã mua.
                </p>

                <div style="margin-top: 20px;">
                    <span class="status-pill {{ $order->payment_status }}">{{ strtoupper($order->payment_status) }}</span>
                    <span class="status-pill {{ $order->fulfillment_status }}" style="margin-left: 8px;">{{ strtoupper($order->fulfillment_status) }}</span>
                </div>

                <div class="summary">
                    <div class="summary-row">
                        <span class="label">Gói</span>
                        <span class="value">{{ $order->package?->display_name }}</span>
                    </div>
                    <div class="summary-row">
                        <span class="label">Giá thanh toán</span>
                        <span class="value">{{ number_format($order->amount, 0, ',', '.') }}đ</span>
                    </div>
                    <div class="summary-row">
                        <span class="label">Email nhận đơn</span>
                        <span class="value">{{ $order->customer_email ?: 'Không có' }}</span>
                    </div>
                </div>

                @if ($order->payment_status === \App\Models\Order::PAYMENT_PENDING)
                    <div class="alert warning" style="margin-top: 24px;">
                        Đơn này vẫn đang chờ thanh toán. Nếu khách đã chuyển khoản, vui lòng kiểm tra lại nội dung CK hoặc webhook SePay.
                    </div>
                    <div class="actions" style="margin-top: 20px;">
                        <a class="button primary" href="{{ route('checkout.show', $order->order_number) }}">Mở lại trang thanh toán</a>
                    </div>
                @elseif ($order->fulfillment_status === \App\Models\Order::FULFILLMENT_FULFILLED && $order->softwareKey)
                    <div class="alert success" style="margin-top: 24px;">
                        Tài khoản/key đã được cấp thành công.
                    </div>
                    <div class="summary" style="margin-top: 20px;">
                        <div class="summary-row">
                            <span class="label">Tài khoản</span>
                            <span class="value">{{ $order->softwareKey->label ?: 'Key đã cấp' }}</span>
                        </div>
                        @if ($order->softwareKey->username)
                            <div class="summary-row">
                                <span class="label">Username / Email</span>
                                <span class="value">{{ $order->softwareKey->username }}</span>
                            </div>
                        @endif
                        @if ($order->softwareKey->password)
                            <div class="summary-row">
                                <span class="label">Mật khẩu</span>
                                <span class="value">{{ $order->softwareKey->password }}</span>
                            </div>
                        @endif
                        @if ($order->softwareKey->license_key)
                            <div class="summary-row">
                                <span class="label">License key</span>
                                <span class="value">{{ $order->softwareKey->license_key }}</span>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="alert error" style="margin-top: 24px;">
                        Đơn đã được ghi nhận nhưng chưa thể giao key. Hãy kiểm tra log webhook hoặc stock key trong Filament.
                    </div>
                @endif
            </div>
        </section>

        <aside class="stack">
            <div class="card">
                <div class="card-body">
                    <form class="stack" action="{{ route('orders.search') }}" method="GET">
                        <div class="field">
                            <label for="code">Tìm mã đơn khác</label>
                            <input class="input" id="code" type="text" name="code" placeholder="VD: {{ $order->order_number }}">
                        </div>
                        <button class="button primary" type="submit">Tra cứu lại</button>
                    </form>
                </div>
            </div>
        </aside>
    </div>
@endsection
