<?php
$config = require __DIR__ . '/../config.php';

// Connection handled by DatabaseFactory
require_once __DIR__ . '/DatabaseFactory.php';

function db()
{
    // Retro-compatibility, returns the interface instance
    return DatabaseFactory::getInstance();
}

function db_query(string $sql, array $params = [])
{
    $db = DatabaseFactory::getInstance();
    // If it's SELECT, we might want to return something iterable, but the legacy code 
    // uses db_query to return PDOStatement, then calls fetch() or fetchAll().
    // The codebase actually uses db_fetch and db_fetchAll directly mostly.
    // For INSERT/UPDATE/DELETE, execute is called.
    if (stripos(trim($sql), 'SELECT') === 0) {
        return $db->query($sql, $params);
    } else {
        return $db->execute($sql, $params);
    }
}

function db_fetchAll(string $sql, array $params = []): array
{
    return DatabaseFactory::getInstance()->fetchAll($sql, $params);
}

function db_fetch(string $sql, array $params = []): ?array
{
    $result = DatabaseFactory::getInstance()->fetch($sql, $params);
    return $result ?: null;
}
