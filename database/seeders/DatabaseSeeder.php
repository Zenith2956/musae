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

       Role::firstOrCreate(['id' => 1], ['name' => 'élève']);
       Role::firstOrCreate(['id' => 2], ['name' => 'prof']);
       
        DB::table('roles')->insert([
            ['id' => 1, 'name' => 'élève', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'prof', 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('generic_instruments')->insert([
            ['id' => 1, 'name' => 'Guitare'],
            ['id' => 2, 'name' => 'Piano'],
            ['id' => 3, 'name' => 'Batterie'],
            ['id' => 4, 'name' => 'Violon'],
            ['id' => 5, 'name' => 'Saxophone'],
            ['id' => 6, 'name' => 'Flûte'],
            ['id' => 7, 'name' => 'Contrebasse'],
            ['id' => 8, 'name' => 'Clarinette'],
            ['id' => 9, 'name' => 'Trompette'],
            ['id' => 10, 'name' => 'Harpe'],
            ['id' => 11, 'name' => 'Accordéon'],
            ['id' => 12, 'name' => 'Harmonica'],
            ['id' => 13, 'name' => 'Tambourin'],
            ['id' => 14, 'name' => 'Orgue'],
            ['id' => 15, 'name' => 'Trombone'],
            ['id' => 16, 'name' => 'Cor d’harmonie'],
            ['id' => 17, 'name' => 'Bongo'],
            ['id' => 18, 'name' => 'Maracas'],
            ['id' => 19, 'name' => 'Ukulélé'],
            ['id' => 20, 'name' => 'Mandoline'],
            ['id' => 21, 'name' => 'Xylophone'],
            ['id' => 22, 'name' => 'Glockenspiel'],
            ['id' => 23, 'name' => 'Didgeridoo'],
            ['id' => 24, 'name' => 'Vibraphone'],
            ['id' => 25, 'name' => 'Balafon'],
        ]);

        User::factory()->create([
            'name' => 'Test User',
            'nickname' => 'testuser',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'), 
            'role_id' => 1,
        ]);
        
        DB::table('training_medias')->insert([
            ['id' => 1, 'link' => 'https://example.com']
        ]);

        DB::table('trainings')->insert([
            ['id' => 1, 'name' => 'test1', 'date_training' => '2026-03-14 10:45:00', 'duration' => 60, 'training_media_id' => null, 'user_id' => 1, 'sheet_id' => null],
            ['id' => 2, 'name' => 'Didjeridoo', 'date_training' => '2026-03-16 15:00:00', 'duration' => 60, 'training_media_id' => null, 'user_id' => 1, 'sheet_id' => null],
        ]);
    }
}
