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
            $table->string('das_name')->nullable()->after('location_description');
            $table->decimal('das_area_km2', 12, 2)->nullable()->after('das_name');
            $table->unsignedTinyInteger('das_level')->nullable()->after('das_area_km2');
            $table->string('hybas_id', 50)->nullable()->after('das_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hidrologi_jobs', function (Blueprint $table) {
            $table->dropColumn(['das_name', 'das_area_km2', 'das_level', 'hybas_id']);
        });
    }
};
