<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => env('FILAMENT_ADMIN_EMAIL', 'admin@vietmap.local')],
            [
                'name' => env('FILAMENT_ADMIN_NAME', 'Administrator'),
                'password' => Hash::make(env('FILAMENT_ADMIN_PASSWORD', 'Admin12345')),
                'email_verified_at' => now(),
                'is_admin' => true,
            ],
        );
    }
}
