<?php
namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
class AdminUserSeeder extends Seeder {
    public function run(): void {
        $admin = User::firstOrCreate(['email' => 'admin@drnda.local'], ['name' => 'Administrator', 'password' => Hash::make('admin123'), 'email_verified_at' => now(), 'active' => true]);
        $admin->syncRoles(['administrator']);
        \$this->command->info('Admin: admin@drnda.local / admin123');
    }
}
