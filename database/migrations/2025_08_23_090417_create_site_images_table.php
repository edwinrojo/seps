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
        Schema::create('site_images', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulidMorphs('site_imageable');
            $table->text('file_path');
            $table->unsignedBigInteger('file_size');
            $table->geography('location', subtype: 'point', srid: 4326);
            $table->timestamp('captured_at', precision: 0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_images');
    }
};
