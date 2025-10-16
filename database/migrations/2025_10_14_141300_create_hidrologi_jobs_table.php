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
        Schema::create('hidrologi_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('job_id')->unique()->index(); // UUID dari API Python
            $table->unsignedBigInteger('user_id')->nullable(); // ID user yang submit
            
            // Parameter Input
            $table->decimal('longitude', 10, 6); // -180.000000 sampai 180.000000
            $table->decimal('latitude', 10, 6);  // -90.000000 sampai 90.000000
            $table->date('start_date'); // Tanggal mulai analisis
            $table->date('end_date');   // Tanggal akhir analisis
            
            // Informasi Lokasi (Optional)
            $table->string('location_name')->nullable(); // Nama lokasi (input dari user)
            $table->text('location_description')->nullable(); // Deskripsi lokasi
            
            // Status & Progress
            $table->enum('status', [
                'pending',      // Baru dibuat, belum di-submit ke API
                'submitted',    // Sudah di-submit ke API, menunggu response
                'processing',   // Sedang diproses oleh Python
                'completed',    // Berhasil selesai
                'completed_with_warning', // Selesai tapi ada warning
                'failed',       // Gagal
                'cancelled'     // Dibatalkan oleh user
            ])->default('pending');
            
            $table->integer('progress')->default(0); // Progress 0-100
            $table->text('status_message')->nullable(); // Pesan status saat ini
            
            // Informasi Hasil
            $table->integer('png_count')->default(0); // Jumlah file PNG
            $table->integer('csv_count')->default(0); // Jumlah file CSV
            $table->integer('json_count')->default(0); // Jumlah file JSON
            $table->integer('total_files')->default(0); // Total semua file
            
            // Path & URL
            $table->string('result_path')->nullable(); // Path folder hasil
            $table->text('api_response')->nullable(); // Response lengkap dari API (JSON)
            
            // Error Handling
            $table->text('error_message')->nullable(); // Pesan error
            $table->text('warning_message')->nullable(); // Pesan warning
            $table->text('error_log_path')->nullable(); // Path ke error.log
            
            // Timestamps
            $table->timestamp('submitted_at')->nullable(); // Kapan di-submit
            $table->timestamp('started_at')->nullable();   // Kapan mulai diproses
            $table->timestamp('completed_at')->nullable(); // Kapan selesai
            $table->timestamps(); // created_at, updated_at
            $table->softDeletes(); // deleted_at
            
            // Indexes untuk query cepat
            $table->index('user_id');
            $table->index('status');
            $table->index('created_at');
            $table->index(['user_id', 'status']);
            $table->index('job_id');
            
            // Foreign Key (uncomment jika ada users table)
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hidrologi_jobs');
    }
};
