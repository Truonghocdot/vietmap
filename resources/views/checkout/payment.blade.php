@extends('layouts.storefront')

@section('title', 'Thanh toán đơn hàng ' . $order->order_number)

@section('content')
    <div class="grid two">
        <section class="card">
            <div class="card-body">
                <span class="eyebrow">Bước 2</span>
                <h1 class="page-title">Thanh toán đơn {{ $order->order_number }}</h1>
                <p class="page-subtitle">
                    Quét mã QR SePay hoặc chuyển khoản thủ công. Đừng sửa nội dung chuyển khoản để webhook tự nhận đúng đơn.
                </p>

                <div style="margin: 20px 0 0;">
                    <span class="status-pill {{ $order->payment_status }}">
                        {{ strtoupper($order->payment_status) }}
                    </span>
                    <span class="status-pill {{ $order->fulfillment_status }}" style="margin-left: 8px;">
                        {{ strtoupper($order->fulfillment_status) }}
                    </span>
                </div>

                @if ($order->payment_status === \App\Models\Order::PAYMENT_EXPIRED)
                    <div class="alert error" style="margin-top: 20px;">
                        Đơn này đã hết hạn thanh toán. Key đã được trả lại kho để tránh giữ stock quá lâu.
                    </div>
                @elseif ($order->fulfillment_status === \App\Models\Order::FULFILLMENT_FULFILLED && $order->softwareKey)
                    <div class="alert success" style="margin-top: 20px;">
                        Thanh toán đã được xác nhận. Thông tin tài khoản/key ở ngay bên dưới.
                    </div>
                @else
                    <div class="alert warning" style="margin-top: 20px;">
                        Đơn sẽ giữ key đến <strong>{{ optional($order->expires_at)->format('H:i d/m/Y') }}</strong>.
                    </div>
                @endif

                <div class="summary">
                    <div class="summary-row">
                        <span class="label">Gói</span>
                        <span class="value">{{ $order->package?->display_name }}</span>
                    </div>
                    <div class="summary-row">
                        <span class="label">Tổng tiền</span>
                        <span class="value price">{{ number_format($order->amount, 0, ',', '.') }}đ</span>
                    </div>
                    <div class="summary-row">
                        <span class="label">Nội dung chuyển khoản</span>
                        <span class="value" id="transfer-content">{{ $order->order_number }}</span>
                    </div>
                    <div class="summary-row">
                        <span class="label">Gmail nhận tự động</span>
                        <span class="value">{{ $order->customer_email ?: 'Không nhập' }}</span>
                    </div>
                </div>

                @if ($order->payment_status !== \App\Models\Order::PAYMENT_EXPIRED && $order->fulfillment_status !== \App\Models\Order::FULFILLMENT_FULFILLED)
                    <div class="actions" style="margin-top: 24px;">
                        <button class="button secondary" type="button" data-copy="{{ $order->order_number }}">Copy nội dung CK</button>
                        <button class="button ghost" type="button" data-copy="{{ $accountNumber }}">Copy STK</button>
                    </div>
                @endif

                @if ($order->softwareKey && $order->fulfillment_status === \App\Models\Order::FULFILLMENT_FULFILLED)
                    <div class="stack" style="margin-top: 26px;">
                        <div class="summary-row">
                            <span class="label">Tên tài khoản</span>
                            <span class="value">{{ $order->softwareKey->label ?: 'Tài khoản đã cấp' }}</span>
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
                        @if ($order->softwareKey->notes)
                            <div class="alert success">
                                {{ $order->softwareKey->notes }}
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </section>

        <aside class="stack">
            <div class="card">
                <div class="card-body" style="text-align: center;">
                    <img
                        src="{{ $qrUrl }}"
                        alt="QR SePay"
                        style="width: min(100%, 280px); border-radius: 20px; border: 1px solid #dbe3ef; background: #fff;"
                    >
                    <p class="muted" style="margin: 14px 0 0;">QR SePay đã chứa sẵn số tiền và nội dung chuyển khoản.</p>
                </div>
            </div>

            <div class="meta-card">
                <h3>Thông tin nhận tiền</h3>
                <div class="meta-list">
                    <div class="meta-item">
                        <strong>Ngân hàng</strong>
                        <span>{{ $bankCode ?: 'Chưa cấu hình' }}</span>
                    </div>
                    <div class="meta-item">
                        <strong>Số tài khoản</strong>
                        <span>{{ $accountNumber ?: 'Chưa cấu hình' }}</span>
                    </div>
                    <div class="meta-item">
                        <strong>Chủ tài khoản</strong>
                        <span>{{ $accountName ?: 'Chưa cấu hình' }}</span>
                    </div>
                    <div class="meta-item">
                        <strong>Nội dung bắt buộc</strong>
                        <span>{{ $order->order_number }}</span>
                    </div>
                </div>
            </div>
        </aside>
    </div>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('[data-copy]').forEach((button) => {
            button.addEventListener('click', async () => {
                try {
                    await navigator.clipboard.writeText(button.dataset.copy || '');
                    button.textContent = 'Da copy';
                    setTimeout(() => {
                        button.textContent = button.dataset.copy === '{{ $order->order_number }}'
                            ? 'Copy noi dung CK'
                            : 'Copy STK';
                    }, 1200);
                } catch (error) {
                    window.alert('Khong copy duoc, vui long copy thu cong.');
                }
            });
        });

        @if ($order->payment_status === \App\Models\Order::PAYMENT_PENDING)
            const statusUrl = @json(url('/api/orders/' . $order->order_number . '/status'));

            setInterval(async () => {
                try {
                    const response = await fetch(statusUrl, { headers: { 'Accept': 'application/json' } });
                    const data = await response.json();

                    if (!data.success) {
                        return;
                    }

                    if (['paid', 'expired'].includes(data.order.payment_status) || data.order.fulfillment_status === 'fulfilled') {
                        window.location.reload();
                    }
                } catch (error) {
                    console.debug(error);
                }
            }, 5000);
        @endif
    </script>
@endpush
