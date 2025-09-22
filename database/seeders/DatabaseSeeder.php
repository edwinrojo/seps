<?php

namespace Database\Seeders;

use App\Models\LobCategory;
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
            'middle_name' => 'Gonida',
            'suffix' => '',
            'role' => 'supplier',
            'email' => 'joyce@local.dev',
            'contact_number' => '946-565-2778',
            'password' => bcrypt('1234'),
        ]);

        $supplier_user->supplier()->create([
            'business_name' => 'Wincy Pharmacy Incorporated',
            'owner_name' => 'Joyce Gonida Rojo',
            'email' => 'contact@wincypharmacy.com',
            'website' => 'https://www.wincypharmacy.com',
            'mobile_number' => '946-565-2778',
            'landline_number' => '(082) 123-4567',
            'supplier_type' => 'goods',
        ]);

        $supplier_user2 = User::create([
            'id' => (string) Str::ulid(),
            'first_name' => 'Michael',
            'last_name' => 'Tan',
            'middle_name' => 'Lee',
            'suffix' => '',
            'role' => 'supplier',
            'email' => 'michael@local.dev',
            'contact_number' => '946-565-2778',
            'password' => bcrypt('1234'),
        ]);

        $supplier_user3 = User::create([
            'id' => (string) Str::ulid(),
            'first_name' => 'Sarah',
            'last_name' => 'Lim',
            'middle_name' => 'Chen',
            'suffix' => '',
            'role' => 'supplier',
            'email' => 'sarah@local.dev',
            'contact_number' => '946-565-2778',
            'password' => bcrypt('1234'),
        ]);

        $supplier_user4 = User::create([
            'id' => (string) Str::ulid(),
            'first_name' => 'David',
            'last_name' => 'Garcia',
            'middle_name' => 'Martinez',
            'suffix' => '',
            'role' => 'supplier',
            'email' => 'david@local.dev',
            'contact_number' => '946-565-2778',
            'password' => bcrypt('1234'),
        ]);

        $lob_category1 = LobCategory::create([
            'title' => 'Information Technology',
            'description' => 'Category for IT-related products and services.',
        ]);

        $lob_category2 = LobCategory::create([
            'title' => 'Office Supplies',
            'description' => 'Category for general office supplies and equipment.',
        ]);

        $lob_category3 = LobCategory::create([
            'title' => 'Construction Materials',
            'description' => 'Category for materials used in construction projects.',
        ]);

        $lob_category1->lobSubcategories()->createMany([
            ['title' => 'ICT Equipment', 'description' => 'Desktops, laptops, and accessories.'],
            ['title' => 'Networking Equipment', 'description' => 'Routers, switches, and other networking devices.'],
            ['title' => 'Software', 'description' => 'Operating systems, productivity software, and specialized applications.'],
        ]);

        $lob_category2->lobSubcategories()->createMany([
            ['title' => 'Paper', 'description' => 'Various types of paper for printing and writing.'],
            ['title' => 'Office Furniture', 'description' => 'Desks, chairs, and other office furniture.'],
            ['title' => 'Stationery', 'description' => 'Pens, pencils, and other stationery items.'],
        ]);

        $this->call(DocumentSeeder::class);
        $this->call(ProvinceSeeder::class);
        $this->call(MunicipalitySeeder::class);
        $this->call(BarangaySeeder::class);
    }
}
