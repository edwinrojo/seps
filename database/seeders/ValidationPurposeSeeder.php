<?php

namespace Database\Seeders;

use App\Models\ValidationPurpose;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ValidationPurposeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ValidationPurpose::create([
            'purpose' => 'Physical Office Visit',
            'description' => 'Verification through an on-site visit to the office location.',
            'is_iv' => true,
        ]);
    }
}
