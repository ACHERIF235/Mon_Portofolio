<?php
require_once __DIR__ . '/../core/DatabaseFactory.php';

/**
 * Repository de base avec les opérations CRUD communes
 * Utilise le pattern Repository pour l'abstraction de la base de données
 */
abstract class BaseRepository
{
    protected DatabaseInterface $db;
    protected string $table;
    protected string $primaryKey = 'id';

    public function __construct()
    {
        $this->db = DatabaseFactory::getInstance();
    }

    /**
     * Trouve un enregistrement par son ID
     */
    public function findById(int $id): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1",
            ['id' => $id]
        );
    }

    /**
     * Trouve tous les enregistrements
     */
    public function findAll(string $orderBy = 'id ASC'): array
    {
        return $this->db->fetchAll("SELECT * FROM {$this->table} ORDER BY {$orderBy}");
    }

    /**
     * Trouve des enregistrements avec des filtres
     */
    public function findBy(array $criteria, string $orderBy = 'id ASC'): array
    {
        $where = [];
        $params = [];
        
        foreach ($criteria as $field => $value) {
            $where[] = "$field = :$field";
            $params[$field] = $value;
        }
        
        $sql = "SELECT * FROM {$this->table}";
        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        $sql .= " ORDER BY {$orderBy}";
        
        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Trouve un enregistrement avec des filtres
     */
    public function findOneBy(array $criteria): ?array
    {
        $results = $this->findBy($criteria, 'id ASC LIMIT 1');
        return $results[0] ?? null;
    }

    /**
     * Compte le nombre d'enregistrements
     */
    public function count(array $criteria = []): int
    {
        $where = '';
        $params = [];
        
        if (!empty($criteria)) {
            $conditions = [];
            foreach ($criteria as $field => $value) {
                $conditions[] = "$field = :$field";
                $params[$field] = $value;
            }
            $where = " WHERE " . implode(' AND ', $conditions);
        }
        
        $result = $this->db->fetch("SELECT COUNT(*) as total FROM {$this->table}{$where}", $params);
        return (int)($result['total'] ?? 0);
    }

    /**
     * Crée un nouvel enregistrement
     */
    public function create(array $data): bool
    {
        $columns = array_keys($data);
        $placeholders = array_map(fn($col) => ":$col", $columns);
        
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $columns) . ") 
                VALUES (" . implode(', ', $placeholders) . ")";
        
        return $this->db->execute($sql, $data);
    }

    /**
     * Met à jour un enregistrement
     */
    public function update(int $id, array $data): bool
    {
        $set = [];
        foreach (array_keys($data) as $column) {
            $set[] = "$column = :$column";
        }
        
        $data[$this->primaryKey] = $id;
        $sql = "UPDATE {$this->table} SET " . implode(', ', $set) . " 
                WHERE {$this->primaryKey} = :{$this->primaryKey}";
        
        return $this->db->execute($sql, $data);
    }

    /**
     * Supprime un enregistrement
     */
    public function delete(int $id): bool
    {
        return $this->db->execute(
            "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id",
            ['id' => $id]
        );
    }

    /**
     * Exécute une requête personnalisée
     */
    protected function query(string $sql, array $params = []): array
    {
        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Exécute une requête personnalisée et retourne une seule ligne
     */
    protected function queryOne(string $sql, array $params = []): ?array
    {
        return $this->db->fetch($sql, $params);
    }

    /**
     * Exécute une requête sans retour (INSERT/UPDATE/DELETE)
     */
    protected function execute(string $sql, array $params = []): bool
    {
        return $this->db->execute($sql, $params);
    }
}
