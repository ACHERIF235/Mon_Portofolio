<?php
require_once __DIR__ . '/db.php';

function get_setting(string $key, $default = null)
{
    $row = db_fetch('SELECT `value` FROM settings WHERE `key` = :key LIMIT 1', ['key' => $key]);
    return $row['value'] ?? $default;
}

function get_all_settings(): array
{
    $rows = db_fetchAll('SELECT `key`, `value` FROM settings');
    $settings = [];
    foreach ($rows as $row) {
        $settings[$row['key']] = $row['value'];
    }
    return $settings;
}

function save_setting(string $key, string $value): void
{
    if (db_fetch('SELECT 1 FROM settings WHERE `key` = :key', ['key' => $key])) {
        db_query('UPDATE settings SET `value` = :value WHERE `key` = :key', ['key' => $key, 'value' => $value]);
    } else {
        db_query('INSERT INTO settings (`key`, `value`) VALUES (:key, :value)', ['key' => $key, 'value' => $value]);
    }
}

function translate(array $item, string $lang, string $field): string
{
    $key = $field . '_' . $lang;
    return $item[$key] ?? $item[$field . '_fr'] ?? $item[$field . '_en'] ?? '';
}

function slugify(string $text): string
{
    $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = trim($text, '-');
    $text = strtolower($text);
    return preg_replace('~[^-\w]+~', '', $text);
}

function flash(string $message, string $type = 'success'): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['flash'] = ['message' => $message, 'type' => $type];
}

function get_flash(): ?array
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function upload_file(array $file, array $allowedTypes, string $targetDir, array $allowedExtensions = []): ?string
{
    if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $mimeType = mime_content_type($file['tmp_name']);
    if (!in_array($mimeType, $allowedTypes, true)) {
        return null;
    }

    $safeName = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', basename($file['name']));
    $extension = pathinfo($safeName, PATHINFO_EXTENSION);
    if ($allowedExtensions && !in_array(strtolower($extension), $allowedExtensions, true)) {
        return null;
    }

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    $filename = sprintf('%s-%s.%s', time(), bin2hex(random_bytes(6)), $extension);
    $destination = rtrim($targetDir, '/\\') . DIRECTORY_SEPARATOR . $filename;

    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return $filename;
    }
    return null;
}

function send_email(string $to, string $subject, string $body, string $replyTo = ''): bool
{
    $resendApiKey = getenv('RESEND_API_KEY') ?: ($_SERVER['RESEND_API_KEY'] ?? ($_ENV['RESEND_API_KEY'] ?? ''));
    
    // If a Resend API key is provided, use Resend API
    if ($resendApiKey) {
        $ch = curl_init('https://api.resend.com/emails');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $resendApiKey,
            'Content-Type: application/json'
        ]);
        
        $payload = [
            'from' => 'Acme <onboarding@resend.dev>', // Resend trial requires this from address
            'to' => [$to],
            'subject' => $subject,
            'html' => nl2br(htmlspecialchars($body)),
        ];
        
        if ($replyTo) {
            $payload['reply_to'] = $replyTo;
        }

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (is_resource($ch)) {
            curl_close($ch);
        }
        
        return $httpCode >= 200 && $httpCode < 300;
    }
    
    // Fallback to PHP's built-in mail() function
    $headers = 'From: no-reply@' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . "\r\n";
    if ($replyTo) {
        $headers .= 'Reply-To: ' . $replyTo . "\r\n";
    }
    $headers .= 'Content-Type: text/plain; charset=UTF-8' . "\r\n";
    
    return @mail($to, $subject, $body, $headers);
}
