<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MunicipalitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = storage_path('app/geo/municipalities.json');
        $municipalities = json_decode(file_get_contents($json), true);

        foreach ($municipalities as $provincePsgc => $municipalityList) {
            $province = \App\Models\Province::where('psgc_code', $provincePsgc)->first();
            if (! $province) {
                continue;
            }
            foreach ($municipalityList as $municipality) {
                $existing = \App\Models\Municipality::where('municipality_code', $municipality['code'])->first();
                $data = [
                    'province_id' => $province->id,
                    'name' => $municipality['name'],
                    'old_name' => $municipality['oldName'] ?? null,
                    'is_capital' => $municipality['isCapital'] ?? false,
                    'is_city' => $municipality['isCity'] ?? false,
                    'is_municipality' => $municipality['isMunicipality'] ?? true,
                    'province_code' => $municipality['provinceCode'] ?? null,
                    'district_code' => $municipality['districtCode'] ?? null,
                    'region_code' => $municipality['regionCode'] ?? null,
                    'island_group_code' => $municipality['islandGroupCode'] ?? null,
                    'psgc_10_digit_code' => $municipality['psgc10DigitCode'] ?? null,
                ];
                if ($existing) {
                    $existing->update($data);
                } else {
                    \App\Models\Municipality::create(array_merge(['municipality_code' => $municipality['code']], $data));
                }
            }
        }
    }
}
