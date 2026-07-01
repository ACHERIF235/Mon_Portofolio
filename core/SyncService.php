<?php
require_once __DIR__ . '/DatabaseFactory.php';

/**
 * Service de synchronisation entre MySQL et Supabase
 * Permet une migration progressive avec les deux bases actives
 */
class SyncService
{
    private DatabaseInterface $mysql;
    private DatabaseInterface $supabase;
    private string $logFile;
    
    // Tables à synchroniser avec leurs colonnes de timestamp
    private array $syncTables = [
        'settings' => null, // Pas de updated_at
        'admin_users' => null,
        'skills' => null,
        'projects' => null,
        'education' => null,
        'associations' => null,
        'languages' => null,
        'contacts' => null,
        'inbox_messages' => 'created_at',
    ];
    
    public function __construct()
    {
        // MySQL est la base primaire pendant la migration
        $config = require __DIR__ . '/../config.php';
        
        $this->mysql = new MySQLDatabase(
            $config['db_host'],
            $config['db_name'],
            $config['db_user'],
            $config['db_pass']
        );
        
        $this->supabase = new SupabaseDatabase(
            $config['supabase_url'] ?? '',
            $config['supabase_key'] ?? '',
            $config['supabase_auth_token'] ?? null
        );
        
        $this->logFile = __DIR__ . '/../logs/sync.log';
        $this->ensureLogDirectory();
    }
    
    /**
     * Synchronise toutes les tables vers Supabase
     */
    public function syncAllToSupabase(): array
    {
        $results = [];
        $startTime = microtime(true);
        
        foreach ($this->syncTables as $table => $timestampColumn) {
            $results[$table] = $this->syncTableToSupabase($table, $timestampColumn);
        }
        
        $duration = round((microtime(true) - $startTime) * 1000, 2);
        $this->log('SYNC_ALL', "Completed in {$duration}ms: " . json_encode($results));
        
        return $results;
    }
    
    /**
     * Synchronise une table spécifique vers Supabase
     */
    public function syncTableToSupabase(string $table, ?string $timestampColumn = null): array
    {
        $lastSync = $this->getLastSyncTime($table);
        $syncedCount = 0;
        $errors = [];
        
        try {
            // Récupérer les données depuis MySQL
            if ($timestampColumn && $lastSync) {
                // Sync incrémental
                $sql = "SELECT * FROM {$table} WHERE {$timestampColumn} > :last_sync ORDER BY {$timestampColumn}";
                $params = ['last_sync' => $lastSync];
            } else {
                // Sync complet
                $sql = "SELECT * FROM {$table}";
                $params = [];
            }
            
            $rows = $this->mysql->fetchAll($sql, $params);
            
            // Envoyer vers Supabase
            foreach ($rows as $row) {
                try {
                    $this->upsertToSupabase($table, $row);
                    $syncedCount++;
                } catch (Exception $e) {
                    $errors[] = [
                        'id' => $row['id'] ?? null,
                        'error' => $e->getMessage()
                    ];
                    $this->log('SYNC_ERROR', "Table $table, row " . ($row['id'] ?? 'unknown') . ": " . $e->getMessage());
                }
            }
            
            // Mettre à jour le timestamp de sync
            $this->updateLastSyncTime($table);
            
            $this->log('SYNC_TABLE', "Table $table: $syncedCount rows synced, " . count($errors) . " errors");
            
        } catch (Exception $e) {
            $this->log('SYNC_FATAL', "Fatal error syncing table $table: " . $e->getMessage());
            $errors[] = ['fatal' => $e->getMessage()];
        }
        
        return [
            'table' => $table,
            'synced' => $syncedCount,
            'errors' => $errors,
            'last_sync' => $lastSync
        ];
    }
    
