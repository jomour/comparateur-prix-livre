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
        Schema::create('historique_search_rarity_factor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('historique_search_id')->constrained('historique_search')->onDelete('cascade');
            $table->string('factor');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historique_search_rarity_factor');
    }
};
