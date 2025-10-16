<?php

namespace App\Models;

use App\Models\HidrologiJobs;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HidrologiFile extends Model
{
    use SoftDeletes;

    protected $table = 'hidrologi_files';

    protected $fillable = [
        'job_id',
        'job_uuid',
        'filename',
        'file_type',
        'mime_type',
        'file_size',
        'file_size_mb',
        'file_path',
        'download_url',
        'preview_url',
        'display_name',
        'description',
        'display_order',
        'is_available',
        'download_count',
        'last_downloaded_at',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'file_size_mb' => 'decimal:2',
        'display_order' => 'integer',
        'is_available' => 'boolean',
        'download_count' => 'integer',
        'last_downloaded_at' => 'datetime',
    ];

    /**
     * Relasi ke model HidrologiJobs
     */
    public function job()
    {
        return $this->belongsTo(HidrologiJobs::class, 'job_id');
    }

    /**
     * Increment download count
     */
    public function incrementDownloadCount()
    {
        $this->increment('download_count');
        $this->update(['last_downloaded_at' => now()]);
    }

    /**
     * Check if file is image
     */
    public function isImage()
    {
        return $this->file_type === 'png' || str_starts_with($this->mime_type ?? '', 'image/');
    }

    /**
     * Scope untuk filter by file type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('file_type', $type);
    }

    /**
     * Scope untuk file yang tersedia
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }
}