    /**
     * Synchronise depuis Supabase vers MySQL (pour webhooks)
     */
    public function syncFromSupabase(array $payload): array
    {
        $table = $payload['table'] ?? null;
        $record = $payload['record'] ?? null;
        $type = $payload['type'] ?? null; // INSERT, UPDATE, DELETE
        
        if (!$table || !$record || !$type) {
            throw new InvalidArgumentException('Invalid webhook payload');
        }
        
        try {
            switch ($type) {
                case 'INSERT':
                    $this->insertToMySQL($table, $record);
                    break;
                case 'UPDATE':
                    $this->updateToMySQL($table, $record);
                    break;
                case 'DELETE':
                    $this->deleteFromMySQL($table, $record['id']);
                    break;
            }
            
            $this->log('WEBHOOK_SYNC', "Synced $type on $table from Supabase");
            return ['status' => 'success'];
            
        } catch (Exception $e) {
            $this->log('WEBHOOK_ERROR', "Error syncing from Supabase: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Upsert dans Supabase
     */
    private function upsertToSupabase(string $table, array $row): void
    {
        $existing = $this->supabase->fetch(
            "SELECT * FROM {$table} WHERE id = :id LIMIT 1",
            ['id' => $row['id']]
        );
        
        if ($existing) {
            // UPDATE
            $set = [];
            foreach ($row as $key => $value) {
                if ($key !== 'id') {
                    $set[] = "`$key` = :$key";
                }
            }
            $sql = "UPDATE {$table} SET " . implode(', ', $set) . " WHERE id = :id";
            $this->supabase->execute($sql, $row);
        } else {
            // INSERT
            $columns = implode(', ', array_map(fn($k) => "`$k`", array_keys($row)));
            $placeholders = implode(', ', array_map(fn($k) => ":$k", array_keys($row)));
            $sql = "INSERT INTO {$table} ($columns) VALUES ($placeholders)";
            $this->supabase->execute($sql, $row);
        }
    }
    
    /**
     * Insert dans MySQL depuis Supabase
     */
    private function insertToMySQL(string $table, array $record): void
    {
        $columns = implode(', ', array_map(fn($k) => "`$k`", array_keys($record)));
        $placeholders = implode(', ', array_map(fn($k) => ":$k", array_keys($record)));
        $sql = "INSERT INTO {$table} ($columns) VALUES ($placeholders)";
        $this->mysql->execute($sql, $record);
    }
    
    /**
     * Update dans MySQL depuis Supabase
     */
    private function updateToMySQL(string $table, array $record): void
    {
        $set = [];
        foreach ($record as $key => $value) {
            if ($key !== 'id') {
                $set[] = "`$key` = :$key";
            }
        }
        $sql = "UPDATE {$table} SET " . implode(', ', $set) . " WHERE id = :id";
        $this->mysql->execute($sql, $record);
    }
    
    /**
     * Delete dans MySQL depuis Supabase
     */
    private function deleteFromMySQL(string $table, int $id): void
    {
        $this->mysql->execute("DELETE FROM {$table} WHERE id = :id", ['id' => $id]);
    }
    
    /**
     * Récupère le dernier timestamp de sync pour une table
     */
    private function getLastSyncTime(string $table): ?string
    {
        $syncFile = __DIR__ . '/../logs/sync_' . $table . '.json';
        if (!file_exists($syncFile)) {
            return null;
        }
        
        $data = json_decode(file_get_contents($syncFile), true);
        return $data['last_sync'] ?? null;
    }
    
    /**
     * Met à jour le timestamp de sync pour une table
     */
    private function updateLastSyncTime(string $table): void
    {
        $syncFile = __DIR__ . '/../logs/sync_' . $table . '.json';
        $data = [
            'last_sync' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        file_put_contents($syncFile, json_encode($data, JSON_PRETTY_PRINT));
    }
    
    /**
     * Crée le répertoire de logs si nécessaire
     */
    private function ensureLogDirectory(): void
    {
        $logDir = dirname($this->logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
    }
    
    /**
     * Écrit un log
     */
    private function log(string $level, string $message): void
    {
        $entry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'level' => $level,
            'message' => $message
        ];
        
        file_put_contents(
            $this->logFile,
            json_encode($entry) . "\n",
            FILE_APPEND
        );
    }
    
    /**
     * Vérifie l'intégrité des données entre MySQL et Supabase
     */
    public function verifyIntegrity(): array
    {
        $results = [];
        
        foreach ($this->syncTables as $table => $timestampColumn) {
            $mysqlCount = $this->mysql->fetch("SELECT COUNT(*) as total FROM {$table}")['total'] ?? 0;
            $supabaseCount = $this->supabase->fetch("SELECT COUNT(*) as total FROM {$table}")['total'] ?? 0;
            
            $results[$table] = [
                'mysql_count' => $mysqlCount,
                'supabase_count' => $supabaseCount,
                'match' => $mysqlCount === $supabaseCount,
                'difference' => abs($mysqlCount - $supabaseCount)
            ];
        }
        
        return $results;
    }
}
