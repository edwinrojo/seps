<?php

namespace Database\Seeders;

use App\Models\Office;
use App\Models\Twg;
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
            'first_name' => 'John',
            'last_name' => 'Doe',
            'middle_name' => 'Dela Cruz',
            'suffix' => 'III',
            'role' => 'administrator',
            'email' => 'admin@local.dev',
            'contact_number' => '946-565-2778',
            'password' => bcrypt('1234'),
        ]);

        $office = Office::create([
            'id' => (string) Str::ulid(),
            'title' => 'Provincial Information and Communications Technology Office',
            'acronym' => 'PICTO',
        ]);

        $office2 = Office::create([
            'id' => (string) Str::ulid(),
            'title' => 'Provincial General Services Office',
            'acronym' => 'PGSO',
        ]);

        $twg_user = User::create([
            'id' => (string) Str::ulid(),
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'middle_name' => 'Ann',
            'suffix' => 'Jr.',
            'role' => 'twg',
            'email' => 'twg@local.dev',
            'contact_number' => '946-565-2778',
            'password' => bcrypt('1234'),
        ]);

        $twg_user->twg()->create([
            'office_id' => $office->id,
            'position_title' => 'Information Technology Officer II',
            'twg_type' => 'goods'
        ]);

        $end_user = User::create([
            'id' => (string) Str::ulid(),
            'first_name' => 'Emily',
            'last_name' => 'Johnson',
            'middle_name' => 'Marie',
            'suffix' => '',
            'role' => 'end-user',
            'email' => 'enduser@local.dev',
            'contact_number' => '946-565-2778',
            'password' => bcrypt('1234'),
        ]);

        $end_user->endUser()->create([
            'office_id' => $office->id,
            'designation' => 'Administrative Officer II',
        ]);

        $supplier_user = User::create([
            'id' => (string) Str::ulid(),
            'first_name' => 'Joyce',
            'last_name' => 'Rojo',
            'middle_name' => 'Razonable',
            'suffix' => '',
            'role' => 'supplier',
            'email' => 'joyce@local.dev',
            'contact_number' => '946-565-2778',
            'password' => bcrypt('1234'),
        ]);
    }
}
