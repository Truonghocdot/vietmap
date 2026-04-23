<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    public const PAYMENT_PENDING = 'pending';
    public const PAYMENT_PAID = 'paid';
    public const PAYMENT_EXPIRED = 'expired';
    public const PAYMENT_FAILED = 'failed';

    public const FULFILLMENT_PENDING = 'pending';
    public const FULFILLMENT_FULFILLED = 'fulfilled';
    public const FULFILLMENT_FAILED = 'failed';

    protected $fillable = [
        'order_number',
        'package_id',
        'coupon_id',
        'software_key_id',
        'customer_email',
        'customer_ip',
        'original_amount',
        'discount_amount',
        'amount',
        'currency',
        'coupon_code',
        'payment_provider',
        'payment_gateway',
        'provider_transaction_id',
        'payment_status',
        'fulfillment_status',
        'paid_at',
        'fulfilled_at',
        'expires_at',
        'notes',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'paid_at' => 'datetime',
            'fulfilled_at' => 'datetime',
            'expires_at' => 'datetime',
            'meta' => 'array',
        ];
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('payment_status', self::PAYMENT_PENDING);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function softwareKey(): BelongsTo
    {
        return $this->belongsTo(SoftwareKey::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at?->isPast() ?? false;
    }

    public function canFulfill(): bool
    {
        return $this->payment_status === self::PAYMENT_PAID
            && $this->fulfillment_status === self::FULFILLMENT_PENDING;
    }
}
