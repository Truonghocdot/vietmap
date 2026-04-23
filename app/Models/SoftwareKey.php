<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Hidden(['username', 'password', 'license_key'])]
class SoftwareKey extends Model
{
    public const STATUS_AVAILABLE = 'available';
    public const STATUS_RESERVED = 'reserved';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_DISABLED = 'disabled';

    protected $fillable = [
        'package_id',
        'order_id',
        'reference',
        'label',
        'username',
        'password',
        'license_key',
        'notes',
        'extra_data',
        'status',
        'reserved_at',
        'delivered_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'username' => 'encrypted',
            'password' => 'encrypted',
            'license_key' => 'encrypted',
            'extra_data' => 'array',
            'reserved_at' => 'datetime',
            'delivered_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query
            ->where('status', self::STATUS_AVAILABLE)
            ->where('is_active', true);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function getMaskedCredentialAttribute(): string
    {
        $parts = array_filter([
            $this->label,
            $this->username ? 'User: ' . $this->maskSecret($this->username) : null,
            $this->license_key ? 'Key: ' . $this->maskSecret($this->license_key, 4) : null,
        ]);

        return implode(' | ', $parts);
    }

    private function maskSecret(?string $value, int $visible = 3): ?string
    {
        if (blank($value)) {
            return null;
        }

        $length = mb_strlen($value);

        if ($length <= ($visible * 2)) {
            return str_repeat('*', $length);
        }

        return mb_substr($value, 0, $visible)
            . str_repeat('*', max(4, $length - ($visible * 2)))
            . mb_substr($value, -$visible);
    }
}
