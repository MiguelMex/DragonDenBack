<?php

namespace Database\Seeders;

use App\Models\profile;
use App\Models\User;
use DB;
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
        $email = env('ADMIN_EMAIL', 'admin@gmail.com');
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

            // Create a default profile for the new user
            $profile = profile::create([
                'user_id'     => $user->user_id,
                'name'        => 'Usuario',
                'nickname'    => 'Apodo',
                'description' => 'Nuevo Usuario',
                'avatar'      => 'avatars/default.jpeg',
            ]);

            DB::commit();
            $this->command->info("Profile user created with email: {$email}");
            
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
