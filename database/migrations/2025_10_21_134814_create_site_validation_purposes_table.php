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
        Schema::create('site_validation_purposes', function (Blueprint $table) {
            $table->foreignUlid('site_validation_id')->constrained('site_validations')->cascadeOnDelete();
            $table->foreignUlid('validation_purpose_id')->constrained('validation_purposes')->cascadeOnDelete();
            $table->timestamps();

            $table->primary(['site_validation_id', 'validation_purpose_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_validation_purposes');
    }
};
