<?php

namespace Database\Seeders;

use App\Models\ValidationPurpose;
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

        ValidationPurpose::create([
            'purpose' => 'Document Review',
            'description' => 'Verification through review of submitted documents.',
            'is_iv' => true,
        ]);

        ValidationPurpose::create([
            'purpose' => 'Availability of Stock',
            'description' => 'Verification of the supplier’s ability to provide the required stock.',
            'is_iv' => true,
        ]);
    }
}
