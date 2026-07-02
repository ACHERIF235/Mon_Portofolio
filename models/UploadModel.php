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

        $destinationName = sprintf('%s-%s.%s', time(), bin2hex(random_bytes(8)), $extension);
        $config = require __DIR__ . '/../config.php';
        
        $dbType = $_ENV['DB_TYPE'] ?? $config['db_type'] ?? 'mysql';
        
        if ($dbType === 'supabase') {
            // Upload to Supabase Storage
            $url = rtrim($config['supabase_url'] ?? '', '/') . '/storage/v1/object/uploads/' . $destinationName;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'apikey: ' . ($config['supabase_key'] ?? ''),
                'Authorization: Bearer ' . ($config['supabase_auth_token'] ?? ''),
                'Content-Type: ' . $mimeType
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents($file['tmp_name']));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if (is_resource($ch)) {
                curl_close($ch);
            }
            
            if ($httpCode >= 200 && $httpCode < 300) {
                // Return public URL
                return rtrim($config['supabase_url'] ?? '', '/') . '/storage/v1/object/public/uploads/' . $destinationName;
            }
            return null;
        }

        if (!is_dir($targetDir) && !mkdir($targetDir, 0755, true) && !is_dir($targetDir)) {
            return null;
        }

        $destinationPath = rtrim($targetDir, '/\\') . DIRECTORY_SEPARATOR . $destinationName;

        if (move_uploaded_file($file['tmp_name'], $destinationPath)) {
            return $destinationName;
        }
        return null;
    }
}
