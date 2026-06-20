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
            'longitude'          => 'required|numeric|between:-180,180',
            'latitude'           => 'required|numeric|between:-90,90',
            'start_date'         => 'required|date|before:end_date',
            'end_date'           => 'required|date|after:start_date|before_or_equal:today',
            'location_name'      => 'nullable|string|max:255',
            'location_description' => 'nullable|string|max:1000',
            // Tambah ini:
            'das_name'           => 'nullable|string|max:255',
            'das_area_km2'       => 'nullable|numeric',
            'das_level'          => 'nullable|integer|between:3,8',
            'hybas_id'           => 'nullable|numeric',
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
                $validated['end_date'],
                // Tambah ini:
                $validated['das_name']    ?? null,
                $validated['das_area_km2'] ?? null,
                $validated['das_level']   ?? null,
                $validated['hybas_id']    ?? null,
            );

            if ($result['success']) {
                // Update job dengan job_id dari API
                $apiData = $result['data'];
                $job->update([
                    'job_id' => $apiData['job_id'],
                    'status' => 'submitted',
                    'submitted_at' => now(),
                    'status_message' => 'Job successfully submitted to API, waiting for processing',
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
                    'message' => 'Job successfully submitted to API, waiting for processing',
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
     * AJAX endpoint Step 2 form create — ambil polygon DAS dari Python API
     * berdasarkan titik (lat/lon) & level HydroSHEDS yang dipilih user.
     */
    public function dasInfo(Request $request)
    {
        $validated = $request->validate([
            'lat'   => 'required|numeric|between:-90,90',
            'lon'   => 'required|numeric|between:-180,180',
            'level' => 'required|integer|between:3,8',
        ]);

        $result = $this->apiService->getDasInfo(
            $validated['lat'],
            $validated['lon'],
            $validated['level']
        );

        if ($result['success']) {
            return response()->json(array_merge(['success' => true], $result['data']));
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'] ?? 'DAS tidak ditemukan untuk lokasi/level ini'
        ], $result['status'] ?? 404);
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

        // 🔄 ALWAYS re-fetch files untuk memastikan file baru (seperti HTML peta) ikut masuk
        if (in_array($job->status, ['completed', 'completed_with_warning'])) {
            Log::info('Show job - fetching latest files', [
                'job_id' => $job->id,
                'job_uuid' => $job->job_id,
                'current_files_count' => $job->files->count()
            ]);
            
            $this->fetchAndStoreFiles($job);
            $job->load('files'); // Reload files relation
            
            Log::info('Show job - files after fetch', [
                'job_id' => $job->id,
                'files_count' => $job->files->count(),
                'files' => $job->files->pluck('filename')->toArray()
            ]);
        }

        // Ambil text summary dari API (prioritas: summary terstruktur, fallback ke logs lengkap)
        $summary = null;
        $fullLogs = null;
        
        Log::info('Loading job detail page', [
            'job_id' => $job->id,
            'job_uuid' => $job->job_id,
            'status' => $job->status
        ]);
        
        if (in_array($job->status, ['completed', 'completed_with_warning'])) {
            // Ambil summary terstruktur langsung dari API (English keys)
            $summaryResult = $this->apiService->getSummary($job->job_id);
            if ($summaryResult['success']) {
                $summary = $summaryResult['data']['summary'] ?? null;
                
                // 🐛 DEBUG: Log TWI analysis data structure untuk debugging VPS vs Lokal
                Log::info('Summary TWI Analysis Debug - Controller Level', [
                    'job_id' => $job->id,
                    'job_uuid' => $job->job_id,
                    'summary_has_twi' => isset($summary['twi_analysis']),
                    'twi_is_array' => isset($summary['twi_analysis']) && is_array($summary['twi_analysis']),
                    'twi_keys' => isset($summary['twi_analysis']) ? array_keys($summary['twi_analysis']) : null,
                    'twi_status' => $summary['twi_analysis']['status'] ?? 'no_status_key',
                    'twi_enhanced' => $summary['twi_analysis']['twi_enhanced'] ?? 'not_found',
                    'twi_risk_level' => $summary['twi_analysis']['risk_level'] ?? 'not_found',
                    'twi_has_flood_zones' => isset($summary['twi_analysis']['flood_zones']),
                    'twi_has_rtho' => isset($summary['twi_analysis']['rtho_recommendations']),
                    'full_twi_structure' => isset($summary['twi_analysis']) ? json_encode($summary['twi_analysis'], JSON_PRETTY_PRINT) : 'TWI data not found in summary',
                    'api_result_keys' => isset($summaryResult['data']) ? array_keys($summaryResult['data']) : [],
                    'summary_top_level_keys' => isset($summary) ? array_keys($summary) : []
                ]);
            } else {
                Log::warning('Failed to get summary from API', [
                    'job_id' => $job->id,
                    'job_uuid' => $job->job_id,
                    'error' => $summaryResult['error'] ?? 'Unknown error'
                ]);
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
                
                // Ensure file_type is not empty, default to 'unknown'
                if (empty($fileType)) {
                    $fileType = 'unknown';
                    \Log::warning('File type empty for file: ' . $file['name']);
                }
                
                // Get MIME type
                $mimeType = $this->getMimeTypeFromExtension($fileType);
                
                \Log::info('Storing file', [
                    'filename' => $file['name'],
                    'file_type' => $fileType,
                    'mime_type' => $mimeType
                ]);
                
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
                        'display_order' => $file['display_order'] ?? $filesStored,
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
        if ($progress < 20) return __('messages.status_preparing_data');
        if ($progress < 40) return __('messages.status_fetching_gee');
        if ($progress < 60) return __('messages.status_running_ml');
        if ($progress < 90) return __('messages.status_creating_viz');
        if ($progress < 100) return __('messages.status_finishing');
        return __('messages.status_done');
    }

    /**
     * Get display name for file
     */
    protected function getDisplayName($filename)
    {
        $names = [
            // WEAP/RIVANA Dashboard files
            'WEAP_Dashboard.png'                    => 'WEAP Main Dashboard',
            'WEAP_Enhanced_Dashboard.png'           => 'WEAP Complete Dashboard',
            'WEAP_Water_Balance_Dashboard.png'      => 'Water Balance Dashboard',
            'WEAP_Morphometry_Summary.png'          => 'Morphometry Summary',
            'WEAP_Morphology_Ecology_Dashboard.png' => 'Morphology & Ecology Dashboard',
            'RIVANA_Dashboard.png'                  => 'RIVANA Main Dashboard',
            'RIVANA_Enhanced_Dashboard.png'         => 'RIVANA Complete Dashboard',
            'RIVANA_Water_Balance_Dashboard.png'    => 'Water Balance Dashboard',
            'RIVANA_Morphometry_Summary.png'        => 'Morphometry Summary',
            'RIVANA_Morphology_Ecology_Dashboard.png' => 'Morphology & Ecology Dashboard',
            'RIVANA_Baseline_Comparison.png'        => 'ML vs Traditional Comparison',
            'RIVANA_TWI_Dashboard.png'              => '🌊 TWI Analysis Dashboard (Flood Zones & RTH)',

            // River Network Map files
            'RIVANA_Peta_Aliran_Sungai.html'        => '🗺️ Interactive River Network Map',
            'RIVANA_Peta_Aliran_Sungai.png'         => '📷 River Network Map (Image)',
            'RIVANA_Metadata_Peta.json'             => '📊 River Map Metadata',

            // CSV files
            'RIVANA_Hasil_Complete.csv'             => 'Complete Simulation Results',
            'RIVANA_Monthly_WaterBalance.csv'       => 'Monthly Water Balance',
            'RIVANA_Prediksi_30Hari.csv'            => '30-Day Forecast Data',
            'GEE_Raw_Data.csv'                      => 'Raw Satellite Data (GEE)',
            'RIVANA_WaterBalance_Indices.csv'       => 'Water Balance Indices',

            // JSON files
            'RIVANA_WaterBalance_Validation.json'   => 'Water Balance Validation',
            'RIVANA_Model_Validation_Complete.json' => 'Complete Model Validation',
            'RIVANA_Model_Validation_Report.json'   => 'Model Validation Report',
            'RIVANA_Baseline_Comparison.json'       => 'Baseline Comparison',
            'GEE_Data_Metadata.json'                => 'GEE Data Source & Statistics',
            'RIVANA_TWI_Analysis.json'              => '🌊 TWI Analysis, Flood Zones & Drainage',
            'RIVANA_Dam_Cost_Estimate.json'         => '🏗️ Dam Construction Cost Estimate',
        ];

        return $names[$filename] ?? $filename;
    }

    /**
     * Get description for file
     */
    protected function getFileDescription($filename)
    {
        $descriptions = [
            // WEAP files
            'WEAP_Dashboard.png'                    => 'Main dashboard showing reservoir status, supply-demand balance, and operational recommendations',
            'WEAP_Enhanced_Dashboard.png'           => 'Complete dashboard with cost-benefit analysis, water quality, and efficiency metrics',
            'WEAP_Water_Balance_Dashboard.png'      => 'Water inflow and outflow balance analysis for the catchment area',
            'WEAP_Morphometry_Summary.png'          => 'Summary of watershed morphometric parameters',
            'WEAP_Morphology_Ecology_Dashboard.png' => 'River condition analysis including sediment and ecosystem health',

            // RIVANA files
            'RIVANA_Dashboard.png'                  => 'Main dashboard showing hydrological analysis results using Machine Learning',
            'RIVANA_Enhanced_Dashboard.png'         => 'Complete dashboard with predictions, risk analysis, and recommendations',
            'RIVANA_Water_Balance_Dashboard.png'    => 'Water balance visualization: input vs output, hydrological components',
            'RIVANA_Morphometry_Summary.png'        => 'Watershed morphometric parameters: area, shape, slope, drainage',
            'RIVANA_Morphology_Ecology_Dashboard.png' => 'River geomorphological condition and aquatic ecosystem health',
            'RIVANA_Baseline_Comparison.png'        => 'Accuracy comparison between Machine Learning model and traditional methods',
            'RIVANA_TWI_Dashboard.png'              => 'Topographic Wetness Index analysis: flood-prone zones, RTH (green open space), and drainage recommendations',

            // River Network Map files
            'RIVANA_Peta_Aliran_Sungai.html'        => 'Interactive map with zoom, layer toggle, and river flow information. Open in browser for detailed exploration.',
            'RIVANA_Peta_Aliran_Sungai.png'         => 'Static visualization of river network with flow accumulation and topography. Suitable for reports and presentations.',
            'RIVANA_Metadata_Peta.json'             => 'Complete map metadata: coordinates, flow characteristics, water statistics, and satellite data sources.',

            // CSV files
            'RIVANA_Hasil_Complete.csv'             => 'Complete time series data: rainfall, temperature, ET, discharge, reservoir level, supply-demand',
            'RIVANA_Monthly_WaterBalance.csv'       => 'Monthly summary: precipitation, evapotranspiration, infiltration, runoff, storage',
            'RIVANA_Prediksi_30Hari.csv'            => '30-day rainfall and reservoir level forecast from ML model',
            'GEE_Raw_Data.csv'                      => 'Raw data from Google Earth Engine: rainfall, temperature, soil moisture, NDVI, evapotranspiration',
            'RIVANA_WaterBalance_Indices.csv'       => 'Calculated water balance indices derived from the simulation results',

            // JSON files
            'RIVANA_WaterBalance_Validation.json'   => 'Water balance validation with maximum 5% error tolerance',
            'RIVANA_Model_Validation_Complete.json' => 'Model validation metrics: NSE, R², PBIAS, RMSE for all parameters',
            'RIVANA_Model_Validation_Report.json'   => 'Complete validation report with interpretation and recommendations',
            'RIVANA_Baseline_Comparison.json'       => 'ML vs traditional method performance comparison (NRECA, SCS-CN)',
            'GEE_Data_Metadata.json'                => 'Google Earth Engine data sources and statistics used in the analysis',
            'RIVANA_TWI_Analysis.json'              => 'Detailed TWI analysis: flood zone coordinates, RTH recommendations, and drainage planning',
            'RIVANA_Dam_Cost_Estimate.json'         => 'Estimated dam construction cost: minimum/moderate/maximum HPS, RAB components, and work schedule (LPSE PUPR benchmark)',
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
                'message' => 'Job successfully deleted'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete job: ' . $e->getMessage()
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