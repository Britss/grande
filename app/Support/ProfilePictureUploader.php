<?php
declare(strict_types=1);

namespace App\Support;

use RuntimeException;

final class ProfilePictureUploader
{
    public static function storeRequired(array|null $file): string
    {
        if (!is_array($file) || (($file['name'] ?? '') === '')) {
            throw new RuntimeException('Please choose a profile picture to upload.');
        }

        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Profile picture upload failed. Please try again.');
        }

        $maxSize = 3 * 1024 * 1024;

        if ((int) ($file['size'] ?? 0) > $maxSize) {
            throw new RuntimeException('Profile picture must be 3MB or smaller.');
        }

        $tmpName = (string) ($file['tmp_name'] ?? '');

        if ($tmpName === '' || !is_uploaded_file($tmpName)) {
            throw new RuntimeException('Invalid profile picture upload.');
        }

        $imageInfo = @getimagesize($tmpName);
        $allowedMimeTypes = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        ];

        $mimeType = $imageInfo['mime'] ?? '';

        if (!isset($allowedMimeTypes[$mimeType])) {
            throw new RuntimeException('Profile picture must be a JPG, PNG, or WEBP file.');
        }

        $uploadDirectory = dirname(__DIR__, 2) . '/public/uploads/profile-pictures';

        if (!is_dir($uploadDirectory) && !mkdir($uploadDirectory, 0755, true) && !is_dir($uploadDirectory)) {
            throw new RuntimeException('Unable to prepare profile picture upload directory.');
        }

        $filename = sprintf('profile_%d_%s.%s', time(), bin2hex(random_bytes(4)), $allowedMimeTypes[$mimeType]);
        $targetPath = $uploadDirectory . DIRECTORY_SEPARATOR . $filename;

        if (!move_uploaded_file($tmpName, $targetPath)) {
            throw new RuntimeException('Failed to save the uploaded profile picture.');
        }

        return 'public/uploads/profile-pictures/' . $filename;
    }

    public static function deleteIfExists(?string $path): void
    {
        $path = trim((string) $path);

        if ($path === '' || !str_starts_with($path, 'public/uploads/profile-pictures/')) {
            return;
        }

        $fullPath = dirname(__DIR__, 2) . '/' . str_replace('/', DIRECTORY_SEPARATOR, $path);

        if (is_file($fullPath)) {
            @unlink($fullPath);
        }
    }
}
