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
            // Create stream context with proper error handling
            $context = stream_context_create([
                'http' => [
                    'timeout' => 30,
                    'ignore_errors' => true,
                    'follow_location' => 1
                ]
            ]);
            
            // Fetch file content
            $fileContent = @file_get_contents($downloadUrl, false, $context);
            
            if ($fileContent === false) {
                $error = error_get_last();
                \Log::error('File download failed - file_get_contents returned false', [
                    'file_id' => $id,
                    'file_name' => $file->filename,
                    'job_uuid' => $file->job_uuid,
                    'url' => $downloadUrl,
                    'error' => $error['message'] ?? 'Unknown error',
                    'http_response_header' => $http_response_header ?? 'No headers'
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
        ];
        
        return $mimeTypes[$fileType] ?? 'application/octet-stream';
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
            // Coba ambil konten dengan context options untuk error handling yang lebih baik
            $context = stream_context_create([
                'http' => [
                    'timeout' => 30,
                    'ignore_errors' => true,
                    'follow_location' => 1
                ]
            ]);
            
            // Untuk PNG/Image - fetch dan return sebagai response
            if ($file->isImage()) {
                // Coba preview URL dulu
                $imageContent = @file_get_contents($previewUrl, false, $context);
                
                // Jika gagal, coba download URL
                if ($imageContent === false) {
                    \Log::warning('Preview URL failed, trying download URL', [
                        'file_id' => $id,
                        'preview_url' => $previewUrl
                    ]);
                    $imageContent = @file_get_contents($downloadUrl, false, $context);
                }
                
                if ($imageContent === false) {
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
                    ->header('Content-Type', 'image/' . $file->file_type)
                    ->header('Cache-Control', 'public, max-age=86400') // Cache 24 hours
                    ->header('Access-Control-Allow-Origin', '*');
            }
            
            // Untuk CSV - fetch dan return sebagai text
            if ($file->file_type === 'csv') {
                // Coba preview URL dulu
                $csvContent = @file_get_contents($previewUrl, false, $context);
                
                // Jika gagal, coba download URL
                if ($csvContent === false) {
                    \Log::warning('CSV preview URL failed, trying download URL', [
                        'file_id' => $id,
                        'preview_url' => $previewUrl
                    ]);
                    $csvContent = @file_get_contents($downloadUrl, false, $context);
                }
                
                if ($csvContent === false) {
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
                $jsonContent = @file_get_contents($previewUrl, false, $context);
                
                // Jika gagal, coba download URL
                if ($jsonContent === false) {
                    \Log::warning('JSON preview URL failed, trying download URL', [
                        'file_id' => $id,
                        'preview_url' => $previewUrl
                    ]);
                    $jsonContent = @file_get_contents($downloadUrl, false, $context);
                }
                
                if ($jsonContent === false) {
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
