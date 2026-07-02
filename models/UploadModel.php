<?php
class UploadModel
{
    public static function save(array $file, array $allowedTypes, array $allowedExtensions, string $targetDir): ?string
    {
        if (!$file) {
            throw new RuntimeException("Aucun fichier reçu.");
        }
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $phpErrors = [
                UPLOAD_ERR_INI_SIZE => 'Le fichier dépasse la taille maximale autorisée (upload_max_filesize).',
                UPLOAD_ERR_FORM_SIZE => 'Le fichier dépasse la taille maximale autorisée par le formulaire.',
                UPLOAD_ERR_PARTIAL => 'Le fichier n\'a été que partiellement téléchargé.',
                UPLOAD_ERR_NO_FILE => 'Aucun fichier n\'a été téléchargé.',
                UPLOAD_ERR_NO_TMP_DIR => 'Le dossier temporaire est manquant.',
                UPLOAD_ERR_CANT_WRITE => 'Échec de l\'écriture du fichier sur le disque.',
                UPLOAD_ERR_EXTENSION => 'Une extension PHP a arrêté l\'envoi de fichier.'
            ];
            $err = $phpErrors[$file['error']] ?? 'Erreur inconnue ('.$file['error'].')';
            throw new RuntimeException("Erreur d'upload: $err");
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        if (!in_array($mimeType, $allowedTypes, true)) {
            throw new RuntimeException("Type MIME non autorisé : $mimeType");
        }

        $fileName = safe_file_name($file['name']);
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if ($allowedExtensions && !in_array($extension, $allowedExtensions, true)) {
            throw new RuntimeException("Extension non autorisée : $extension");
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

            $url = $supabaseUrl . '/storage/v1/object/uploads/' . $destinationName;
            $url .= '?apikey=' . urlencode($supabaseKey);

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
                return $supabaseUrl . '/storage/v1/object/public/uploads/' . $destinationName;
            }
            
            $err = json_decode($response, true);
            $msg = $err['message'] ?? $err['error'] ?? 'Erreur inconnue';
            throw new RuntimeException("Supabase HTTP $httpCode: $msg ($response)");
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
