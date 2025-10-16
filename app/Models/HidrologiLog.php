<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HidrologiLog extends Model
{
    protected $table = 'hidrologi_logs';

    protected $fillable = [
        'job_id',
        'job_uuid',
        'log_level',
        'event_type',
        'message',
        'details',
        'progress_at_event',
        'status_at_event',
    ];

    protected $casts = [
        'progress_at_event' => 'integer',
        'details' => 'array', // Auto cast JSON to array
    ];

    /**
     * Relasi ke model HidrologiJobs
     */
    public function job()
    {
        return $this->belongsTo(HidrologiJobs::class, 'job_id');
    }

    /**
     * Scope untuk filter by log level
     */
    public function scopeLevel($query, $level)
    {
        return $query->where('log_level', $level);
    }

    /**
     * Scope untuk filter by event type
     */
    public function scopeEventType($query, $eventType)
    {
        return $query->where('event_type', $eventType);
    }

    /**
     * Scope untuk errors only
     */
    public function scopeErrors($query)
    {
        return $query->where('log_level', 'error');
    }

    /**
     * Scope untuk warnings only
     */
    public function scopeWarnings($query)
    {
        return $query->where('log_level', 'warning');
    }

    /**
     * Static method untuk create log dengan shorthand
     */
    public static function logInfo($jobId, $jobUuid, $message, $eventType = null, $details = null)
    {
        return self::create([
            'job_id' => $jobId,
            'job_uuid' => $jobUuid,
            'log_level' => 'info',
            'event_type' => $eventType,
            'message' => $message,
            'details' => $details,
        ]);
    }

    public static function logError($jobId, $jobUuid, $message, $eventType = null, $details = null)
    {
        return self::create([
            'job_id' => $jobId,
            'job_uuid' => $jobUuid,
            'log_level' => 'error',
            'event_type' => $eventType,
            'message' => $message,
            'details' => $details,
        ]);
    }

    public static function logWarning($jobId, $jobUuid, $message, $eventType = null, $details = null)
    {
        return self::create([
            'job_id' => $jobId,
            'job_uuid' => $jobUuid,
            'log_level' => 'warning',
            'event_type' => $eventType,
            'message' => $message,
            'details' => $details,
        ]);
    }

    public static function logSuccess($jobId, $jobUuid, $message, $eventType = null, $details = null)
    {
        return self::create([
            'job_id' => $jobId,
            'job_uuid' => $jobUuid,
            'log_level' => 'success',
            'event_type' => $eventType,
            'message' => $message,
            'details' => $details,
        ]);
    }
}
