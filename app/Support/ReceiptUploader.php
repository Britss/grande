<?php
declare(strict_types=1);

namespace App\Support;

use RuntimeException;

final class ReceiptUploader
{
    public static function validateAndStore(array|null $file): string
    {
        if (!is_array($file) || (($file['name'] ?? '') === '')) {
            throw new RuntimeException('Please upload your payment receipt before completing the order.');
        }

        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Receipt upload failed. Please try again.');
        }

        $maxSize = 5 * 1024 * 1024;

        if ((int) ($file['size'] ?? 0) > $maxSize) {
            throw new RuntimeException('Receipt image must be 5MB or smaller.');
        }

        $tmpName = (string) ($file['tmp_name'] ?? '');

        if ($tmpName === '' || !is_uploaded_file($tmpName)) {
            throw new RuntimeException('Invalid receipt upload.');
        }

        $imageInfo = @getimagesize($tmpName);
        $allowedMimeTypes = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        ];

        $mimeType = $imageInfo['mime'] ?? '';

        if (!isset($allowedMimeTypes[$mimeType])) {
            throw new RuntimeException('Receipt must be a JPG, PNG, or WEBP image.');
        }

        $uploadDirectory = dirname(__DIR__, 2) . '/public/uploads/receipts';

        if (!is_dir($uploadDirectory) && !mkdir($uploadDirectory, 0755, true) && !is_dir($uploadDirectory)) {
            throw new RuntimeException('Unable to prepare receipt upload directory.');
        }

        $filename = sprintf('receipt_%d_%s.%s', time(), bin2hex(random_bytes(4)), $allowedMimeTypes[$mimeType]);
        $targetPath = $uploadDirectory . DIRECTORY_SEPARATOR . $filename;

        if (!move_uploaded_file($tmpName, $targetPath)) {
            throw new RuntimeException('Failed to save the uploaded receipt.');
        }

        return $filename;
    }

    public static function deleteIfExists(?string $filename): void
    {
        $filename = trim((string) $filename);

        if ($filename === '') {
            return;
        }

        $path = dirname(__DIR__, 2) . '/public/uploads/receipts/' . $filename;

        if (is_file($path)) {
            @unlink($path);
        }
    }
}
