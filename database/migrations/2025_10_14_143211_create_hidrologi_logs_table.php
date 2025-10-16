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
       Schema::create('hidrologi_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_id'); // Relasi ke hidrologi_jobs
            $table->string('job_uuid'); // UUID job dari API
            
            // Log Information
            $table->enum('log_level', [
                'info',
                'warning',
                'error',
                'debug',
                'success'
            ])->default('info');
            
            $table->string('event_type')->nullable(); // submit, processing, completed, failed, etc
            $table->text('message'); // Pesan log
            $table->text('details')->nullable(); // Detail tambahan (JSON)
            
            // Context
            $table->integer('progress_at_event')->nullable(); // Progress saat event terjadi
            $table->string('status_at_event')->nullable(); // Status saat event terjadi
            
            $table->timestamps();
            
            // Indexes
            $table->index('job_id');
            $table->index('job_uuid');
            $table->index('log_level');
            $table->index('event_type');
            $table->index('created_at');
            
            // Foreign Key
            $table->foreign('job_id')
                  ->references('id')
                  ->on('hidrologi_jobs')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hidrologi_logs');
    }
};
