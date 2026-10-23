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
        Schema::table('hidrologi_jobs', function (Blueprint $table) {
            $table->longText('das_geojson')->nullable()->after('hybas_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hidrologi_jobs', function (Blueprint $table) {
            $table->dropColumn('das_geojson');
        });
    }
};
