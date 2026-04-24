<?php

namespace Database\Factories;

use App\Enums\ProcType;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Supplier>
 */
class SupplierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $supplierType = fake()->randomElement(ProcType::cases());

        return [
            'user_id' => null,
            'business_name' => fake()->unique()->company(),
            'website' => fake()->url(),
            'email' => fake()->unique()->safeEmail(),
            'mobile_number' => '9'.fake()->numerify('##-###-####'),
            'landline_number' => fake()->phoneNumber(),
            'owner_name' => fake()->name(),
            'supplier_type' => $supplierType->value,
        ];
    }
}
