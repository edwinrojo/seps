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
        Schema::create('supplier_lobs', function (Blueprint $table) {
            $table->foreignUlid('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            $table->foreignUlid('lob_category_id')->constrained('lob_categories')->cascadeOnDelete();
            $table->foreignUlid('lob_subcategory_id')->nullable()->constrained('lob_subcategories')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['supplier_id', 'lob_category_id', 'lob_subcategory_id'], 'supplier_lob_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_lobs');
    }
};
