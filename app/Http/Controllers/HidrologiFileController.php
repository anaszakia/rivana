<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\HidrologiApiService;
use App\Models\HidrologiFile;

class HidrologiFileController extends Controller
{
    protected $apiService;

    public function __construct(HidrologiApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Increment download counter and download file
     */
    public function download($id)
    {
        $file = HidrologiFile::findOrFail($id);

        // Get download URL from API
        $downloadUrl = $this->apiService->getDownloadUrl($file->job_uuid, $file->filename);

        \Log::info('File download requested', [
            'file_id' => $id,
            'file_name' => $file->filename,
            'job_uuid' => $file->job_uuid,
            'download_url' => $downloadUrl
        ]);

        try {
            // Create stream context with Bearer token and proper error handling
            $context = stream_context_create([
                'http' => [
                    'timeout' => 30,
                    'ignore_errors' => true,
                    'follow_location' => 1,
                    'header' => $this->apiService->getAuthHeaderString()
                ]
            ]);
            
            // Fetch file content (validates HTTP status, won't return error bodies as "success")
            $fileContent = $this->fetchRemoteFile($downloadUrl, $context);
            
            if ($fileContent === null) {
                $error = error_get_last();
                \Log::error('File download failed - request unsuccessful or returned no content', [
                    'file_id' => $id,
                    'file_name' => $file->filename,
                    'job_uuid' => $file->job_uuid,
                    'url' => $downloadUrl,
                    'error' => $error['message'] ?? 'Unknown error',
                    'http_response_status' => $this->lastFetchStatusLine ?? 'No headers'
                ]);
                
                // Return 404 with proper error message
                abort(404, 'File tidak ditemukan di server API. Pastikan job telah selesai diproses.');
            }
            
            // Update download count hanya jika berhasil
            $file->incrementDownloadCount();
            
            // Determine content type
            $contentType = $file->mime_type ?? $this->getMimeType($file->file_type);
            
            \Log::info('File downloaded successfully', [
                'file_id' => $id,
                'file_name' => $file->filename,
                'size' => strlen($fileContent),
                'content_type' => $contentType
            ]);
            
            // Return file dengan proper headers
            return response($fileContent)
                ->header('Content-Type', $contentType)
                ->header('Content-Disposition', 'attachment; filename="' . $file->filename . '"')
                ->header('Content-Length', strlen($fileContent))
                ->header('Cache-Control', 'public, max-age=3600');
                
        } catch (\Exception $e) {
            \Log::error('File download exception: ' . $e->getMessage(), [
                'file_id' => $id,
                'file_name' => $file->filename,
                'job_uuid' => $file->job_uuid,
                'url' => $downloadUrl,
                'exception' => $e->getTraceAsString()
            ]);
            
            // Return proper error response
            abort(500, 'Terjadi kesalahan saat mengunduh file: ' . $e->getMessage());
        }
    }
    
    /**
     * Get MIME type based on file extension
     */
    private function getMimeType($fileType)
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
        ];
        
