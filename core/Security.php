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
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token(?string $token): bool
{
    return !empty($token) && !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function safe_file_name(string $fileName): string
{
    $fileName = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', basename($fileName));
    return substr($fileName, 0, 200);
}
