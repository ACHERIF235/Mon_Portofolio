<?php
require_once __DIR__ . '/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function is_logged_in(): bool
{
    return !empty($_SESSION['admin_id']);
}

function require_admin(): void
{
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function authenticate_admin(string $email, string $password): bool
{
    $user = db_fetch('SELECT * FROM admin_users WHERE email = :email LIMIT 1', ['email' => $email]);
    if ($user && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_email'] = $user['email'];
        return true;
    }
    return false;
}

function admin_logout(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        setcookie(session_name(), '', time() - 42000);
    }
    session_destroy();
}

function admin_user(): ?array
{
    if (!is_logged_in()) {
        return null;
    }
    return db_fetch('SELECT id, email, name FROM admin_users WHERE id = :id', ['id' => $_SESSION['admin_id']]);
}
