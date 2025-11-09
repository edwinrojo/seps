<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            $table->foreignUlid('province_id')->constrained('provinces')->cascadeOnDelete();
            $table->foreignUlid('municipality_id')->constrained('municipalities')->cascadeOnDelete();
            $table->foreignUlid('barangay_id')->constrained('barangays')->cascadeOnDelete();
            $table->string('label', 255);
            $table->string('line_1', 2048);
            $table->string('line_2', 2048)->nullable();
            $table->string('country', 255);
            $table->string('zip_code', 20);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
