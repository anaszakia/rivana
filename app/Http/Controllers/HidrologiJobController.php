<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\HidrologiApiService;
use App\Models\HidrologiJobs;
use App\Models\HidrologiFile;
use App\Models\HidrologiLog;

class HidrologiJobController extends Controller
{
    protected $apiService;

    public function __construct(HidrologiApiService $apiService)
    {
        $this->apiService = $apiService;
        // Uncomment jika menggunakan auth
        // $this->middleware('auth');
    }

    /**
     * Halaman utama - daftar job
     */
    public function index()
    {
        $jobs = HidrologiJobs::query()
            // ->where('user_id', Auth::id()) // Uncomment jika ada auth
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('hidrologi.index', compact('jobs'));
    }

    /**
     * Form untuk submit job baru
     */
    public function create()
    {
        return view('hidrologi.create');
    }

    /**
     * Submit job baru ke API
     */
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'longitude' => 'required|numeric|between:-180,180',
            'latitude' => 'required|numeric|between:-90,90',
            'start_date' => 'required|date|before:end_date',
            'end_date' => 'required|date|after:start_date|before_or_equal:today',
            'location_name' => 'nullable|string|max:255',
            'location_description' => 'nullable|string|max:1000'
        ]);

        DB::beginTransaction();
        try {
            // 1. Buat record di database dengan status pending
            $job = HidrologiJobs::create([
                'job_id' => 'pending', // Sementara, akan di-update setelah dapat dari API
                'user_id' => 1, // Default user ID since no authentication
                'longitude' => $validated['longitude'],
                'latitude' => $validated['latitude'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'location_name' => $validated['location_name'] ?? null,
                'location_description' => $validated['location_description'] ?? null,
                'status' => 'pending',
                'progress' => 0,
                'status_message' => 'Mengirim request ke API...'
            ]);

            // Log event
            HidrologiLog::create([
                'job_id' => $job->id,
                'job_uuid' => 'pending',
                'log_level' => 'info',
                'event_type' => 'created',
                'message' => 'Job created in database',
                'progress_at_event' => 0,
                'status_at_event' => 'pending'
            ]);

            // 2. Submit ke Python API
            $result = $this->apiService->submitJob(
                $validated['longitude'],
                $validated['latitude'],
                $validated['start_date'],
                $validated['end_date']
            );

            if ($result['success']) {
                // Update job dengan job_id dari API
                $apiData = $result['data'];
                $job->update([
                    'job_id' => $apiData['job_id'],
                    'status' => 'submitted',
                    'submitted_at' => now(),
                    'status_message' => 'Job berhasil di-submit ke API',
                    'api_response' => json_encode($apiData)
                ]);

                // Update log dengan job_uuid yang benar
                HidrologiLog::where('job_id', $job->id)->update([
                    'job_uuid' => $apiData['job_id']
                ]);

                // Log success
                HidrologiLog::create([
                    'job_id' => $job->id,
                    'job_uuid' => $apiData['job_id'],
                    'log_level' => 'success',
                    'event_type' => 'submitted',
                    'message' => 'Job successfully submitted to Python API',
                    'details' => json_encode($apiData),
                    'progress_at_event' => 0,
                    'status_at_event' => 'submitted'
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Job berhasil di-submit',
                    'job_id' => $job->id,
                    'job_uuid' => $apiData['job_id']
                ]);

            } else {
                // API error
                $errorMsg = $result['error'];
                if (isset($result['response'])) {
                    $errorMsg .= ' - Response: ' . $result['response'];
                }
                
                $job->update([
                    'status' => 'failed',
                    'error_message' => $errorMsg
                ]);

                HidrologiLog::create([
                    'job_id' => $job->id,
                    'job_uuid' => 'pending',
                    'log_level' => 'error',
                    'event_type' => 'submit_failed',
                    'message' => 'Failed to submit to Python API',
                    'details' => json_encode($result),
                    'progress_at_event' => 0,
                    'status_at_event' => 'failed'
                ]);

                DB::commit();

                return response()->json([
                    'success' => false,
                    'message' => 'Gagal submit ke API: ' . $errorMsg,
                    'debug' => $result
                ], 500);
            }

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error submitting hidrologi job: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cek status job (untuk AJAX polling)
     */
    public function checkStatus($id)
    {
        $job = HidrologiJobs::findOrFail($id);

        // Jika status masih processing atau submitted, cek ke API
        if (in_array($job->status, ['submitted', 'processing'])) {
            $result = $this->apiService->checkStatus($job->job_id);

            if ($result['success']) {
                $apiData = $result['data'];

                // Update job dengan data terbaru dari API
                $updateData = [
                    'progress' => $apiData['progress'] ?? $job->progress,
                    'status_message' => $this->getStatusMessage($apiData['progress'] ?? 0)
                ];

                // Update status jika berubah
                if (isset($apiData['status'])) {
                    $updateData['status'] = $apiData['status'];

                    if ($apiData['status'] === 'processing' && !$job->started_at) {
                        $updateData['started_at'] = now();
                    }

                    if (in_array($apiData['status'], ['completed', 'completed_with_warning'])) {
                        $updateData['completed_at'] = now();
                        $updateData['png_count'] = $apiData['files_generated']['png'] ?? 0;
                        $updateData['csv_count'] = $apiData['files_generated']['csv'] ?? 0;
                        $updateData['json_count'] = $apiData['files_generated']['json'] ?? 0;
                        $updateData['total_files'] = ($apiData['files_generated']['png'] ?? 0) +
                                                     ($apiData['files_generated']['csv'] ?? 0) +
                                                     ($apiData['files_generated']['json'] ?? 0);
                        $updateData['result_path'] = $apiData['result_path'] ?? null;
                    }

                    if ($apiData['status'] === 'failed') {
                        $updateData['error_message'] = $apiData['error'] ?? 'Unknown error';
                    }

                    if (isset($apiData['warning'])) {
                        $updateData['warning_message'] = $apiData['warning'];
                    }
                }

                $job->update($updateData);

                // Log status update
                HidrologiLog::create([
                    'job_id' => $job->id,
                    'job_uuid' => $job->job_id,
                    'log_level' => 'info',
                    'event_type' => 'status_update',
                    'message' => 'Status updated from API',
                    'details' => json_encode($apiData),
                    'progress_at_event' => $apiData['progress'] ?? 0,
                    'status_at_event' => $apiData['status'] ?? 'unknown'
                ]);

                // Jika completed, fetch files
                if (in_array($apiData['status'], ['completed', 'completed_with_warning'])) {
                    $this->fetchAndStoreFiles($job);
                }
            }
        }

        return response()->json([
            'success' => true,
            'job' => [
                'id' => $job->id,
                'job_id' => $job->job_id,
                'status' => $job->status,
                'progress' => $job->progress,
                'status_message' => $job->status_message,
                'png_count' => $job->png_count,
                'csv_count' => $job->csv_count,
                'json_count' => $job->json_count,
                'error_message' => $job->error_message,
                'warning_message' => $job->warning_message
            ]
        ]);
    }

    /**
     * Tampilkan hasil job
     */
    public function show($id)
    {
        $job = HidrologiJobs::with('files')->findOrFail($id);

        // Jika belum ada files di database tapi status completed, fetch dari API
        if (in_array($job->status, ['completed', 'completed_with_warning']) && $job->files->isEmpty()) {
            $this->fetchAndStoreFiles($job);
            $job->load('files');
        }

        // Ambil text summary dari API (prioritas: summary terstruktur, fallback ke logs lengkap)
        $summary = null;
        $fullLogs = null;
        if (in_array($job->status, ['completed', 'completed_with_warning'])) {
            // Coba ambil summary terstruktur dulu
            $summaryResult = $this->apiService->getSummary($job->job_id);
            if ($summaryResult['success']) {
                $summary = $summaryResult['data']['summary'] ?? null;
            }
            
            // Ambil juga full logs untuk detail lengkap
            $logsResult = $this->apiService->getLogs($job->job_id);
            if ($logsResult['success']) {
                $fullLogs = $logsResult['data']['log_content'] ?? null;
            }
        }

        return view('hidrologi.show', compact('job', 'summary', 'fullLogs'));
    }

    /**
     * Get summary sebagai JSON (untuk AJAX)
     */
    public function getSummary($id)
    {
        $job = HidrologiJobs::findOrFail($id);

        if (!in_array($job->status, ['completed', 'completed_with_warning'])) {
            return response()->json([
                'success' => false,
                'message' => 'Summary hanya tersedia untuk job yang sudah selesai'
            ], 400);
        }

        $result = $this->apiService->getSummary($job->job_id);

        if ($result['success']) {
            return response()->json($result['data']);
        }

        return response()->json([
            'success' => false,
            'message' => 'Gagal mengambil summary: ' . $result['error']
        ], 500);
    }

    /**
     * Get full logs lengkap sebagai JSON (untuk AJAX)
     */
    public function getLogs($id)
    {
        $job = HidrologiJobs::findOrFail($id);

        if (!in_array($job->status, ['completed', 'completed_with_warning', 'failed'])) {
            return response()->json([
                'success' => false,
                'message' => 'Logs hanya tersedia untuk job yang sudah selesai atau failed'
            ], 400);
        }

        $result = $this->apiService->getLogs($job->job_id);

        if ($result['success']) {
            return response()->json($result['data']);
        }

        return response()->json([
            'success' => false,
            'message' => 'Gagal mengambil logs: ' . $result['error']
        ], 500);
    }

    /**
     * Fetch dan simpan informasi files dari API ke database
     */
    protected function fetchAndStoreFiles(HidrologiJobs $job)
    {
        // Try getFiles first (supports all file types)
        $result = $this->apiService->getFiles($job->job_id);

        if ($result['success']) {
            $filesData = $result['data'];
            $apiBaseUrl = config('services.hidrologi.api_url');
            $filesStored = 0;

            // Handle 'files' key (all file types) or fallback to 'images' key (PNG only)
            $filesList = $filesData['files'] ?? $filesData['images'] ?? [];

            foreach ($filesList as $file) {
                // Detect file type from filename
                $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $fileType = strtolower($fileExtension);
                
                // Get MIME type
                $mimeType = $this->getMimeTypeFromExtension($fileType);
                
                HidrologiFile::updateOrCreate(
                    [
                        'job_id' => $job->id,
                        'filename' => $file['name']
                    ],
                    [
                        'job_uuid' => $job->job_id,
                        'file_type' => $fileType,
                        'mime_type' => $mimeType,
                        'file_size' => $file['size'] ?? 0,
                        'file_size_mb' => $file['size_mb'] ?? 0,
                        'download_url' => $apiBaseUrl . ($file['download_url'] ?? "/download/{$job->job_id}/{$file['name']}"),
                        'preview_url' => $apiBaseUrl . ($file['preview_url'] ?? "/preview/{$job->job_id}/{$file['name']}"),
                        'display_name' => $this->getDisplayName($file['name']),
                        'description' => $this->getFileDescription($file['name']),
                        'display_order' => $file['order'] ?? $filesStored,
                        'is_available' => true
                    ]
                );
                
                $filesStored++;
            }

            if ($filesStored > 0) {
                // Log
                HidrologiLog::create([
                    'job_id' => $job->id,
                    'job_uuid' => $job->job_id,
                    'log_level' => 'success',
                    'event_type' => 'files_stored',
                    'message' => $filesStored . ' files stored in database',
                    'progress_at_event' => 100,
                    'status_at_event' => $job->status
                ]);
            }
        }
    }
    
    /**
     * Get MIME type from file extension
     */
    protected function getMimeTypeFromExtension($extension)
    {
        $mimeTypes = [
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'csv' => 'text/csv',
            'json' => 'application/json',
            'txt' => 'text/plain',
            'pdf' => 'application/pdf',
            'html' => 'text/html',
            'htm' => 'text/html',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'xls' => 'application/vnd.ms-excel',
        ];
        
        return $mimeTypes[$extension] ?? 'application/octet-stream';
    }

    /**
     * Get status message berdasarkan progress
     */
    protected function getStatusMessage($progress)
    {
        if ($progress < 20) return 'Mempersiapkan data...';
        if ($progress < 40) return 'Mengambil data dari Google Earth Engine...';
        if ($progress < 60) return 'Menjalankan model Machine Learning...';
        if ($progress < 90) return 'Membuat visualisasi...';
        if ($progress < 100) return 'Menyelesaikan proses...';
        return 'Selesai!';
    }

    /**
     * Get display name untuk file
     */
    protected function getDisplayName($filename)
    {
        $names = [
            'WEAP_Dashboard.png' => 'Dashboard Utama WEAP',
            'WEAP_Enhanced_Dashboard.png' => 'Dashboard Lengkap',
            'WEAP_Water_Balance_Dashboard.png' => 'Dashboard Keseimbangan Air',
            'WEAP_Morphometry_Summary.png' => 'Ringkasan Morfometri',
            'WEAP_Morphology_Ecology_Dashboard.png' => 'Dashboard Morfologi & Ekologi'
        ];

        return $names[$filename] ?? $filename;
    }

    /**
     * Get description untuk file
     */
    protected function getFileDescription($filename)
    {
        $descriptions = [
            'WEAP_Dashboard.png' => 'Dashboard utama menampilkan status waduk, supply-demand, dan rekomendasi operasi',
            'WEAP_Enhanced_Dashboard.png' => 'Dashboard lengkap dengan analisis biaya-manfaat, kualitas air, dan efisiensi',
            'WEAP_Water_Balance_Dashboard.png' => 'Analisis keseimbangan air masuk dan keluar di wilayah',
            'WEAP_Morphometry_Summary.png' => 'Ringkasan parameter morfometri DAS',
            'WEAP_Morphology_Ecology_Dashboard.png' => 'Analisis kondisi sungai, sedimen, dan kesehatan ekosistem'
        ];

        return $descriptions[$filename] ?? null;
    }

    /**
     * Delete job
     */
    public function destroy($id)
    {
        try {
            $job = HidrologiJobs::findOrFail($id);

            // Log sebelum delete
            HidrologiLog::create([
                'job_id' => $job->id,
                'job_uuid' => $job->job_id,
                'log_level' => 'warning',
                'event_type' => 'deleted',
                'message' => 'Job deleted by user',
                'progress_at_event' => $job->progress,
                'status_at_event' => $job->status
            ]);

            // Soft delete
            $job->delete();

            return response()->json([
                'success' => true,
                'message' => 'Job berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus job: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete multiple jobs
     */
    public function bulkDestroy(Request $request)
    {
        try {
            $validated = $request->validate([
                'job_ids' => 'required|array|min:1',
                'job_ids.*' => 'required|integer|exists:hidrologi_jobs,id'
            ]);

            $jobIds = $validated['job_ids'];
            $deletedCount = 0;
            $failedCount = 0;
            $errors = [];

            DB::beginTransaction();
            
            foreach ($jobIds as $jobId) {
                try {
                    $job = HidrologiJobs::findOrFail($jobId);

                    // Log sebelum delete
                    HidrologiLog::create([
                        'job_id' => $job->id,
                        'job_uuid' => $job->job_id,
                        'log_level' => 'warning',
                        'event_type' => 'deleted',
                        'message' => 'Job deleted by user (bulk delete)',
                        'progress_at_event' => $job->progress,
                        'status_at_event' => $job->status
                    ]);

                    // Soft delete
                    $job->delete();
                    $deletedCount++;
                    
                } catch (\Exception $e) {
                    $failedCount++;
                    $errors[] = "Job ID {$jobId}: {$e->getMessage()}";
                    Log::error("Failed to delete job {$jobId}", [
                        'error' => $e->getMessage()
                    ]);
                }
            }

            DB::commit();

            // Prepare response message
            $message = "{$deletedCount} pekerjaan berhasil dihapus.";
            if ($failedCount > 0) {
                $message .= " {$failedCount} pekerjaan gagal dihapus.";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'deleted_count' => $deletedCount,
                'failed_count' => $failedCount,
                'errors' => $errors
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid: ' . implode(', ', $e->errors()['job_ids'] ?? ['Unknown error'])
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk delete error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel job yang sedang processing
     */
    public function cancel($id)
    {
        $job = HidrologiJobs::findOrFail($id);

        if (in_array($job->status, ['pending', 'submitted', 'processing'])) {
            $job->update([
                'status' => 'cancelled',
                'status_message' => 'Job dibatalkan oleh user'
            ]);

            HidrologiLog::create([
                'job_id' => $job->id,
                'job_uuid' => $job->job_id,
                'log_level' => 'warning',
                'event_type' => 'cancelled',
                'message' => 'Job cancelled by user',
                'progress_at_event' => $job->progress,
                'status_at_event' => 'cancelled'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Job berhasil dibatalkan'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Job tidak dapat dibatalkan'
        ], 400);
    }
}