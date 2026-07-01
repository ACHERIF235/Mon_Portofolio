<?php
require_once __DIR__ . '/BaseRepository.php';

/**
 * Repository pour la table inbox_messages
 * Remplace MessageModel avec une abstraction de base de données
 */
class MessageRepository extends BaseRepository
{
    protected string $table = 'inbox_messages';
    protected string $primaryKey = 'id';

    /**
     * Crée un nouveau message de contact
     */
    public function createMessage(string $name, string $email, string $message): bool
    {
        return $this->create([
            'name' => $name,
            'email' => $email,
            'message' => $message,
            'created_at' => date('Y-m-d H:i:s'),
            'is_read' => 0,
            'is_replied' => 0
        ]);
    }

    /**
     * Récupère les messages non lus
     */
    public function findUnread(): array
    {
        return $this->findBy(['is_read' => 0], 'created_at DESC');
    }

    /**
     * Récupère les messages avec pagination
     */
    public function findWithPagination(int $page = 1, int $perPage = 20): array
    {
        $offset = ($page - 1) * $perPage;
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table} ORDER BY created_at DESC LIMIT :limit OFFSET :offset",
            ['limit' => $perPage, 'offset' => $offset]
        );
    }

    /**
     * Compte le total des messages
     */
    public function countTotal(): int
    {
        $result = $this->db->fetch("SELECT COUNT(*) as total FROM {$this->table}");
        return (int)($result['total'] ?? 0);
    }

    /**
     * Marque un message comme lu
     */
    public function markAsRead(int $id): bool
    {
        return $this->update($id, ['is_read' => 1]);
    }

    /**
     * Marque un message comme répondu
     */
    public function markAsReplied(int $id): bool
    {
        return $this->update($id, ['is_replied' => 1, 'is_read' => 1]);
    }

    /**
     * Marque un message comme non lu
     */
    public function markAsUnread(int $id): bool
    {
        return $this->update($id, ['is_read' => 0]);
    }
}
