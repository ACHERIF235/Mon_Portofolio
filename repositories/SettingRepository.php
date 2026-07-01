<?php
require_once __DIR__ . '/BaseRepository.php';

/**
 * Repository pour la table settings
 * Remplace SettingModel avec une abstraction de base de données
 */
class SettingRepository extends BaseRepository
{
    protected string $table = 'settings';
    protected string $primaryKey = 'id';

    /**
     * Récupère une valeur de configuration par sa clé
     */
    public function getByKey(string $key, $default = null): ?string
    {
        $result = $this->queryOne(
            "SELECT value FROM {$this->table} WHERE `key` = :key LIMIT 1",
            ['key' => $key]
        );
        return $result['value'] ?? $default;
    }

    /**
     * Récupère toutes les configurations sous forme de tableau associatif
     */
    public function getAllAsArray(): array
    {
        $rows = $this->findAll('`key` ASC');
        $settings = [];
        foreach ($rows as $row) {
            $settings[$row['key']] = $row['value'];
        }
        return $settings;
    }

    /**
     * Sauvegarde ou met à jour une configuration
     */
    public function save(string $key, string $value): bool
    {
        $existing = $this->findOneBy(['key' => $key]);
        
        if ($existing) {
            return $this->update($existing['id'], ['value' => $value]);
        }
        
        return $this->create(['key' => $key, 'value' => $value]);
    }

    /**
     * Sauvegarde plusieurs configurations en une transaction
     */
    public function saveBatch(array $settings): bool
    {
        $this->db->beginTransaction();
        
        try {
            foreach ($settings as $key => $value) {
                if (!$this->save($key, $value)) {
                    throw new RuntimeException("Failed to save setting: $key");
                }
            }
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Settings batch save failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Supprime une configuration
     */
    public function deleteByKey(string $key): bool
    {
        $existing = $this->findOneBy(['key' => $key]);
        if ($existing) {
            return $this->delete($existing['id']);
        }
        return false;
    }
}
