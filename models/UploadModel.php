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
        
        require_once __DIR__ . '/../core/DatabaseFactory.php';
        $db = DatabaseFactory::getInstance();
        
        if ($db instanceof SupabaseDatabase) {
            return $db->uploadFile('uploads', $destinationName, $file['tmp_name'], $mimeType);
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
