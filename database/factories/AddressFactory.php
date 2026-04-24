<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\Barangay;
use App\Models\Municipality;
use App\Models\Province;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Address>
 */
class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $province = Province::query()->create([
            'psgc_code' => fake()->unique()->numerify('##########'),
            'name' => fake()->state(),
            'region_code' => fake()->optional()->numerify('##'),
            'island_group_code' => fake()->optional()->numerify('##'),
            'psgc_10_digit_code' => fake()->optional()->numerify('##########'),
        ]);

        $municipality = Municipality::query()->create([
            'province_id' => $province->id,
            'municipality_code' => fake()->unique()->numerify('##########'),
            'name' => fake()->city(),
            'old_name' => fake()->optional()->city(),
            'is_capital' => fake()->boolean(),
            'is_city' => fake()->boolean(),
            'is_municipality' => true,
            'province_code' => fake()->optional()->numerify('##'),
            'district_code' => fake()->optional()->numerify('##'),
            'region_code' => fake()->optional()->numerify('##'),
            'island_group_code' => fake()->optional()->numerify('##'),
            'psgc_10_digit_code' => fake()->optional()->numerify('##########'),
        ]);

        $barangay = Barangay::query()->create([
            'municipality_id' => $municipality->id,
            'barangay_code' => fake()->unique()->numerify('##########'),
            'name' => fake()->word(),
            'oldName' => fake()->optional()->word(),
            'subMunicipalityCode' => fake()->optional()->numerify('##'),
            'cityCode' => fake()->optional()->numerify('##'),
            'municipalityCode' => fake()->optional()->numerify('##'),
            'districtCode' => false,
            'provinceCode' => fake()->optional()->numerify('##'),
            'regionCode' => fake()->optional()->numerify('##'),
            'islandGroupCode' => fake()->optional()->numerify('##'),
            'psgc10DigitCode' => fake()->optional()->numerify('##########'),
        ]);

        return [
            'supplier_id' => Supplier::factory(),
            'label' => fake()->randomElement(['Office', 'Warehouse', 'Branch']),
            'line_1' => fake()->streetAddress(),
            'line_2' => fake()->optional()->secondaryAddress(),
            'municipality_id' => $municipality->id,
            'barangay_id' => $barangay->id,
            'province_id' => $province->id,
            'country' => 'Philippines',
            'zip_code' => fake()->numerify('####'),
        ];
    }
}
