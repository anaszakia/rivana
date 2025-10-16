<?php

namespace App\Models;

use App\Models\User;
use App\Models\HidrologiLog;
use App\Models\HidrologiFile;
use Illuminate\Database\Eloquent\Model;

class HidrologiJobs extends Model
{
    protected $table = 'hidrologi_jobs';

    protected $fillable = [
        'job_id',
        'user_id',
        'longitude',
        'latitude',
        'start_date',
        'end_date',
        'location_name',
        'location_description',
        'status',
        'progress',
        'status_message',
        'png_count',
        'csv_count',
        'json_count',
        'total_files',
        'result_path',
        'api_response',
        'error_message',
        'warning_message',
        'error_log_path',
        'submitted_at',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Relasi ke model User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke model HidrologiFile
     */
    public function files()
    {
        return $this->hasMany(HidrologiFile::class, 'job_id');
    }

    /**
     * Relasi ke model HidrologiLog
     */
    public function logs()
    {
        return $this->hasMany(HidrologiLog::class, 'job_id');
    }
}
