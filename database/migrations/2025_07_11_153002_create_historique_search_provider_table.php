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
        Schema::create('historique_search_provider', function (Blueprint $table) {
            $table->id();
            $table->foreignId('historique_search_id')->constrained('historique_search')->onDelete('cascade');
            $table->string('name'); // amazon, fnac, cultura
            $table->decimal('min', 8, 2)->nullable(); // prix minimum
            $table->decimal('max', 8, 2)->nullable(); // prix maximum
            $table->decimal('amplitude', 8, 2)->nullable(); // amplitude des prix
            $table->decimal('average', 8, 2)->nullable(); // prix moyen
            $table->integer('nb_offre')->default(0); // nombre d'offres analysées
            $table->timestamps();
            
            // Index pour optimiser les requêtes
            $table->index(['historique_search_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historique_search_provider');
    }
};
