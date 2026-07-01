<?php
/**
 * Script de configuration Supabase
 * Guide pour l'exécution manuelle du SQL, puis teste la connexion et configure le mot de passe admin
 */

require_once __DIR__ . '/config.php';

$config = require __DIR__ . '/config.php';

echo "=== Configuration Supabase ===\n\n";

$sqlFile = __DIR__ . '/data/init_postgresql.sql';

echo "⚠ Supabase ne permet pas de créer des tables via l'API REST.\n";
echo "Vous devez exécuter le SQL manuellement dans le dashboard.\n\n";

echo "Instructions:\n";
echo "1. Allez sur https://supabase.com/dashboard\n";
echo "2. Sélectionnez votre projet: apfqtfkizqsoeujhtzxe\n";
echo "3. Cliquez sur 'SQL Editor' dans la barre latérale\n";
echo "4. Cliquez sur 'New Query'\n";
echo "5. Copiez le contenu du fichier: $sqlFile\n";
echo "6. Collez le SQL et cliquez sur 'Run'\n";
echo "7. Attendez que toutes les tables soient créées\n\n";

echo "Fichier SQL à copier: $sqlFile\n\n";

echo "Appuyez sur ENTER une fois que vous avez exécuté le SQL dans Supabase...";
$line = trim(fgets(STDIN));

// Étape 2: Tester la connexion
echo "\nÉtape 2: Test de connexion...\n";

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
    echo "Assurez-vous d'avoir exécuté le SQL dans le dashboard Supabase.\n";
    exit(1);
}

echo "\n";

// Étape 3: Mettre à jour le mot de passe admin
echo "Étape 3: Configuration du mot de passe admin...\n";

$adminPassword = password_hash('Admin123!', PASSWORD_DEFAULT);

try {
    // Vérifier si l'admin existe
    $existingAdmin = $db->fetch(
        "SELECT id FROM admin_users WHERE email = :email LIMIT 1",
        ['email' => 'admin@portfolio.local']
    );
    
    if ($existingAdmin) {
        // Mettre à jour le mot de passe
        $db->execute(
            "UPDATE admin_users SET password_hash = :hash WHERE email = :email",
            ['hash' => $adminPassword, 'email' => 'admin@portfolio.local']
        );
        echo "✓ Mot de passe admin mis à jour\n";
    } else {
        // Créer l'admin
        $db->execute(
            "INSERT INTO admin_users (email, password_hash, name) VALUES (:email, :hash, :name)",
            [
                'email' => 'admin@portfolio.local',
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

echo "\n=== Configuration terminée avec succès ===\n";
echo "\nIdentifiants admin:\n";
echo "Email: admin@portfolio.local\n";
echo "Mot de passe: Admin123!\n";
echo "\nProchaine étape:\n";
echo "1. Changez DB_TYPE=supabase dans config.php ou utilisez la variable d'environnement\n";
echo "2. Testez l'application\n";
