<?php

namespace Database\Seeders;

use App\Models\AdminUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $username = trim((string) env('ADMIN_USERNAME', ''));
        $password = (string) env('ADMIN_PASSWORD', '');

        if ($username === '' || $password === '') {
            $this->command?->warn('ADMIN_USERNAME/ADMIN_PASSWORD not set; skipping initial admin creation.');
            return;
        }

        $admin = AdminUser::firstOrNew(['username' => $username]);

        if (! $admin->exists) {
            $admin->password = Hash::make($password);
            $admin->role = 'super_admin';
            $admin->is_active = true;
            $admin->save();
            $this->command?->info("Initial admin '{$username}' created.");
        } else {
            $this->command?->info("Admin '{$username}' already exists; leaving credentials unchanged.");
        }
    }
}
