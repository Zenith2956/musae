<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        DB::table('role')->insert([
            ['id' => 0, 'name' => 'élève', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 1, 'name' => 'prof', 'created_at' => now(), 'updated_at' => now()],
        ]);

        User::factory()->create([
            'name' => 'Test User',
            'nickname' => 'testuser',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'), 
            'role_id' => 0,
        ]);

    }
}
