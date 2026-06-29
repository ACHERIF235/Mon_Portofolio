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

            if (AdminUserModel::verifyCredentials($email, $password)) {
                session_regenerate_id(true);
                $user = AdminUserModel::findByEmail($email);
                $_SESSION['admin_id'] = $user['id'];
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
        session_destroy();
        header('Location: login.php');
        exit;
    }
}
