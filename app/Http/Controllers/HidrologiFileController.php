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

        // Update download count menggunakan method dari model
        $file->incrementDownloadCount();

        // Get download URL from API
        $downloadUrl = $this->apiService->getDownloadUrl($file->job_uuid, $file->filename);

        try {
            // Fetch file content
            $fileContent = @file_get_contents($downloadUrl);
            
            if ($fileContent === false) {
                \Log::error('File download failed', [
                    'file_id' => $id,
                    'file_name' => $file->filename,
                    'url' => $downloadUrl
                ]);
                
                // Try redirect as fallback
                return redirect($downloadUrl);
            }
            
            // Determine content type
            $contentType = $file->mime_type ?? $this->getMimeType($file->file_type);
            
            // Return file dengan proper headers
            return response($fileContent)
                ->header('Content-Type', $contentType)
                ->header('Content-Disposition', 'attachment; filename="' . $file->filename . '"')
                ->header('Content-Length', strlen($fileContent));
                
        } catch (\Exception $e) {
            \Log::error('File download error: ' . $e->getMessage(), [
                'file_id' => $id,
                'file_name' => $file->filename,
                'url' => $downloadUrl
            ]);
            
            // Fallback to redirect
            return redirect($downloadUrl);
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

        try {
            // Untuk PNG/Image - fetch dan return sebagai response
            if ($file->isImage()) {
                $imageContent = file_get_contents($previewUrl);
                
                if ($imageContent === false) {
                    abort(404, 'Image file not found');
                }
                
                // Return image dengan proper headers
                return response($imageContent)
                    ->header('Content-Type', 'image/' . $file->file_type)
                    ->header('Cache-Control', 'public, max-age=86400'); // Cache 24 hours
            }
            
            // Untuk CSV - fetch dan return sebagai text
            if ($file->file_type === 'csv') {
                $csvContent = file_get_contents($previewUrl);
                
                if ($csvContent === false) {
                    abort(404, 'CSV file not found');
                }
                
                return response($csvContent)
                    ->header('Content-Type', 'text/csv')
                    ->header('Cache-Control', 'public, max-age=3600');
            }
            
            // Untuk JSON - fetch dan return sebagai JSON
            if ($file->file_type === 'json') {
                $jsonContent = file_get_contents($previewUrl);
                
                if ($jsonContent === false) {
                    abort(404, 'JSON file not found');
                }
                
                return response($jsonContent)
                    ->header('Content-Type', 'application/json')
                    ->header('Cache-Control', 'public, max-age=3600');
            }
            
            // Untuk file lain, redirect ke download
            abort(404, 'Preview not available for this file type');
            
        } catch (\Exception $e) {
            \Log::error('File preview error: ' . $e->getMessage(), [
                'file_id' => $id,
                'file_name' => $file->filename,
                'url' => $previewUrl
            ]);
            
            abort(404, 'Unable to load file: ' . $e->getMessage());
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
