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
            $supabaseUrl = getenv('SUPABASE_URL') ?: (getenv('URL_SUPABASE') ?: ($_SERVER['SUPABASE_URL'] ?? ($_SERVER['URL_SUPABASE'] ?? ($_ENV['SUPABASE_URL'] ?? ($_ENV['URL_SUPABASE'] ?? ($config['supabase_url'] ?? ''))))));
            $supabaseKey = getenv('SUPABASE_KEY') ?: (getenv('SUPABASE_API_KEY') ?: (getenv('CLÉ_PUBLISHABLE_SUPABASE') ?: (getenv('CLE_PUBLISHABLE_SUPABASE') ?: ($_SERVER['SUPABASE_KEY'] ?? ($_SERVER['SUPABASE_API_KEY'] ?? ($_SERVER['CLÉ_PUBLISHABLE_SUPABASE'] ?? ($_SERVER['CLE_PUBLISHABLE_SUPABASE'] ?? ($_ENV['SUPABASE_KEY'] ?? ($_ENV['SUPABASE_API_KEY'] ?? ($_ENV['CLÉ_PUBLISHABLE_SUPABASE'] ?? ($_ENV['CLE_PUBLISHABLE_SUPABASE'] ?? ($config['supabase_key'] ?? ''))))))))))));
            $supabaseAuthToken = getenv('SUPABASE_AUTH_TOKEN') ?: (getenv('CLÉ_SECRET_SUPABASE') ?: (getenv('CLE_SECRET_SUPABASE') ?: ($_SERVER['SUPABASE_AUTH_TOKEN'] ?? ($_SERVER['CLÉ_SECRET_SUPABASE'] ?? ($_SERVER['CLE_SECRET_SUPABASE'] ?? ($_ENV['SUPABASE_AUTH_TOKEN'] ?? ($_ENV['CLÉ_SECRET_SUPABASE'] ?? ($_ENV['CLE_SECRET_SUPABASE'] ?? ($config['supabase_auth_token'] ?? $supabaseKey)))))))));
            
            $supabaseUrl = rtrim(trim($supabaseUrl), '/');
            $supabaseKey = trim($supabaseKey);
            $supabaseAuthToken = trim($supabaseAuthToken);

            // Upload to Supabase Storage
            $url = $supabaseUrl . '/storage/v1/object/uploads/' . $destinationName;
            $url .= '?apikey=' . urlencode($supabaseKey); // Fallback for headers stripping

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'apikey: ' . $supabaseKey,
                'Authorization: Bearer ' . $supabaseAuthToken,
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
                return $supabaseUrl . '/storage/v1/object/public/uploads/' . $destinationName;
            }
            error_log("Supabase Storage Upload Error: HTTP $httpCode - Response: $response");
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
