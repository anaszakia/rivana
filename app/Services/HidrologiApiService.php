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
    public function submitJob($longitude, $latitude, $startDate, $endDate)
    {
        try {
            // Konversi ke float untuk memastikan tipe data benar
            $lng = floatval($longitude);
            $lat = floatval($latitude);
            
            // Data yang akan dikirim sesuai format API Python
            $payload = [
                'longitude' => $lng,
                'latitude' => $lat,
                'start' => $startDate,
                'end' => $endDate
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
                $responseData = $response->json();
                
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
            $response = Http::timeout(10)
                ->withHeaders($this->getHeaders())
                ->get("{$this->apiUrl}/summary/{$jobId}");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'error' => 'Summary not found'
            ];

        } catch (Exception $e) {
            Log::error('Hidrologi API Get Summary Error: ' . $e->getMessage());
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
}
