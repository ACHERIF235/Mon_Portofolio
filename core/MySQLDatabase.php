<?php
require_once __DIR__ . '/DatabaseInterface.php';

/**
 * Implémentation MySQL de l'interface Database
 * Maintient la compatibilité avec l'architecture existante
 */
class MySQLDatabase implements DatabaseInterface
{
    private PDO $pdo;

    public function __construct(string $host, string $dbname, string $user, string $password)
    {
        $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
        $this->pdo = new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    }

    public function query(string $sql, array $params = []): object
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function fetch(string $sql, array $params = []): ?array
    {
        $result = $this->query($sql, $params)->fetch();
        return $result ?: null;
    }

    public function fetchAll(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll();
    }

    public function execute(string $sql, array $params = []): bool
    {
        return $this->query($sql, $params) !== false;
    }

    public function lastInsertId(): string|int
    {
        return $this->pdo->lastInsertId();
    }

    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->pdo->commit();
    }

    public function rollback(): bool
    {
        return $this->pdo->rollBack();
    }

    /**
     * Accès direct au PDO pour compatibilité legacy
     */
    public function getPDO(): PDO
    {
        return $this->pdo;
    }
}
