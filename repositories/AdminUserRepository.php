<?php
require_once __DIR__ . '/BaseRepository.php';

/**
 * Repository pour la table admin_users
 * Remplace AdminUserModel avec une abstraction de base de données
 */
class AdminUserRepository extends BaseRepository
{
    protected string $table = 'admin_users';
    protected string $primaryKey = 'id';

    /**
     * Trouve un utilisateur par email
     */
    public function findByEmail(string $email): ?array
    {
        return $this->findOneBy(['email' => $email]);
    }

    /**
     * Vérifie les identifiants de connexion
     */
    public function verifyCredentials(string $email, string $password): bool
    {
        $user = $this->findByEmail($email);
        return $user && password_verify($password, $user['password_hash']);
    }

    /**
     * Crée un nouvel administrateur
     */
    public function createAdmin(string $email, string $password, string $name): bool
    {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        return $this->create([
            'email' => $email,
            'password_hash' => $passwordHash,
            'name' => $name
        ]);
    }

    /**
     * Met à jour le mot de passe d'un utilisateur
     */
    public function updatePassword(int $userId, string $newPassword): bool
    {
        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        return $this->update($userId, ['password_hash' => $passwordHash]);
    }

    /**
     * Récupère un utilisateur sans le hash du mot de passe
     */
    public function findSafeById(int $id): ?array
    {
        $user = $this->findById($id);
        if ($user) {
            unset($user['password_hash']);
        }
        return $user;
    }
}
