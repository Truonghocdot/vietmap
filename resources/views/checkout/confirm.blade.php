@extends('layouts.storefront')

@section('title', 'Xác nhận đơn hàng')

@section('content')
    <div class="grid two">
        <section class="card">
            <div class="card-body">
                <span class="eyebrow">Bước 1</span>
                <h1 class="page-title">Xác nhận gói và tạo đơn hàng</h1>
                <p class="page-subtitle">
                    Khách chọn gói, quét thanh toán SePay, sau đó hệ thống sẽ tự nhả thông tin tài khoản/key đã mua.
                    Nếu khách nhập Gmail, hệ thống sẽ gửi lại thông tin này qua email ngay sau khi webhook xác nhận thanh toán.
                </p>

                @if ($errors->has('checkout'))
                    <div class="alert error" style="margin-top: 20px;">
                        {{ $errors->first('checkout') }}
                    </div>
                @endif

                <div class="summary">
                    <div class="summary-row">
                        <span class="label">Gói</span>
                        <span class="value">{{ $package->display_name }}</span>
                    </div>
                    <div class="summary-row">
                        <span class="label">Thời hạn</span>
                        <span class="value">{{ $package->duration_label ?: $package->duration_hours . ' giờ' }}</span>
                    </div>
                    <div class="summary-row">
                        <span class="label">Giá gốc</span>
                        <span class="value">{{ number_format($amounts['original_amount'], 0, ',', '.') }}đ</span>
                    </div>
                    @if ($coupon)
                        <div class="summary-row">
                            <span class="label">Coupon</span>
                            <span class="value">{{ $coupon->code }}</span>
                        </div>
                        <div class="summary-row">
                            <span class="label">Giảm giá</span>
                            <span class="value">-{{ number_format($amounts['discount_amount'], 0, ',', '.') }}đ</span>
                        </div>
                    @endif
                    <div class="summary-row total">
                        <span class="label">Tổng thanh toán</span>
                        <span class="value price">{{ number_format($amounts['amount'], 0, ',', '.') }}đ</span>
                    </div>
                </div>

                <form class="stack" style="margin-top: 24px;" method="POST" action="{{ route('checkout.store') }}">
                    @csrf
                    <input type="hidden" name="package_id" value="{{ $package->id }}">
                    <input type="hidden" name="coupon_code" value="{{ $coupon?->code }}">

                    <div class="field">
                        <label for="customer_email">Gửi thông tin mua hàng qua Gmail</label>
                        <input
                            id="customer_email"
                            class="input"
                            type="email"
                            name="customer_email"
                            value="{{ old('customer_email', $prefilledEmail) }}"
                            placeholder="example@gmail.com"
                        >
                        <small>Tùy chọn. Nếu để trống, khách vẫn xem được tài khoản/key trực tiếp trên trang đơn hàng sau khi thanh toán.</small>
                        @error('customer_email')
                            <small style="color: var(--danger);">{{ $message }}</small>
                        @enderror
                    </div>

                    @if ($coupon)
                        <div class="alert success">
                            Mã <strong>{{ $coupon->code }}</strong> đã được áp dụng trước khi tạo đơn hàng.
                        </div>
                    @endif

                    <div class="actions">
                        <a class="button ghost" href="{{ route('storefront.home') }}">Quay lại chọn gói</a>
                        <button class="button primary" type="submit">Xác nhận và tạo đơn SePay</button>
                    </div>
                </form>
            </div>
        </section>

        <aside class="stack">
            <div class="meta-card">
                <h3>Luồng tự động</h3>
                <div class="meta-list">
                    <div class="meta-item">
                        <strong>1. Tạo đơn</strong>
                        <span>Reserve sẵn 1 key thuộc đúng gói để tránh oversell.</span>
                    </div>
                    <div class="meta-item">
                        <strong>2. Quét QR</strong>
                        <span>Khách chuyển đúng số tiền và đúng nội dung là mã đơn.</span>
                    </div>
                    <div class="meta-item">
                        <strong>3. Webhook SePay</strong>
                        <span>Hệ thống đối soát tự động và đổi trạng thái sang đã thanh toán.</span>
                    </div>
                    <div class="meta-item">
                        <strong>4. Tự giao key</strong>
                        <span>Nhả tài khoản/key ra màn hình đơn hàng và gửi mail nếu có Gmail.</span>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="status-pill pending">Đang chờ tạo đơn</div>
                    <p class="page-subtitle" style="margin-top: 14px;">
                        Mỗi đơn sẽ có mã riêng để khách dùng làm nội dung chuyển khoản. Đây là phần quan trọng nhất để SePay khớp giao dịch đúng đơn hàng.
                    </p>
                </div>
            </div>
        </aside>
    </div>
@endsection
