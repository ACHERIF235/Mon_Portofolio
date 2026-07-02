<?php
/**
 * Script de configuration Supabase via API REST
 * Teste la connexion et configure le mot de passe admin
 * Les tables doivent être créées manuellement dans le dashboard
 */

require_once __DIR__ . '/config.php';

$config = require __DIR__ . '/config.php';

echo "=== Configuration Supabase (Mode REST API) ===\n\n";

$sqlFile = __DIR__ . '/data/init_postgresql.sql';

echo "⚠ XAMPP n'a pas le driver PostgreSQL par défaut.\n";
echo "⚠ Les tables doivent être créées manuellement dans le dashboard Supabase.\n\n";

echo "=== INSTRUCTIONS RAPIDES ===\n\n";
echo "1. Ouvrez: https://supabase.com/dashboard/project/apfqtfkizqsoeujhtzxe\n";
echo "2. Dans la barre latérale gauche, cliquez sur:\n";
echo "   - 'SQL Editor' (icône de terminal) OU\n";
echo "   - 'Database' → 'SQL Editor'\n";
echo "3. Cliquez sur 'New query' ou le bouton '+'\n";
echo "4. Copiez tout le contenu de ce fichier:\n";
echo "   $sqlFile\n";
echo "5. Collez dans l'éditeur et cliquez sur 'Run' (ou ▶)\n";
echo "6. Attendez que toutes les tables soient créées (succès vert)\n\n";

echo "Appuyez sur ENTER une fois terminé...";
$line = trim(fgets(STDIN));

// Étape 1: Tester la connexion via SupabaseDatabase
echo "\nÉtape 1: Test de connexion Supabase...\n";

require_once __DIR__ . '/core/DatabaseInterface.php';
require_once __DIR__ . '/core/SupabaseDatabase.php';

try {
    $db = new SupabaseDatabase(
        $config['supabase_url'],
        $config['supabase_key'],
        $config['supabase_auth_token']
    );
    
    $settings = $db->fetchAll('SELECT * FROM settings LIMIT 5');
    echo "✓ Connexion réussie: " . count($settings) . " settings récupérés\n";
    
    if (!empty($settings)) {
        echo "  Exemples:\n";
        foreach (array_slice($settings, 0, 3) as $setting) {
            echo "  - {$setting['key']}: " . substr($setting['value'], 0, 50) . "...\n";
        }
    }
    
} catch (Exception $e) {
    echo "✗ Erreur de connexion: " . $e->getMessage() . "\n";
    echo "Assurez-vous d'avoir exécuté le SQL dans le dashboard.\n";
    exit(1);
}

echo "\n";

// Étape 2: Mettre à jour le mot de passe admin
echo "Étape 2: Configuration du mot de passe admin...\n";

$adminPassword = password_hash('acherif235@', PASSWORD_DEFAULT);

try {
    // Vérifier si l'admin existe
    $existingAdmin = $db->fetch(
        "SELECT id FROM admin_users WHERE email = :email LIMIT 1",
        ['email' => 'alifa.acherif1@ugb.edu.sn']
    );
    
    if ($existingAdmin) {
        // Mettre à jour le mot de passe
        $db->execute(
            "UPDATE admin_users SET password_hash = :hash WHERE email = :email",
            ['hash' => $adminPassword, 'email' => 'alifa.acherif1@ugb.edu.sn']
        );
        echo "✓ Mot de passe admin mis à jour\n";
    } else {
        // Créer l'admin
        $db->execute(
            "INSERT INTO admin_users (email, password_hash, name) VALUES (:email, :hash, :name)",
            [
                'email' => 'alifa.acherif1@ugb.edu.sn',
                'hash' => $adminPassword,
                'name' => 'Admin Portfolio'
            ]
        );
        echo "✓ Compte admin créé\n";
    }
    
} catch (Exception $e) {
    echo "✗ Erreur configuration admin: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n=== Configuration terminée ===\n";
echo "\n✓ DB_TYPE est déjà configuré sur 'supabase' dans config.php\n";
echo "\nIdentifiants admin:\n";
echo "Email: alifa.acherif1@ugb.edu.sn\n";
echo "Mot de passe: acherif235@\n";
echo "\nVous pouvez maintenant tester l'application!\n";
