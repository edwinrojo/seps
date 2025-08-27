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
        Schema::create('municipalities', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('province_id')->constrained('provinces')->cascadeOnDelete();
            $table->string('municipality_code')->unique();
            $table->string('name');
            $table->string('old_name')->nullable();
            $table->boolean('is_capital')->default(false);
            $table->boolean('is_city')->default(false);
            $table->boolean('is_municipality')->default(true);
            $table->string('province_code')->nullable();
            $table->string('district_code')->nullable();
            $table->string('region_code')->nullable();
            $table->string('island_group_code')->nullable();
            $table->string('psgc_10_digit_code')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('municipalities');
    }
};
