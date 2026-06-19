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
        Schema::create('organization_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('org_id')->index()->constrained('organizations')->cascadeOnDelete();
            $table->text('name')->nullable();
            $table->integer('rating')->nullable();
            $table->text('date')->nullable();
            $table->text('review')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_reviews');
    }
};
