<?php
/**
 * EXEMPLE DE MIGRATION: SettingModel → SettingRepository
 * 
 * Ce fichier montre comment migrer progressivement du code legacy vers
 * la nouvelle architecture avec Repository Pattern.
 */

// ============================================================================
// 1. CODE ORIGINAL (Legacy) - models/SettingModel.php
// ============================================================================
/*
class SettingModel
{
    public static function get(string $key, $default = null)
    {
        $row = db_fetch('SELECT value FROM settings WHERE `key` = :key LIMIT 1', ['key' => $key]);
        return $row['value'] ?? $default;
    }

    public static function all(): array
    {
        $rows = db_fetchAll('SELECT `key`, `value` FROM settings');
        $settings = [];
        foreach ($rows as $row) {
            $settings[$row['key']] = $row['value'];
        }
        return $settings;
    }

    public static function save(string $key, string $value): void
    {
        if (db_fetch('SELECT 1 FROM settings WHERE `key` = :key', ['key' => $key])) {
            db_query('UPDATE settings SET value = :value WHERE `key` = :key', ['value' => $value, 'key' => $key]);
        } else {
            db_query('INSERT INTO settings (`key`, `value`) VALUES (:key, :value)', ['key' => $key, 'value' => $value]);
        }
    }
}
*/

// ============================================================================
// 2. NOUVEAU CODE (Repository) - repositories/SettingRepository.php
// ============================================================================
require_once __DIR__ . '/../repositories/SettingRepository.php';

class SettingModel
{
    private static ?SettingRepository $repository = null;

    /**
     * Initialise le repository (lazy loading)
     */
    private static function getRepository(): SettingRepository
    {
        if (self::$repository === null) {
            self::$repository = new SettingRepository();
        }
        return self::$repository;
    }

    /**
     * Récupère une valeur de configuration
     * MIGRATION: Utilise le repository au lieu de db_fetch direct
     */
    public static function get(string $key, $default = null)
    {
        return self::getRepository()->getByKey($key, $default);
    }

    /**
     * Récupère toutes les configurations
     * MIGRATION: Utilise le repository au lieu de db_fetchAll direct
     */
    public static function all(): array
    {
        return self::getRepository()->getAllAsArray();
    }

    /**
     * Sauvegarde une configuration
     * MIGRATION: Utilise le repository au lieu de db_query direct
     */
    public static function save(string $key, string $value): void
    {
        self::getRepository()->save($key, $value);
    }

    /**
     * NOUVELLE MÉTHODE: Sauvegarde en batch (transaction)
     * Avantage: Une seule transaction pour plusieurs updates
     */
    public static function saveBatch(array $settings): bool
    {
        return self::getRepository()->saveBatch($settings);
    }
}

// ============================================================================
// 3. EXEMPLE D'UTILISATION DANS UN CONTROLLER
// ============================================================================
/*
// AVANT (Legacy):
class AdminController
{
    public static function settings(): void
    {
        $settings = SettingModel::all();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            foreach ($fields as $field) {
                SettingModel::save($field, sanitize_text($_POST[$field] ?? ''));
            }
            // Chaque save = une requête SQL séparée
        }
    }
}

// APRÈS (Optimisé):
class AdminController
{
    public static function settings(): void
    {
        $settings = SettingModel::all();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $batch = [];
            foreach ($fields as $field) {
                $batch[$field] = sanitize_text($_POST[$field] ?? '');
            }
            // Une seule transaction pour tous les updates
            SettingModel::saveBatch($batch);
        }
    }
}
*/

// ============================================================================
// 4. EXEMPLE DE MIGRATION PROGRESSIVE (Strangler Pattern)
// ============================================================================
/*
// ÉTAPE 1: Garder le Model comme wrapper (comme ci-dessus)
// - Les contrôleurs existants continuent de fonctionner
// - Le Model délègue au Repository
// - Aucun changement dans les contrôleurs

// ÉTAPE 2: Migrer les contrôleurs un par un
class AdminController
{
    private static ?SettingRepository $settingRepo = null;
    
    private static function getSettingRepo(): SettingRepository
    {
        if (self::$settingRepo === null) {
            self::$settingRepo = new SettingRepository();
        }
        return self::$settingRepo;
    }
    
    public static function settings(): void
    {
        // MIGRATION: Utiliser directement le repository
        $settings = self::getSettingRepo()->getAllAsArray();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $batch = [];
            foreach ($fields as $field) {
                $batch[$field] = sanitize_text($_POST[$field] ?? '');
            }
            self::getSettingRepo()->saveBatch($batch);
        }
        
        View::render('admin/settings', ['settings' => $settings]);
    }
}

// ÉTAPE 3: Supprimer les Models legacy une fois tous les contrôleurs migrés
// - Supprimer models/SettingModel.php
// - Les contrôleurs utilisent directement les repositories
*/

// ============================================================================
// 5. TEST DE COMPATIBILITÉ
// ============================================================================
echo "=== Test de compatibilité SettingModel ===\n";

// Test avec l'ancien code (doit fonctionner)
$settings = SettingModel::all();
echo "Nombre de settings: " . count($settings) . "\n";

// Test avec la nouvelle méthode batch
$testBatch = [
    'test_key_1' => 'value_1',
    'test_key_2' => 'value_2'
];
$result = SettingModel::saveBatch($testBatch);
echo "Batch save result: " . ($result ? 'SUCCESS' : 'FAILED') . "\n";

// Nettoyage
SettingModel::save('test_key_1', '');
SettingModel::save('test_key_2', '');

echo "=== Test terminé ===\n";
