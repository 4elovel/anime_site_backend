<?php

namespace AnimeSite\Models\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use AnimeSite\Services\FileService;

trait HasFiles
{
    /**
     * Get the file service instance
     *
     * @return FileService
     */
    protected function fileService(): FileService
    {
        return app(FileService::class);
    }

    /**
     * Handle file upload for a model attribute
     *
     * @param UploadedFile|string|null $file The uploaded file or file path
     * @param string $directory The directory to store the file in
     * @param string|null $oldFilePath The old file path to delete if exists
     * @return string|null The stored file path or null if no file
     */
    public function handleFileUpload($file, string $directory, ?string $oldFilePath = null): ?string
    {
        // If it's already a string and not a file, return it
        if (is_string($file) && !$file instanceof UploadedFile) {
            return $file;
        }

        // If it's a file, store it
        if ($file instanceof UploadedFile) {
            return $this->fileService()->storeFile($file, $directory, $oldFilePath);
        }

        // If it's a base64 string, store it
        if (is_string($file) && str_starts_with($file, 'data:image')) {
            return $this->fileService()->storeBase64Image($file, $directory, $oldFilePath);
        }

        return null;
    }

    /**
     * Get the full URL for a file path
     *
     * @param string|null $filePath The file path
     * @return string|null The full URL or null if no file
     */
    public function getFileUrl(?string $filePath): ?string
    {
        return $this->fileService()->getFileUrl($filePath);
    }

    /**
     * Delete a file
     *
     * @param string|null $filePath The file path to delete
     * @return bool Whether the file was deleted
     */
    public function deleteFile(?string $filePath): bool
    {
        return $this->fileService()->deleteFile($filePath);
    }

    /**
     * Process an array of files (for JSON fields like pictures)
     *
     * @param array|null $files Array of files or file paths
     * @param string $directory The directory to store files in
     * @param array|null $oldFiles The old files to delete if replaced
     * @return array The processed files
     */
    public function processFilesArray(?array $files, string $directory, ?array $oldFiles = null): array
    {
        if (!$files) {
            return [];
        }

        $processedFiles = [];
        $oldFilesMap = [];

        // Create a map of old files for easier lookup
        if ($oldFiles) {
            foreach ($oldFiles as $oldFile) {
                if (is_string($oldFile)) {
                    $oldFilesMap[basename($oldFile)] = $oldFile;
                }
            }
        }

        foreach ($files as $index => $file) {
            // If it's already a string path and not a new file, keep it
            if (is_string($file) && !$file instanceof UploadedFile && !str_starts_with($file, 'data:image')) {
                $processedFiles[] = $file;
                continue;
            }

            // Find old file to replace if any
            $oldFilePath = null;
            if ($oldFiles && isset($oldFiles[$index])) {
                $oldFilePath = $oldFiles[$index];
            }

            // Store the new file
            $filePath = $this->handleFileUpload($file, $directory, $oldFilePath);
            if ($filePath) {
                $processedFiles[] = $filePath;
            }
        }

        // Delete any old files that weren't replaced
        if ($oldFiles) {
            foreach ($oldFiles as $oldFile) {
                if (is_string($oldFile) && !in_array($oldFile, $processedFiles)) {
                    $this->deleteFile($oldFile);
                }
            }
        }

        return $processedFiles;
    }

    /**
     * Process attachments for Movie model
     *
     * @param array|null $attachments The attachments array
     * @param string $directory The directory to store files in
     * @return array The processed attachments
     */
    public function processAttachments(?array $attachments, string $directory): array
    {
        if (!$attachments) {
            return [];
        }

        return $this->fileService()->processAttachments($attachments, $directory);
    }
}
