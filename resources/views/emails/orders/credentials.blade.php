<x-mail::message>
# Don hang {{ $order->order_number }} da duoc giao

Cam on ban da thanh toan goi **{{ $order->package?->display_name }}** tai {{ config('app.name') }}.

<x-mail::panel>
Ma don: {{ $order->order_number }}

Tong thanh toan: {{ number_format($order->amount, 0, ',', '.') }} VND

Trang thai thanh toan: {{ strtoupper($order->payment_status) }}
</x-mail::panel>

@if ($order->softwareKey?->label)
Ten tai khoan: **{{ $order->softwareKey->label }}**
@endif

@if ($order->softwareKey?->username)
Username / Email: **{{ $order->softwareKey->username }}**
@endif

@if ($order->softwareKey?->password)
Mat khau: **{{ $order->softwareKey->password }}**
@endif

@if ($order->softwareKey?->license_key)
License key: **{{ $order->softwareKey->license_key }}**
@endif

@if ($order->softwareKey?->notes)
Ghi chu:

{{ $order->softwareKey->notes }}
@endif

<x-mail::button :url="route('orders.show', $order->order_number)">
Mo lai don hang
</x-mail::button>

Neu ban can kiem tra lai lich su, hay vao trang don hang va nhap ma don ben tren.

Tron bo luong thanh toan da duoc doi soat tu dong boi SePay webhook.

Cam on,<br>
{{ config('app.name') }}
</x-mail::message>
