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
        Schema::create('barangays', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('municipality_id')->constrained('municipalities')->cascadeOnDelete();
            $table->string('barangay_code')->unique();
            $table->string('name');
            $table->string('oldName')->nullable();
            $table->string('subMunicipalityCode')->nullable();
            $table->string('cityCode')->nullable();
            $table->string('municipalityCode')->nullable();
            $table->boolean('districtCode')->default(false);
            $table->string('provinceCode')->nullable();
            $table->string('regionCode')->nullable();
            $table->string('islandGroupCode')->nullable();
            $table->string('psgc10DigitCode')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barangays');
    }
};
