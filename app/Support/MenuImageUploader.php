<?php
declare(strict_types=1);

namespace App\Support;

use RuntimeException;

final class MenuImageUploader
{
    public static function storeOptional(array|null $file): ?string
    {
        if (!is_array($file) || (($file['name'] ?? '') === '')) {
            return null;
        }

        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Menu image upload failed. Please try again.');
        }

        $maxSize = 5 * 1024 * 1024;

        if ((int) ($file['size'] ?? 0) > $maxSize) {
            throw new RuntimeException('Menu image must be 5MB or smaller.');
        }

        $tmpName = (string) ($file['tmp_name'] ?? '');

        if ($tmpName === '' || !is_uploaded_file($tmpName)) {
            throw new RuntimeException('Invalid menu image upload.');
        }

        $imageInfo = @getimagesize($tmpName);
        $allowedMimeTypes = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        ];

        $mimeType = $imageInfo['mime'] ?? '';

        if (!isset($allowedMimeTypes[$mimeType])) {
            throw new RuntimeException('Menu image must be a JPG, PNG, or WEBP file.');
        }

        $uploadDirectory = dirname(__DIR__, 2) . '/public/uploads/menu-items';

        if (!is_dir($uploadDirectory) && !mkdir($uploadDirectory, 0755, true) && !is_dir($uploadDirectory)) {
            throw new RuntimeException('Unable to prepare menu image upload directory.');
        }

        $filename = sprintf('menu_%d_%s.%s', time(), bin2hex(random_bytes(4)), $allowedMimeTypes[$mimeType]);
        $targetPath = $uploadDirectory . DIRECTORY_SEPARATOR . $filename;

        if (!move_uploaded_file($tmpName, $targetPath)) {
            throw new RuntimeException('Failed to save the uploaded menu image.');
        }

        return 'public/uploads/menu-items/' . $filename;
    }
}
