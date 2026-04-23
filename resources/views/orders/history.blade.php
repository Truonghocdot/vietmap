@extends('layouts.storefront')

@section('title', 'Lịch sử đơn hàng 30 ngày')

@section('content')
    <div class="card">
        <div class="card-body">
            <span class="eyebrow">Lịch sử IP</span>
            <h1 class="page-title">Lịch sử đơn hàng 30 ngày gần nhất</h1>
            <p class="page-subtitle">
                Trang này mô phỏng lại tính năng của site hiện tại: xem nhanh các đơn được tạo từ IP hiện tại để khách tra cứu lại đơn và mở lại màn hình thanh toán khi cần.
            </p>

            <div class="actions" style="margin: 24px 0;">
                <a class="button ghost" href="{{ route('storefront.home') }}">Về trang chủ</a>
                <form action="{{ route('orders.search') }}" method="GET" style="display: flex; gap: 12px; flex: 1; flex-wrap: wrap;">
                    <input class="input" type="text" name="code" placeholder="Nhập mã đơn hàng để tra cứu" style="flex: 1; min-width: 240px;">
                    <button class="button primary" type="submit">Tìm đơn</button>
                </form>
            </div>

            @if ($orders->isEmpty())
                <div class="alert warning">
                    Chưa có đơn nào được ghi nhận trong 30 ngày qua từ IP này.
                </div>
            @else
                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>Mã đơn</th>
                                <th>Gói</th>
                                <th>Trạng thái</th>
                                <th>Số tiền</th>
                                <th>Thời gian</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $historyOrder)
                                <tr>
                                    <td><strong>{{ $historyOrder->order_number }}</strong></td>
                                    <td>{{ $historyOrder->package?->display_name }}</td>
                                    <td>
                                        <span class="status-pill {{ $historyOrder->payment_status }}">
                                            {{ strtoupper($historyOrder->payment_status) }}
                                        </span>
                                    </td>
                                    <td>{{ number_format($historyOrder->amount, 0, ',', '.') }}đ</td>
                                    <td>{{ $historyOrder->created_at->format('H:i d/m/Y') }}</td>
                                    <td>
                                        <a href="{{ route('orders.show', $historyOrder->order_number) }}" style="color: var(--accent); font-weight: 700;">
                                            Xem chi tiết
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
