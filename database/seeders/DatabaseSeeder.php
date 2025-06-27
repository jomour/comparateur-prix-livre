<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        User::create([
            'name' => 'Jona Mourier',
            'email' => 'jona.mourier@hotmail.fr',
            'password' => bcrypt('JonaMourier2025!'),
        ]);

        User::create([
            'name' => 'Jonas Kinkai',
            'email' => 'jonas@kinkai.fr',
            'password' => bcrypt('JonasKinkai202!'),
        ]);

        User::create([
            'name' => 'Gabriel Kinkai',
            'email' => 'gabriel@kinkai.fr',
            'password' => bcrypt('GabrielKinkai2025!'),
        ]);
    }
}
