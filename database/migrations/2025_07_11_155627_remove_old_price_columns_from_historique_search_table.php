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
            $table->dropColumn(['prix_amazon', 'prix_cultura', 'prix_fnac']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('historique_search', function (Blueprint $table) {
            $table->decimal('prix_amazon', 8, 2)->nullable();
            $table->decimal('prix_cultura', 8, 2)->nullable();
            $table->decimal('prix_fnac', 8, 2)->nullable();
        });
    }
};
