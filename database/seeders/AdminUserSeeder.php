<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@labelsalesagents.com',
            'password' => Hash::make('Thelabel99!'),
            'email_verified_at' => now(),
            'is_approved' => true,
            'approved_at' => now(),
        ]);

        // Assign admin role
        $admin->assignRole('admin');
    }
}
