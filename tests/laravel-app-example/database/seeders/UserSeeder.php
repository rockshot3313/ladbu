<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'role' => 'admin',
            ],
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'role' => 'user',
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'role' => 'user',
            ],
        ]);
    }
}
