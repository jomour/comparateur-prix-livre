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
            $table->text('sales_text')->nullable()->after('rarete');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('historique_search', function (Blueprint $table) {
            $table->dropColumn('sales_text');
        });
    }
};
