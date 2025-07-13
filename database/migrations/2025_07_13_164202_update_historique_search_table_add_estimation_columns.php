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
        Schema::table('historique_search', function (Blueprint $table) {
            // Renommer la colonne existante
            $table->renameColumn('estimation_occasion', 'estimation_occasion_correct');
            
            // Ajouter les nouvelles colonnes
            $table->decimal('estimation_occasion_bon', 8, 2)->nullable()->after('estimation_occasion_correct');
            $table->decimal('estimation_occasion_excellent', 8, 2)->nullable()->after('estimation_occasion_bon');
            $table->text('rarete')->nullable()->after('estimation_occasion_excellent');
            $table->integer('score_rarete')->nullable()->after('rarete');
            $table->decimal('anilist_popularite', 8, 2)->nullable()->after('score_rarete');
            $table->decimal('anilist_note', 8, 2)->nullable()->after('anilist_popularite');
            $table->string('anilist_statut')->nullable()->after('anilist_note');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('historique_search', function (Blueprint $table) {
            // Supprimer les nouvelles colonnes
            $table->dropColumn([
                'estimation_occasion_bon',
                'estimation_occasion_excellent',
                'rarete',
                'score_rarete',
                'anilist_popularite',
                'anilist_note',
                'anilist_statut'
            ]);
            
            // Renommer la colonne en arrière
            $table->renameColumn('estimation_occasion_correct', 'estimation_occasion');
        });
    }
};
