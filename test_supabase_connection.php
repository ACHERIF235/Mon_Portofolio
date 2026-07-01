<?php
/**
 * Script de test de connexion à Supabase
 * Exécutez ce fichier pour vérifier que la connexion fonctionne
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/core/DatabaseInterface.php';
require_once __DIR__ . '/core/SupabaseDatabase.php';

$config = require __DIR__ . '/config.php';

echo "=== Test de connexion Supabase ===\n\n";

try {
    $db = new SupabaseDatabase(
        $config['supabase_url'],
        $config['supabase_key'],
        $config['supabase_auth_token']
    );
    
    echo "✓ Instance Supabase créée\n";
    echo "  URL: " . $config['supabase_url'] . "\n\n";
    
    // Test simple: essayer de récupérer les settings
    echo "Test de lecture (SELECT)...\n";
    try {
        $settings = $db->fetchAll('SELECT * FROM settings LIMIT 5');
        echo "✓ Lecture réussie: " . count($settings) . " settings récupérés\n";
        if (!empty($settings)) {
            foreach ($settings as $setting) {
                echo "  - {$setting['key']}: {$setting['value']}\n";
            }
        }
    } catch (Exception $e) {
        echo "✗ Lecture échouée: " . $e->getMessage() . "\n";
        echo "  Note: Les tables n'existent peut-être pas encore. Exécutez init_postgresql.sql dans Supabase.\n";
    }
    
    echo "\n=== Test terminé ===\n";
    
} catch (Exception $e) {
    echo "✗ Erreur de connexion: " . $e->getMessage() . "\n";
    echo "\nVérifiez:\n";
    echo "1. L'URL Supabase est correcte\n";
    echo "2. La clé API est valide\n";
    echo "3. Le projet Supabase est actif\n";
    exit(1);
}
