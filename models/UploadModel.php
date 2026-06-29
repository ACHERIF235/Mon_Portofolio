<?php
class UploadModel
{
    public static function save(array $file, array $allowedTypes, array $allowedExtensions, string $targetDir): ?string
    {
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        if (!in_array($mimeType, $allowedTypes, true)) {
            return null;
        }

        $fileName = safe_file_name($file['name']);
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if ($allowedExtensions && !in_array($extension, $allowedExtensions, true)) {
            return null;
        }

        if (!is_dir($targetDir) && !mkdir($targetDir, 0755, true) && !is_dir($targetDir)) {
            return null;
        }

        $destinationName = sprintf('%s-%s.%s', time(), bin2hex(random_bytes(8)), $extension);
        $destinationPath = rtrim($targetDir, '/\\') . DIRECTORY_SEPARATOR . $destinationName;

        if (move_uploaded_file($file['tmp_name'], $destinationPath)) {
            return $destinationName;
        }
        return null;
    }
}
