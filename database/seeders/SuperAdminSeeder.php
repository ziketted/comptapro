<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\LicenseKey;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create SuperAdmin User
        $superAdmin = User::updateOrCreate(
            ['email' => 'admin@comptapro.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('admin123'),
                'role' => 'superadmin',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('SuperAdmin user created: admin@comptapro.com / admin123');

        // 2. Generate Initial License Keys (5)
        for ($i = 0; $i < 5; $i++) {
            LicenseKey::create([
                'key' => strtoupper(Str::random(4)) . '-' . strtoupper(Str::random(4)) . '-' . strtoupper(Str::random(4)) . '-' . strtoupper(Str::random(4)),
                'status' => 'UNUSED',
            ]);
        }

        $this->command->info('5 initial license keys generated.');
    }
}
