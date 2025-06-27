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
        Schema::create('historique_search', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('isbn', 13)->index();
            $table->string('prix_fnac')->nullable();
            $table->string('prix_amazon')->nullable();
            $table->string('prix_cultura')->nullable();
            $table->timestamps();
            
            // Index pour optimiser les recherches
            $table->index(['isbn', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historique_search');
    }
};