        return $mimeTypes[$fileType] ?? 'application/octet-stream';
    }

    /**
     * Holds the status line of the most recent fetchRemoteFile() call, for logging.
     */
    private $lastFetchStatusLine = null;

    /**
     * Fetch a remote file via stream context and validate the HTTP status code.
     *
     * file_get_contents() with 'ignore_errors' => true still returns the response
     * body even on 401/404/500 instead of returning false - so without checking
     * the actual status line, an API error JSON (e.g. {"error":"Unauthorized"})
     * would silently be treated as the real file content. This guards against that.
     *
     * @return string|null  File content on success (HTTP 2xx), null on any failure
     */
    private function fetchRemoteFile($url, $context)
    {
        $content = @file_get_contents($url, false, $context);
        $this->lastFetchStatusLine = $http_response_header[0] ?? null;

        if ($content === false) {
            return null;
        }

        if (!$this->isSuccessfulHttpResponse($http_response_header ?? null)) {
            return null;
        }

        return $content;
    }

    /**
     * Check whether the $http_response_header populated by file_get_contents()
     * indicates a 2xx HTTP status.
     */
    private function isSuccessfulHttpResponse($headers)
    {
        if (empty($headers) || !isset($headers[0])) {
            return false;
        }

        if (preg_match('#^HTTP/\S+\s+(\d{3})#', $headers[0], $matches)) {
            $statusCode = (int) $matches[1];
            return $statusCode >= 200 && $statusCode < 300;
        }

        return false;
    }

    /**
     * Preview file (untuk gambar, CSV, JSON)
     */
    public function preview($id)
    {
        $file = HidrologiFile::findOrFail($id);

        // Get preview URL from API
        $previewUrl = $this->apiService->getPreviewUrl($file->job_uuid, $file->filename);
        // Get download URL as fallback
        $downloadUrl = $this->apiService->getDownloadUrl($file->job_uuid, $file->filename);

        \Log::info('Attempting file preview', [
            'file_id' => $id,
            'file_name' => $file->filename,
            'file_type' => $file->file_type,
            'job_uuid' => $file->job_uuid,
            'preview_url' => $previewUrl,
            'download_url' => $downloadUrl
        ]);

        try {
            // Coba ambil konten dengan context options untuk error handling yang lebih baik + Bearer Token
            $context = stream_context_create([
                'http' => [
                    'timeout' => 30,
                    'ignore_errors' => true,
                    'follow_location' => 1,
                    'header' => $this->apiService->getAuthHeaderString()
                ]
            ]);
            
            // Untuk PNG/Image - fetch dan return sebagai response
            if ($file->isImage()) {
                // Coba preview URL dulu
                $imageContent = $this->fetchRemoteFile($previewUrl, $context);
                
                // Jika gagal, coba download URL
                if ($imageContent === null) {
                    \Log::warning('Preview URL failed, trying download URL', [
                        'file_id' => $id,
                        'preview_url' => $previewUrl,
                        'status' => $this->lastFetchStatusLine ?? 'No headers'
                    ]);
                    $imageContent = $this->fetchRemoteFile($downloadUrl, $context);
                }
                
                if ($imageContent === null) {
                    $error = error_get_last();
                    \Log::error('Image file not found or not accessible from both URLs', [
                        'file_id' => $id,
                        'file_name' => $file->filename,
                        'preview_url' => $previewUrl,
                        'download_url' => $downloadUrl,
                        'error' => $error['message'] ?? 'Unknown error'
                    ]);
                    abort(404, 'Gambar tidak ditemukan atau tidak dapat diakses');
                }
                
                // Return image dengan proper headers
                return response($imageContent)
                    ->header('Content-Type', $this->getMimeType($file->file_type))
                    ->header('Cache-Control', 'public, max-age=86400') // Cache 24 hours
                    ->header('Access-Control-Allow-Origin', '*');
            }
            
            // Untuk CSV - fetch dan return sebagai text
            if ($file->file_type === 'csv') {
                // Coba preview URL dulu
                $csvContent = $this->fetchRemoteFile($previewUrl, $context);
                
                // Jika gagal, coba download URL
                if ($csvContent === null) {
                    \Log::warning('CSV preview URL failed, trying download URL', [
                        'file_id' => $id,
                        'preview_url' => $previewUrl,
                        'status' => $this->lastFetchStatusLine ?? 'No headers'
                    ]);
                    $csvContent = $this->fetchRemoteFile($downloadUrl, $context);
                }
                
                if ($csvContent === null) {
                    $error = error_get_last();
                    \Log::error('CSV file not found or not accessible from both URLs', [
                        'file_id' => $id,
                        'file_name' => $file->filename,
                        'preview_url' => $previewUrl,
                        'download_url' => $downloadUrl,
                        'error' => $error['message'] ?? 'Unknown error'
                    ]);
                    abort(404, 'File CSV tidak ditemukan atau tidak dapat diakses');
                }
                
                return response($csvContent)
                    ->header('Content-Type', 'text/csv; charset=UTF-8')
                    ->header('Cache-Control', 'public, max-age=3600')
                    ->header('Access-Control-Allow-Origin', '*');
            }
            
            // Untuk JSON - fetch dan return sebagai JSON
            if ($file->file_type === 'json') {
                // Coba preview URL dulu
                $jsonContent = $this->fetchRemoteFile($previewUrl, $context);
                
                // Jika gagal, coba download URL
                if ($jsonContent === null) {
                    \Log::warning('JSON preview URL failed, trying download URL', [
                        'file_id' => $id,
                        'preview_url' => $previewUrl,
                        'status' => $this->lastFetchStatusLine ?? 'No headers'
                    ]);
                    $jsonContent = $this->fetchRemoteFile($downloadUrl, $context);
                }
                
                if ($jsonContent === null) {
                    $error = error_get_last();
                    \Log::error('JSON file not found or not accessible from both URLs', [
                        'file_id' => $id,
                        'file_name' => $file->filename,
                        'preview_url' => $previewUrl,
                        'download_url' => $downloadUrl,
                        'error' => $error['message'] ?? 'Unknown error'
                    ]);
                    abort(404, 'File JSON tidak ditemukan atau tidak dapat diakses');
                }
                
                return response($jsonContent)
                    ->header('Content-Type', 'application/json; charset=UTF-8')
                    ->header('Cache-Control', 'public, max-age=3600')
                    ->header('Access-Control-Allow-Origin', '*');
            }
            
            // Untuk HTML - fetch dan return sebagai HTML (untuk peta interaktif)
            if ($file->file_type === 'html') {
                // Coba preview URL dulu
                $htmlContent = $this->fetchRemoteFile($previewUrl, $context);
                
                // Jika gagal, coba download URL
                if ($htmlContent === null) {
                    \Log::warning('HTML preview URL failed, trying download URL', [
                        'file_id' => $id,
                        'preview_url' => $previewUrl,
                        'status' => $this->lastFetchStatusLine ?? 'No headers'
                    ]);
                    $htmlContent = $this->fetchRemoteFile($downloadUrl, $context);
                }
                
                if ($htmlContent === null) {
                    $error = error_get_last();
                    \Log::error('HTML file not found or not accessible from both URLs', [
                        'file_id' => $id,
                        'file_name' => $file->filename,
                        'preview_url' => $previewUrl,
                        'download_url' => $downloadUrl,
                        'error' => $error['message'] ?? 'Unknown error'
                    ]);
                    abort(404, 'File HTML tidak ditemukan atau tidak dapat diakses');
                }
                
                \Log::info('HTML content fetched successfully', [
                    'file_id' => $id,
                    'content_length' => strlen($htmlContent),
                    'file_name' => $file->filename
                ]);
                
                // Return HTML ASLI tanpa modifikasi - biarkan browser handle semuanya
                // Hanya set headers yang diperlukan agar bisa di-embed di iframe
                return response($htmlContent, 200, [
                    'Content-Type' => 'text/html; charset=UTF-8',
                    'Cache-Control' => 'no-cache, no-store, must-revalidate',
                    'Access-Control-Allow-Origin' => '*',
                    'Access-Control-Allow-Methods' => 'GET, POST, OPTIONS',
                    'Access-Control-Allow-Headers' => '*',
                    'X-Content-Type-Options' => 'nosniff'
                ]);
            }
            
            // Untuk file lain, redirect ke download
            abort(404, 'Preview tidak tersedia untuk tipe file ini');
            
        } catch (\Exception $e) {
            \Log::error('File preview exception: ' . $e->getMessage(), [
                'file_id' => $id,
                'file_name' => $file->filename,
                'file_type' => $file->file_type,
                'preview_url' => $previewUrl,
                'download_url' => $downloadUrl,
                'trace' => $e->getTraceAsString()
            ]);
            
            abort(404, 'Tidak dapat memuat file: ' . $e->getMessage());
        }
    }

    /**
     * Get file info sebagai JSON
     */
    public function info($id)
    {
        $file = HidrologiFile::with('job')->findOrFail($id);

        return response()->json([
            'success' => true,
            'file' => $file
        ]);
    }
}