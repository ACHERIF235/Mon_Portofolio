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

    public static function updateCredentials(int $id, string $email, ?string $password): bool
    {
        if ($password) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            return db_query('UPDATE admin_users SET email = :email, password_hash = :hash WHERE id = :id', [
                'email' => $email,
                'hash' => $hash,
                'id' => $id
            ]);
        } else {
            return db_query('UPDATE admin_users SET email = :email WHERE id = :id', [
                'email' => $email,
                'id' => $id
            ]);
        }
    }
}
