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
         Schema::create('hidrologi_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_id'); // Relasi ke hidrologi_jobs
            $table->string('job_uuid'); // UUID job dari API
            
            // Informasi File
            $table->string('filename'); // Nama file (WEAP_Dashboard.png)
            $table->enum('file_type', ['png', 'csv', 'json', 'log', 'other']); // Tipe file
            $table->string('mime_type')->nullable(); // image/png, text/csv, etc
            $table->bigInteger('file_size')->default(0); // Ukuran file dalam bytes
            $table->decimal('file_size_mb', 10, 2)->default(0); // Ukuran dalam MB
            
            // Path & URL
            $table->string('file_path')->nullable(); // Path lengkap di server Python
            $table->string('download_url')->nullable(); // URL untuk download
            $table->string('preview_url')->nullable(); // URL untuk preview (khusus gambar)
            
            // Metadata
            $table->string('display_name')->nullable(); // Nama untuk ditampilkan ke user
            $table->text('description')->nullable(); // Deskripsi file
            $table->integer('display_order')->default(0); // Urutan tampilan
            
            // Status
            $table->boolean('is_available')->default(true); // Apakah file masih tersedia
            $table->integer('download_count')->default(0); // Jumlah download
            $table->timestamp('last_downloaded_at')->nullable(); // Terakhir di-download
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('job_id');
            $table->index('job_uuid');
            $table->index('file_type');
            $table->index(['job_id', 'file_type']);
            
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
        Schema::dropIfExists('hidrologi_files');
    }
};
