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
        Schema::create('site_validations', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            $table->foreignUlid('address_id')->constrained('addresses')->cascadeOnDelete();
            $table->foreignUlid('twg_id')->references('user_id')->on('twgs')->cascadeOnDelete();
            $table->foreignUlid('validation_purpose_id')->constrained('validation_purposes')->cascadeOnDelete();
            $table->dateTime('validation_date');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_validations');
    }
};
