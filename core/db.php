<?php
$config = require __DIR__ . '/../config.php';

try {
    $pdo = new PDO(
        'mysql:host=' . $config['db_host'] . ';dbname=' . $config['db_name'] . ';charset=utf8mb4',
        $config['db_user'],
        $config['db_pass'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );
} catch (PDOException $e) {
    die('Erreur de connexion à la base de données : ' . htmlspecialchars($e->getMessage()));
}

function db(): PDO
{
    global $pdo;
    return $pdo;
}

function db_query(string $sql, array $params = []): PDOStatement
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

function db_fetchAll(string $sql, array $params = []): array
{
    return db_query($sql, $params)->fetchAll();
}

function db_fetch(string $sql, array $params = []): ?array
{
    $result = db_query($sql, $params)->fetch();
    return $result ?: null;
}
