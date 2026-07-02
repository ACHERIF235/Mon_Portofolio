<?php
require_once __DIR__ . '/../core/bootstrap.php';
require_once __DIR__ . '/../models/AdminUserModel.php';

class AuthController
{
    public static function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = sanitize_text($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if (authenticate_admin($email, $password)) {
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                session_regenerate_id(true);
                $user = AdminUserModel::findByEmail($email);
                if ($user) {
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id'] = $user['id'];
                    $_SESSION['admin_email'] = $user['email'];
                }
                header('Location: dashboard.php');
                exit;
            }
            $error = 'Email ou mot de passe invalide.';
            View::render('admin/login', ['error' => $error]);
            return;
        }

        View::render('admin/login', ['error' => null]);
    }

    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            setcookie(session_name(), '', time() - 42000, '/');
        }
        admin_logout();
        session_destroy();
        header('Location: login.php');
        exit;
    }
}
