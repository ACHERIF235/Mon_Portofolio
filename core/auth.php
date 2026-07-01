<?php
require_once __DIR__ . '/db.php';

function get_jwt_secret(): string {
    $config = require __DIR__ . '/../config.php';
    return $config['supabase_auth_token'] ?? 'default_secret_key_change_me_in_prod';
}

function encode_jwt(array $payload): string {
    $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
    $payload['exp'] = time() + (86400 * 7); // 7 jours
    $payloadJson = json_encode($payload);
    
    $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
    $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payloadJson));
    
    $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, get_jwt_secret(), true);
    $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
    
    return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
}

function decode_jwt(string $token): ?array {
    $parts = explode('.', $token);
    if (count($parts) !== 3) return null;
    
    list($base64UrlHeader, $base64UrlPayload, $base64UrlSignature) = $parts;
    
    $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, get_jwt_secret(), true);
    $expectedSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
    
    if (!hash_equals($expectedSignature, $base64UrlSignature)) {
        return null;
    }
    
    $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $base64UrlPayload)), true);
    if (isset($payload['exp']) && $payload['exp'] < time()) {
        return null;
    }
    
    return $payload;
}

function is_logged_in(): bool {
    $token = $_COOKIE['admin_token'] ?? '';
    return !empty($token) && decode_jwt($token) !== null;
}

function require_admin(): void {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function authenticate_admin(string $email, string $password): bool {
    $user = db_fetch('SELECT * FROM admin_users WHERE email = :email LIMIT 1', ['email' => $email]);
    if ($user && password_verify($password, $user['password_hash'])) {
        $token = encode_jwt([
            'admin_id' => $user['id'],
            'email' => $user['email']
        ]);
        
        // Secure cookie flags
        $isSecure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
        setcookie('admin_token', $token, [
            'expires' => time() + (86400 * 7),
            'path' => '/',
            'secure' => $isSecure,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
        
        return true;
    }
    return false;
}

function admin_logout(): void {
    setcookie('admin_token', '', [
        'expires' => time() - 3600,
        'path' => '/'
    ]);
}

function admin_user(): ?array {
    $token = $_COOKIE['admin_token'] ?? '';
    $payload = decode_jwt($token);
    if (!$payload) {
        return null;
    }
    return db_fetch('SELECT id, email, name FROM admin_users WHERE id = :id', ['id' => $payload['admin_id']]);
}
