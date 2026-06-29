<?php
class AdminUserModel
{
    public static function findByEmail(string $email): ?array
    {
        return db_fetch('SELECT * FROM admin_users WHERE email = :email LIMIT 1', ['email' => $email]);
    }

    public static function findById(int $id): ?array
    {
        return db_fetch('SELECT id, email, name FROM admin_users WHERE id = :id', ['id' => $id]);
    }

    public static function verifyCredentials(string $email, string $password): bool
    {
        $user = self::findByEmail($email);
        return $user && password_verify($password, $user['password_hash']);
    }
}
