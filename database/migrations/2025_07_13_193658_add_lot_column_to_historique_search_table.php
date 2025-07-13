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
            $table->unsignedBigInteger('lot')->nullable();
            $table->foreign('lot')->references('id')->on('historique_search_lot')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('historique_search', function (Blueprint $table) {
            $table->dropForeign(['lot']);
            $table->dropColumn('lot');
        });
    }
};
