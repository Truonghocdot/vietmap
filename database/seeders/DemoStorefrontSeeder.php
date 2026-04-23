<?php

namespace Database\Seeders;

use App\Models\Coupon;
use App\Models\Package;
use App\Models\SoftwareKey;
use Illuminate\Database\Seeder;

class DemoStorefrontSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            ['slug' => 'vietmap-3-gio', 'name' => 'VIETMAP LIVE PRO 3 gio', 'duration_label' => '3 gio', 'duration_hours' => 3, 'price' => 10000, 'compare_at_price' => 19000, 'badge' => 'goi-trai-nghiem'],
            ['slug' => 'vietmap-1-ngay', 'name' => 'VIETMAP LIVE PRO 1 ngay', 'duration_label' => '1 ngay', 'duration_hours' => 24, 'price' => 19000, 'compare_at_price' => 30000, 'badge' => 'mua-nhieu-nhat'],
            ['slug' => 'vietmap-2-ngay', 'name' => 'VIETMAP LIVE PRO 2 ngay', 'duration_label' => '2 ngay', 'duration_hours' => 48, 'price' => 32000, 'compare_at_price' => 40000, 'badge' => 'promo'],
            ['slug' => 'vietmap-3-ngay', 'name' => 'VIETMAP LIVE PRO 3 ngay', 'duration_label' => '3 ngay', 'duration_hours' => 72, 'price' => 42000, 'compare_at_price' => 50000, 'badge' => 'dac-biet'],
            ['slug' => 'vietmap-7-ngay', 'name' => 'VIETMAP LIVE PRO 7 ngay', 'duration_label' => '7 ngay', 'duration_hours' => 168, 'price' => 69000, 'compare_at_price' => 100000, 'badge' => 'hot'],
            ['slug' => 'vietmap-10-ngay', 'name' => 'VIETMAP LIVE PRO 10 ngay', 'duration_label' => '10 ngay', 'duration_hours' => 240, 'price' => 75000, 'compare_at_price' => 150000, 'badge' => 'pho-bien'],
            ['slug' => 'vietmap-20-ngay', 'name' => 'VIETMAP LIVE PRO 20 ngay', 'duration_label' => '20 ngay', 'duration_hours' => 480, 'price' => 139000, 'compare_at_price' => 250000, 'badge' => 'tiet-kiem'],
            ['slug' => 'vietmap-30-ngay', 'name' => 'VIETMAP LIVE PRO 30 ngay', 'duration_label' => '30 ngay', 'duration_hours' => 720, 'price' => 189000, 'compare_at_price' => 320000, 'badge' => 'best-value'],
            ['slug' => 'vietmap-90-ngay', 'name' => 'VIETMAP LIVE PRO 90 ngay', 'duration_label' => '90 ngay', 'duration_hours' => 2160, 'price' => 300000, 'compare_at_price' => 450000, 'badge' => 'vip'],
        ];

        foreach ($packages as $index => $payload) {
            $package = Package::query()->updateOrCreate(
                ['slug' => $payload['slug']],
                [
                    'service_code' => 'vietmap',
                    'name' => $payload['name'],
                    'short_name' => $payload['name'],
                    'description' => 'Goi Vietmap duoc clone tu site hien tai va cap tai khoan tu dong qua SePay webhook.',
                    'duration_label' => $payload['duration_label'],
                    'duration_hours' => $payload['duration_hours'],
                    'price' => $payload['price'],
                    'compare_at_price' => $payload['compare_at_price'],
                    'badge' => $payload['badge'],
                    'badge_color' => 'emerald',
                    'features' => [
                        'Canh bao camera',
                        'Giao thong realtime',
                        'Nhan tai khoan tu dong',
                    ],
                    'checkout_notes' => 'Khach quet SePay xong la tu nhan tai khoan ngay.',
                    'is_active' => true,
                    'sort_order' => $index + 1,
                ],
            );

            foreach (range(1, 3) as $counter) {
                SoftwareKey::query()->updateOrCreate(
                    ['reference' => strtoupper($package->slug . '-K' . $counter)],
                    [
                        'package_id' => $package->id,
                        'label' => 'Tai khoan ' . $package->duration_label . ' #' . $counter,
                        'username' => $package->slug . $counter . '@example.com',
                        'password' => 'VM' . strtoupper(substr($package->slug, 0, 4)) . $counter . '2026',
                        'license_key' => 'KEY-' . strtoupper(substr($package->slug, 0, 6)) . '-' . sprintf('%04d', $counter),
                        'notes' => 'Dang nhap trong app Vietmap Live Pro bang thong tin tren.',
                        'extra_data' => [
                            'support' => 'Zalo 0777333763',
                        ],
                        'status' => SoftwareKey::STATUS_AVAILABLE,
                        'is_active' => true,
                    ],
                );
            }
        }

        Coupon::query()->updateOrCreate(
            ['code' => 'VIETMAP10'],
            [
                'description' => 'Giam 10% cho don Vietmap',
                'discount_type' => Coupon::TYPE_PERCENT,
                'discount_value' => 10,
                'min_order_amount' => 19000,
                'max_discount_amount' => 30000,
                'max_uses' => 1000,
                'used_count' => 0,
                'is_active' => true,
            ],
        );

        Coupon::query()->updateOrCreate(
            ['code' => 'GIAM15000'],
            [
                'description' => 'Giam truc tiep 15k',
                'discount_type' => Coupon::TYPE_FIXED,
                'discount_value' => 15000,
                'min_order_amount' => 69000,
                'max_discount_amount' => null,
                'max_uses' => 100,
                'used_count' => 0,
                'is_active' => true,
            ],
        );
    }
}
