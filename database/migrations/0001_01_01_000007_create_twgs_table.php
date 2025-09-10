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
        Schema::create('twgs', function (Blueprint $table) {
            $table->foreignUlid('user_id')->primary()->references('id')->on('users')->cascadeOnDelete();
            $table->foreignUlid('office_id')->constrained('offices')->cascadeOnDelete();
            $table->string('position_title', 500);
            $table->string('twg_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('twgs');
    }
};
