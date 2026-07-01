<?php
/**
 * Interface d'abstraction pour la base de données
 * Permet de basculer entre MySQL (legacy) et Supabase (future)
 */
interface DatabaseInterface
{
    /**
     * Exécute une requête SQL et retourne le statement
     */
    public function query(string $sql, array $params = []): object;

    /**
     * Récupère une seule ligne
     */
    public function fetch(string $sql, array $params = []): ?array;

    /**
     * Récupère toutes les lignes
     */
    public function fetchAll(string $sql, array $params = []): array;

    /**
     * Exécute une requête sans retourner de résultat (INSERT, UPDATE, DELETE)
     */
    public function execute(string $sql, array $params = []): bool;

    /**
     * Retourne le dernier ID inséré
     */
    public function lastInsertId(): string|int;

    /**
     * Commence une transaction
     */
    public function beginTransaction(): bool;

    /**
     * Valide une transaction
     */
    public function commit(): bool;

    /**
     * Annule une transaction
     */
    public function rollback(): bool;
}
