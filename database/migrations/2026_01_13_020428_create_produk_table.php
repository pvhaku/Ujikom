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
        Schema::create('produks', function (Blueprint $table) {
            $table->id();
            $table->string(column: 'name');
            $table->string(column: 'slug');
            $table->string(column: 'thumbnail');
            $table->text(column: 'about');
            $table->unsignedBigInteger(column: 'price');
            $table->unsignedBigInteger(column: 'stock');
            $table->boolean(column: 'is_popular');
            $table->foreignId(column: 'category_id')->constrained()->cascadeOnDelete();
            $table->foreignId(column: 'brand_id')->constrained()->cascadeOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produks');
    }
};
