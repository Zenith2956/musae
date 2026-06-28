<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\GenericInstrument;
use App\Models\Training;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Role::firstOrCreate(['name' => 'élève']);
        Role::firstOrCreate(['name' => 'prof']);

        $instruments = [
            'Guitare','Piano','Batterie','Violon','Saxophone','Flûte','Contrebasse',
            'Clarinette','Trompette','Harpe','Accordéon','Harmonica','Tambourin',
            'Orgue','Trombone','Cor d’harmonie','Bongo','Maracas','Ukulélé',
            'Mandoline','Xylophone','Glockenspiel','Didgeridoo','Vibraphone','Balafon'
        ];

        foreach ($instruments as $index => $name) {
            GenericInstrument::firstOrCreate([
                'id' => $index + 1,
                'name' => $name
            ]);
        }

        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'nickname' => 'testuser',
                'password' => bcrypt('password123'),
                'role_id' => 1,
            ]
        );

        $user = User::firstOrCreate(
            ['email' => 'erwann@example.com'],
            [
                'name' => 'Erwann',
                'nickname' => 'erwann',
                'password' => bcrypt('superprof'),
                'role_id' => 1,
            ]
        );

        $user = User::firstOrCreate(
            ['email' => 'arthur@example.com'],
            [
                'name' => 'Arthur',
                'nickname' => 'arthur',
                'password' => bcrypt('password123'),
                'role_id' => 1,
            ]
        );

        DB::table('training_medias')->insertOrIgnore([
            ['id' => 1, 'link' => 'https://example.com']
        ]);

        Training::create([
            'name' => 'test1',
            'date_training' => '2026-03-15 14:00:00',
            'duration' => 60,
            'training_media_id' => null,
            'user_id' => $user->id,
            'sheet_id' => null
        ]);

        Training::create([
            'name' => 'Didjeridoo',
            'date_training' => '2026-03-16 16:00:00',
            'duration' => 60,
            'training_media_id' => null,
            'user_id' => $user->id,
            'sheet_id' => null
        ]);
    }
}
