<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class HidrologiApiService
{
    protected $apiUrl;
    protected $apiToken;
    protected $timeout;

    public function __construct()
    {
        // Prioritas: services_hidrologi.php, fallback ke services.php
        $this->apiUrl = config('services_hidrologi.hidrologi.api_url') ?? config('services.hidrologi.api_url', 'http://localhost:8000');
        $this->apiToken = config('services_hidrologi.hidrologi.api_token') ?? config('services.hidrologi.api_key');
        $this->timeout = config('services_hidrologi.hidrologi.timeout') ?? config('services.hidrologi.timeout', 300);
        
        // Validasi token
        if (empty($this->apiToken)) {
            Log::warning('Hidrologi API Token not configured in .env file. Please set RIVANA_API_TOKEN or HIDROLOGI_API_KEY');
        }
    }
    
    /**
     * Get headers dengan Bearer Token
     */
    protected function getHeaders()
    {
        return [
            'Authorization' => 'Bearer ' . $this->apiToken,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];
    }

    /**
     * Submit job baru ke Python API
     */
    // Ganti baris 43:
public function submitJob($longitude, $latitude, $startDate, $endDate, $dasName = null, $dasAreaKm2 = null, $dasLevel = null, $hybasId = null)
    {
        try {
            // Konversi ke float untuk memastikan tipe data benar
            $lng = floatval($longitude);
            $lat = floatval($latitude);
            
            // Data yang akan dikirim sesuai format API Python
            $payload = [
                'longitude'    => $lng,
                'latitude'     => $lat,
                'start'        => $startDate,
                'end'          => $endDate,
                'das_name'     => $dasName,
                'das_area_km2' => $dasAreaKm2 ? floatval($dasAreaKm2) : null,
                'das_level'    => $dasLevel ? intval($dasLevel) : null,
                'hybas_id'     => $hybasId ? floatval($hybasId) : null,
            ];
            
            // Log payload untuk debugging
            Log::info('Sending to Hidrologi API', [
                'url' => "{$this->apiUrl}/generate",
                'payload' => $payload
            ]);
            
            $response = Http::timeout($this->timeout)
                ->withHeaders($this->getHeaders())
                ->post("{$this->apiUrl}/generate", $payload);

            // Log response untuk debugging
            Log::info('Hidrologi API Response', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                // Sanitize invalid JSON tokens (NaN, Infinity, -Infinity) yang
                // dikirim oleh API Python tapi tidak valid menurut standar JSON
                $rawBody = $response->body();
                $sanitized = preg_replace('/\b(-?Infinity|NaN)\b/', 'null', $rawBody);
                $data = json_decode($sanitized, true);
                
                // API Python mengembalikan: {job_id, status, message}
                return [
                    'success' => true,
                    'data' => [
                        'job_id' => $responseData['job_id'],
                        'status' => $responseData['status'] ?? 'processing',
                        'message' => $responseData['message'] ?? 'Job submitted successfully',
                        'longitude' => $lng,
                        'latitude' => $lat,
                        'start_date' => $startDate,
                        'end_date' => $endDate
                    ]
                ];
            }

            return [
                'success' => false,
                'error' => 'API returned error: ' . $response->status(),
                'response' => $response->body()
            ];

        } catch (Exception $e) {
            Log::error('Hidrologi API Submit Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Cek status job
     */
    public function checkStatus($jobId)
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders($this->getHeaders())
                ->get("{$this->apiUrl}/status/{$jobId}");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'error' => 'Job not found'
            ];

        } catch (Exception $e) {
            Log::error('Hidrologi API Status Check Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get daftar semua file hasil
     */
    public function getResultFiles($jobId)
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders($this->getHeaders())
                ->get("{$this->apiUrl}/result/{$jobId}");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'error' => 'Results not found'
            ];

        } catch (Exception $e) {
            Log::error('Hidrologi API Get Results Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get daftar semua files (PNG, CSV, JSON, dll)
     */
    public function getFiles($jobId)
    {
        try {
            // Try to get all files first
            $response = Http::timeout(10)
                ->withHeaders($this->getHeaders())
                ->get("{$this->apiUrl}/files/{$jobId}");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            // Fallback to images endpoint jika files endpoint tidak ada
            return $this->getImages($jobId);

        } catch (Exception $e) {
            Log::error('Hidrologi API Get Files Error: ' . $e->getMessage());
            // Fallback to images
            return $this->getImages($jobId);
        }
    }
    
    /**
     * Get daftar file PNG saja (backward compatibility)
     */
    public function getImages($jobId)
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders($this->getHeaders())
                ->get("{$this->apiUrl}/images/{$jobId}");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'error' => 'Images not found'
            ];

        } catch (Exception $e) {
            Log::error('Hidrologi API Get Images Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get URL untuk preview gambar
     */
    public function getPreviewUrl($jobId, $filename)
    {
        // Encode filename untuk menghindari masalah dengan karakter spesial
        $encodedFilename = rawurlencode($filename);
        $url = "{$this->apiUrl}/preview/{$jobId}/{$encodedFilename}";
        
        Log::info('Generated preview URL', [
            'job_id' => $jobId,
            'filename' => $filename,
            'encoded_filename' => $encodedFilename,
            'url' => $url
        ]);
        
        return $url;
    }

    /**
     * Get URL untuk download file
     */
    public function getDownloadUrl($jobId, $filename)
    {
        // Encode filename untuk menghindari masalah dengan karakter spesial
        $encodedFilename = rawurlencode($filename);
        return "{$this->apiUrl}/download/{$jobId}/{$encodedFilename}";
    }

     /**
     * Get text summary hasil analisis (structured)
     */
    public function getSummary($jobId)
    {
        try {
            $url = "{$this->apiUrl}/summary/{$jobId}";
            
            Log::info('Fetching summary from API', [
                'job_id' => $jobId,
                'url' => $url
            ]);
            
            $response = Http::timeout(10)
                ->withHeaders($this->getHeaders())
                ->get($url);

            if ($response->successful()) {
                $data = $response->json();
                
                // Log khusus untuk TWI analysis
                Log::info('Summary API Response - TWI Check', [
                    'job_id' => $jobId,
                    'has_twi_analysis' => isset($data['twi_analysis']),
                    'twi_analysis_type' => isset($data['twi_analysis']) ? gettype($data['twi_analysis']) : 'not_set',
                    'twi_keys' => isset($data['twi_analysis']) && is_array($data['twi_analysis']) ? array_keys($data['twi_analysis']) : [],
                    'twi_status' => isset($data['twi_analysis']['status']) ? $data['twi_analysis']['status'] : 'no_status',
                    'twi_enhanced' => isset($data['twi_analysis']['twi_enhanced']) ? $data['twi_analysis']['twi_enhanced'] : 'not_found',
                    'twi_risk_level' => isset($data['twi_analysis']['risk_level']) ? $data['twi_analysis']['risk_level'] : 'not_found',
                    'response_size' => strlen($response->body()),
                    'top_level_keys' => is_array($data) ? array_keys($data) : []
                ]);
                
                return [
                    'success' => true,
                    'data' => $data
                ];
            }

            Log::warning('Summary API returned non-successful response', [
                'job_id' => $jobId,
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return [
                'success' => false,
                'error' => 'Summary not found'
            ];

        } catch (Exception $e) {
            Log::error('Hidrologi API Get Summary Error: ' . $e->getMessage(), [
                'job_id' => $jobId,
                'exception' => $e->getTraceAsString()
            ]);
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get FULL LOGS lengkap dari Python API (semua output text)
     * Endpoint: GET /logs/{job_id}
     */
    public function getLogs($jobId)
    {
        try {
            $response = Http::timeout(15)
                ->withHeaders($this->getHeaders())
                ->get("{$this->apiUrl}/logs/{$jobId}");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'error' => 'Logs not found'
            ];

        } catch (Exception $e) {
            Log::error('Hidrologi API Get Logs Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Ambil polygon DAS (HydroSHEDS) untuk titik & level tertentu.
     * Dipakai oleh form create (Step 2 - pilih level DAS), BUKAN setelah job submit.
     */
    public function getDasInfo($lat, $lon, $level)
    {
        try {
            $response = Http::timeout(30) // query GEE FeatureCollection bisa beberapa detik
                ->withHeaders($this->getHeaders())
                ->post("{$this->apiUrl}/das-info", [
                    'lat'   => floatval($lat),
                    'lon'   => floatval($lon),
                    'level' => intval($level),
                ]);

            $data = $response->json();

            if ($response->successful() && ($data['success'] ?? false)) {
                return [
                    'success' => true,
                    'data' => $data
                ];
            }

            return [
                'success' => false,
                'status'  => $response->status(),
                'message' => $data['message'] ?? 'DAS tidak ditemukan untuk lokasi/level ini'
            ];

        } catch (Exception $e) {
            Log::error('Hidrologi API Get DAS Info Error: ' . $e->getMessage());
            return [
                'success' => false,
                'status'  => 500,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Get Authorization header string
     */
    public function getAuthHeaderString()
    {
        return 'Authorization: Bearer ' . $this->apiToken;
    }
}