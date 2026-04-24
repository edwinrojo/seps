<?php

namespace Database\Factories;

use App\Models\Attachment;
use App\Models\Document;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Attachment>
 */
class AttachmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'supplier_id' => Supplier::factory(),
            'document_id' => Document::factory(),
            'file_path' => fake()->filePath(),
            'file_size' => fake()->numberBetween(1024, 1048576),
            'validity_date' => fake()->optional()->date(),
        ];
    }
}
