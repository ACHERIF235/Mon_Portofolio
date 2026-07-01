<?php
require_once __DIR__ . '/BaseRepository.php';

/**
 * Repository pour les sections dynamiques (skills, projects, education, etc.)
 * Remplace SectionModel avec une abstraction de base de données
 */
class SectionRepository extends BaseRepository
{
    private array $sections = [
        'skills' => [
            'name' => 'Compétences',
            'table' => 'skills',
            'fields' => ['category', 'name_fr', 'name_en', 'level', 'order_index'],
        ],
        'projects' => [
            'name' => 'Projets',
            'table' => 'projects',
            'fields' => ['title_fr', 'title_en', 'description_fr', 'description_en', 'link', 'order_index'],
        ],
        'education' => [
            'name' => 'Formations',
            'table' => 'education',
            'fields' => ['title_fr', 'title_en', 'organization', 'period', 'description_fr', 'description_en', 'order_index'],
        ],
        'associations' => [
            'name' => 'Associations',
            'table' => 'associations',
            'fields' => ['title_fr', 'title_en', 'organization', 'period', 'description_fr', 'description_en', 'order_index'],
        ],
        'languages' => [
            'name' => 'Langues',
            'table' => 'languages',
            'fields' => ['name_fr', 'name_en', 'level', 'order_index'],
        ],
        'contacts' => [
            'name' => 'Contacts',
            'table' => 'contacts',
            'fields' => ['type', 'label_fr', 'label_en', 'value', 'order_index'],
        ],
        'inbox' => [
            'name' => 'Messages',
            'table' => 'inbox_messages',
            'fields' => ['name', 'email', 'created_at', 'message', 'is_read', 'is_replied'],
        ],
    ];

    /**
     * Récupère toutes les définitions de sections
     */
    public function getSections(): array
    {
        return $this->sections;
    }

    /**
     * Récupère une définition de section
     */
    public function getSection(string $key): ?array
    {
        return $this->sections[$key] ?? null;
    }

    /**
     * Récupère tous les éléments d'une section
     */
    public function findAllBySection(string $type): array
    {
        $section = $this->getSection($type);
        if (!$section) {
            return [];
        }

        $orderBy = in_array('order_index', $section['fields'], true)
            ? 'order_index ASC, id ASC'
            : 'id ASC';

        return $this->db->fetchAll("SELECT * FROM {$section['table']} ORDER BY {$orderBy}");
    }

    /**
     * Récupère un élément d'une section par son ID
     */
    public function findBySection(string $type, int $id): ?array
    {
        $section = $this->getSection($type);
        if (!$section) {
            return null;
        }

        return $this->db->fetch(
            "SELECT * FROM {$section['table']} WHERE id = :id LIMIT 1",
            ['id' => $id]
        );
    }

    /**
     * Sauvegarde ou met à jour un élément de section
     */
    public function saveSectionItem(string $type, array $data, ?int $id = null): bool
    {
        $section = $this->getSection($type);
        if (!$section) {
            return false;
        }

        $table = $section['table'];
        $fields = $section['fields'];
        
        // Filtrer uniquement les champs définis
        $payload = [];
        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $payload[$field] = $data[$field];
            }
        }

        if ($id !== null) {
            $set = [];
            foreach (array_keys($payload) as $column) {
                $set[] = "`$column` = :$column";
            }
            $payload['id'] = $id;
            $sql = "UPDATE {$table} SET " . implode(', ', $set) . " WHERE id = :id";
            return $this->db->execute($sql, $payload);
        }

        $columns = implode(', ', array_map(fn($f) => "`$f`", array_keys($payload)));
        $placeholders = implode(', ', array_map(fn($f) => ":$f", array_keys($payload)));
        $sql = "INSERT INTO {$table} ($columns) VALUES ($placeholders)";
        
        return $this->db->execute($sql, $payload);
    }

    /**
     * Supprime un élément de section
     */
    public function deleteSectionItem(string $type, int $id): bool
    {
        $section = $this->getSection($type);
        if (!$section) {
            return false;
        }

        return $this->db->execute(
            "DELETE FROM {$section['table']} WHERE id = :id",
            ['id' => $id]
        );
    }

    /**
     * Met à jour le statut de lecture d'un message
     */
    public function updateMessageReadStatus(int $id, bool $isRead): bool
    {
        return $this->db->execute(
            "UPDATE inbox_messages SET is_read = :is_read WHERE id = :id",
            ['is_read' => $isRead ? 1 : 0, 'id' => $id]
        );
    }

    /**
     * Met à jour le statut de réponse d'un message
     */
    public function updateMessageRepliedStatus(int $id, bool $isReplied): bool
    {
        return $this->db->execute(
            "UPDATE inbox_messages SET is_replied = :is_replied WHERE id = :id",
            ['is_replied' => $isReplied ? 1 : 0, 'id' => $id]
        );
    }

    /**
     * Compte les éléments non lus dans l'inbox
     */
    public function countUnreadMessages(): int
    {
        $result = $this->db->fetch(
            "SELECT COUNT(*) as total FROM inbox_messages WHERE is_read = 0"
        );
        return (int)($result['total'] ?? 0);
    }
}
