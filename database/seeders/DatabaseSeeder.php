<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $admin = User::create([
            'id' => (string) Str::ulid(),
            'first_name' => 'Edwin',
            'last_name' => 'Rojo',
            'middle_name' => 'Razonable',
            'suffix' => 'Jr.',
            'role' => 'administrator',
            'email' => 'admin@local.dev',
            'contact_number' => '0946-565-2778',
            'password' => bcrypt('1234'),
        ]);
    }
}
