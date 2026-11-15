<?php

namespace Database\Seeders;

use App\Http\Controllers\Auth\RegisterController;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Creates the administrator account (replaces the old "last name = trueman"
 * back door, which granted admin access to anyone who registered with it).
 *
 * Set ADMIN_EMAIL and ADMIN_PASSWORD in .env to choose the credentials. If
 * ADMIN_PASSWORD is not set, a random password is generated and printed once.
 */
class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL') ?: 'admin@benzipaid.test';

        if (User::where('email', $email)->exists()) {
            $this->command?->info("Admin account {$email} already exists; leaving it unchanged.");

            return;
        }

        $password = env('ADMIN_PASSWORD') ?: Str::password(16, symbols: false);

        $admin = new User();
        $admin->forceFill([
            'first_name' => 'Site',
            'last_name' => 'Administrator',
            'email' => $email,
            'password' => Hash::make($password),
            'user_type' => '1',
            'is_activated' => '1',
            'user_status' => '1',
            'email_verified_at' => now(),
            'currency' => '$',
            'a_number' => RegisterController::generateAccountNumber(),
        ])->save();

        $this->command?->info("Admin account created: {$email}");

        if (! env('ADMIN_PASSWORD')) {
            $this->command?->warn("Generated admin password (shown once, change it after logging in): {$password}");
        }
    }
}
