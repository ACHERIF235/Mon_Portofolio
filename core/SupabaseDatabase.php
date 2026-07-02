<?php
require_once __DIR__ . '/DatabaseInterface.php';

/**
 * Implémentation Supabase de l'interface Database
 * Utilise l'API REST Supabase pour les requêtes
 * Préparé pour la migration future
 */
class SupabaseDatabase implements DatabaseInterface
{
    private string $url;
    private string $key;
    private string $authToken;

    public function __construct(string $url, string $key, ?string $authToken = null)
    {
        $this->url = rtrim(trim($url), '/');
        $this->key = trim($key);
        $this->authToken = trim($authToken ?? $key);
    }

    /**
     * Convertit les requêtes SQL en appels API Supabase
     * Note: Supabase REST API ne supporte pas le SQL natif
     * Cette méthode est une simplification - en production, utiliser pgsql
     */
    private function request(string $method, string $table, array $data = []): array
    {
        $url = "{$this->url}/rest/v1/{$table}";
        $headers = [
            'apikey: ' . $this->key,
            'Authorization: Bearer ' . $this->authToken,
            'Content-Type: application/json',
            'Prefer: return=representation'
        ];

        // Construction des filtres depuis les paramètres
        if (!empty($data['filters'])) {
            $filters = [];
            foreach ($data['filters'] as $field => $value) {
                $filters[] = "$field=eq.$value";
            }
            $url .= '?' . implode('&', $filters);
            unset($data['filters']);
        }
        
        // Fallback: send apikey in URL in case headers are stripped by a proxy
        $url .= (strpos($url, '?') !== false ? '&' : '?') . 'apikey=' . urlencode($this->key);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        if ($method !== 'GET' && !empty($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (is_resource($ch)) {
            curl_close($ch);
        }

        if ($httpCode >= 400) {
            $safeKey = substr($this->key, 0, 5) . '*** (len: ' . strlen($this->key) . ')';
            throw new RuntimeException("Supabase API error: HTTP $httpCode - $response [DEBUG: $safeKey]");
        }

        return json_decode($response, true) ?? [];
    }

    public function query(string $sql, array $params = []): object
    {
        // Pour Supabase, on utilise l'endpoint RPC via PostgREST
        // Cette méthode nécessite des fonctions SQL définies dans Supabase
        throw new RuntimeException("Direct SQL queries not supported in Supabase mode. Use repository methods instead.");
    }

    public function fetch(string $sql, array $params = []): ?array
    {
        if (preg_match('/SELECT\s+COUNT\(\*\)\s+AS\s+(\w+)\s+FROM\s+(\w+)/i', $sql, $matches)) {
            $alias = $matches[1];
            $table = $matches[2];
            $filters = $this->extractFilters($sql, $params);
            $results = $this->request('GET', $table, ['filters' => $filters]);
            return [$alias => count($results)];
        }

        // Parser la table depuis le SQL (simplification)
        if (preg_match('/FROM\s+(\w+)/i', $sql, $matches)) {
            $table = $matches[1];
            $filters = $this->extractFilters($sql, $params);
            $results = $this->request('GET', $table, ['filters' => $filters]);
            return $results[0] ?? null;
        }
        return null;
    }

    public function fetchAll(string $sql, array $params = []): array
    {
        if (preg_match('/FROM\s+(\w+)/i', $sql, $matches)) {
            $table = $matches[1];
            $filters = $this->extractFilters($sql, $params);
            return $this->request('GET', $table, ['filters' => $filters]);
        }
        return [];
    }

    public function execute(string $sql, array $params = []): bool
    {
        if (preg_match('/INSERT INTO\s+(\w+)/i', $sql, $matches)) {
            $table = $matches[1];
            $data = $this->extractInsertData($sql, $params);
            $this->request('POST', $table, $data);
            return true;
        }
        if (preg_match('/UPDATE\s+(\w+)/i', $sql, $matches)) {
            $table = $matches[1];
            $data = $this->extractUpdateData($sql, $params);
            $filters = $this->extractFilters($sql, $params);
            $this->request('PATCH', $table, array_merge($data, ['filters' => $filters]));
            return true;
        }
        if (preg_match('/DELETE FROM\s+(\w+)/i', $sql, $matches)) {
            $table = $matches[1];
            $filters = $this->extractFilters($sql, $params);
            $this->request('DELETE', $table, ['filters' => $filters]);
            return true;
        }
        return false;
    }

    public function lastInsertId(): string|int
    {
        // Supabase retourne l'ID dans la réponse
        return 0; // À implémenter avec tracking
    }

    public function beginTransaction(): bool
    {
        // Supabase ne supporte pas les transactions via REST API
        // Utiliser PostgreSQL direct ou des RPC functions
        return true;
    }

    public function commit(): bool
    {
        return true;
    }

    public function rollback(): bool
    {
        return true;
    }

    /**
     * Extrait les filtres depuis une requête SQL (simplification)
     */
    private function extractFilters(string $sql, array $params): array
    {
        $filters = [];
        if (preg_match('/WHERE\s+(\w+)\s*=\s*:(\w+)/i', $sql, $matches)) {
            $filters[$matches[1]] = $params[$matches[2]] ?? null;
        }
        return $filters;
    }

    /**
     * Extrait les données d'INSERT depuis une requête SQL (simplification)
     */
    private function extractInsertData(string $sql, array $params): array
    {
        $data = [];
        if (preg_match_all('/:(\w+)/', $sql, $matches)) {
            foreach ($matches[1] as $param) {
                if (isset($params[$param])) {
                    $data[$param] = $params[$param];
                }
            }
        }
        return $data;
    }

    /**
     * Extrait les données d'UPDATE depuis une requête SQL (simplification)
     */
    private function extractUpdateData(string $sql, array $params): array
    {
        $data = [];
        if (preg_match_all('/`?(\w+)`\?\s*=\s*:(\w+)/i', $sql, $matches)) {
            foreach ($matches[2] as $i => $param) {
                if (isset($params[$param])) {
                    $data[$matches[1][$i]] = $params[$param];
                }
            }
        }
        return $data;
    }
}
