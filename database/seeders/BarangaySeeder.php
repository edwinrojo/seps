<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BarangaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = storage_path('app/geo/barangays.json');
        $barangays = json_decode(file_get_contents($json), true);

        foreach ($barangays as $municipalityPsgc => $barangayList) {
            $municipality = \App\Models\Municipality::where('municipality_code', $municipalityPsgc)->first();
            if (! $municipality || !is_array($barangayList)) {
                continue;
            }
            foreach ($barangayList as $barangay) {
                $existing = \App\Models\Barangay::where('barangay_code', $barangay['code'])->first();
                $data = [
                    'municipality_id' => $municipality->id,
                    'name' => $barangay['name'],
                    'oldName' => $barangay['oldName'] ?? null,
                    'subMunicipalityCode' => $barangay['subMunicipalityCode'] ?? null,
                    'cityCode' => $barangay['cityCode'] ?? null,
                    'municipalityCode' => $barangay['municipalityCode'] ?? null,
                    'districtCode' => $barangay['districtCode'] ?? null,
                    'provinceCode' => $barangay['provinceCode'] ?? null,
                    'regionCode' => $barangay['regionCode'] ?? null,
                    'islandGroupCode' => $barangay['islandGroupCode'] ?? null,
                    'psgc10DigitCode' => $barangay['psgc10DigitCode'] ?? null,
                ];
                if ($existing) {
                    $existing->update($data);
                } else {
                    \App\Models\Barangay::create(array_merge(['barangay_code' => $barangay['code']], $data));
                }
            }
        }
    }
}
