<?php

namespace Database\Seeders;

use App\Models\User;
use Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Str;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@example.com');
        $password = env('ADMIN_PASSWORD', 'password');

        // Check if user already exists
        $user = User::where('email', $email)->first();
        if (!$user) {
            User::create([
                'user_id'   => Str::uuid()->toString(),
                'email'     => $email,
                'password'  => Hash::make($password),
                'admin'     => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->command->info("Admin user created with email: {$email}");
        } else {
            // Optionally update existing user's admin status
            if (!$user->admin) {
                $user->admin = true;
                $user->save();
                $this->command->info("Existing user {$email} promoted to admin.");
            } else {
                $this->command->info("Admin user already exists.");
            }
        }
    }
}
