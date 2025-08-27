<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = storage_path('app/geo/provinces.json');
        $provinces = json_decode(file_get_contents($json), true);

        foreach ($provinces as $province) {
            $existing = \App\Models\Province::where('psgc_code', $province['code'])->first();
            $data = [
                'name' => $province['name'],
                'region_code' => $province['regionCode'] ?? null,
                'island_group_code' => $province['islandGroupCode'] ?? null,
                'psgc_10_digit_code' => $province['psgc10DigitCode'] ?? null,
            ];
            if ($existing) {
                $existing->update($data);
            } else {
                \App\Models\Province::create(array_merge(['psgc_code' => $province['code']], $data));
            }
        }
    }
}
