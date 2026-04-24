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
        Schema::create('analytics_snapshots', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->date('snapshot_date')->index();
            $table->string('metric_key')->index();
            $table->decimal('metric_value', 12, 2);
            $table->json('dimensions')->nullable();
            $table->timestamps();

            $table->unique(['snapshot_date', 'metric_key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytics_snapshots');
    }
};
