<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait FileUploadTrait
{
    /**
     * Upload any file (image, pdf, document, video, etc.) with timestamp unique filename.
     *
     * @param UploadedFile|string|null $file
     * @param string $folder Directory folder name inside storage/public (e.g. 'students', 'teachers', 'attachments')
     * @param string|null $oldFilePath Existing file path to delete if updating
     * @return string|null Relational path or URL of uploaded file
     */
    public function uploadFile($file, string $folder = 'uploads', ?string $oldFilePath = null): ?string
    {
        if (!$file) {
            return $oldFilePath;
        }

        // If $file is an instance of UploadedFile
        if ($file instanceof UploadedFile) {
            // Remove old file if updating
            if ($oldFilePath) {
                $this->deleteFile($oldFilePath);
            }

            // Generate unique filename with timestamp + random string
            $timestamp = date('Ymd_His');
            $random = Str::random(8);
            $extension = strtolower($file->getClientOriginalExtension() ?: 'bin');
            $filename = "{$timestamp}_{$random}.{$extension}";

            // Store file on public disk
            $path = $file->storeAs($folder, $filename, 'public');

            return Storage::disk('public')->url($path);
        }

        // If string (URL or base64 or already uploaded path), return as is
        return is_string($file) ? $file : $oldFilePath;
    }

    /**
     * Delete an existing file from disk.
     */
    public function deleteFile(?string $filePath): bool
    {
        if (empty($filePath)) {
            return false;
        }

        // Extract relative storage path if full URL is given
        if (str_contains($filePath, '/storage/')) {
            $relativePath = str_after($filePath, '/storage/');
            if (Storage::disk('public')->exists($relativePath)) {
                return Storage::disk('public')->delete($relativePath);
            }
        }

        if (Storage::disk('public')->exists($filePath)) {
            return Storage::disk('public')->delete($filePath);
        }

        return false;
    }
}
