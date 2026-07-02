<?php
function sanitize_text(string $value): string
{
    return trim($value);
}

function validate_email(string $email): ?string
{
    return filter_var(trim($email), FILTER_VALIDATE_EMAIL) ?: null;
}

function generate_csrf_token(): string
{
    $token = '';
    if (!empty($_SESSION['csrf_token'])) {
        $token = $_SESSION['csrf_token'];
    } elseif (!empty($_COOKIE['csrf_token'])) {
        $token = $_COOKIE['csrf_token'];
        $_SESSION['csrf_token'] = $token;
    } else {
        $token = bin2hex(random_bytes(16));
        $_SESSION['csrf_token'] = $token;
        
        $isSecure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
        setcookie('csrf_token', $token, [
            'expires' => 0,
            'path' => '/',
            'secure' => $isSecure,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
    }
    return $token;
}

function verify_csrf_token(?string $token): bool
{
    if (empty($token)) {
        return false;
    }
    
    $expected = $_SESSION['csrf_token'] ?? $_COOKIE['csrf_token'] ?? '';
    return !empty($expected) && hash_equals($expected, $token);
}

function safe_file_name(string $fileName): string
{
    $fileName = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', basename($fileName));
    return substr($fileName, 0, 200);
}
