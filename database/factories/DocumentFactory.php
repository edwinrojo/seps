<?php

namespace Database\Factories;

use App\Enums\ProcType;
use App\Models\Document;
use App\Models\DocumentType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Document>
 */
class DocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $procurementType = fake()->randomElement(ProcType::cases());

        return [
            'document_type_id' => DocumentType::query()->create([
                'title' => fake()->unique()->words(3, true),
                'description' => fake()->optional()->sentence(),
            ])->id,
            'title' => fake()->unique()->sentence(3),
            'description' => fake()->optional()->sentence(),
            'procurement_type' => [$procurementType->value],
            'is_required' => fake()->boolean(),
        ];
    }
}
