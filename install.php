<?php
$config = require_once __DIR__ . '/config.php';

try {
    $pdo = new PDO(
        'mysql:host=' . $config['db_host'] . ';charset=utf8mb4',
        $config['db_user'],
        $config['db_pass'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    $pdo->exec('CREATE DATABASE IF NOT EXISTS `' . $config['db_name'] . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    echo "Base de données '{$config['db_name']}' créée ou déjà existante.\n";
} catch (PDOException $e) {
    die('Impossible de créer la base de données : ' . $e->getMessage());
}

require_once __DIR__ . '/core/db.php';

$sql = file_get_contents(__DIR__ . '/data/init.sql');
if ($sql === false) {
    die('Impossible de lire data/init.sql');
}

db()->exec($sql);

$adminPassword = password_hash('acherif235@', PASSWORD_DEFAULT);
$existingAdmin = db_fetch('SELECT id FROM admin_users WHERE email = :email', ['email' => 'alifa.acherif1@ugb.edu.sn']);
if (!$existingAdmin) {
    db_query('INSERT INTO admin_users (email, password_hash, name) VALUES (:email, :hash, :name)', [
        'email' => 'alifa.acherif1@ugb.edu.sn',
        'hash' => $adminPassword,
        'name' => 'Admin Portfolio'
    ]);
    echo "Compte administrateur créé : alifa.acherif1@ugb.edu.sn / acherif235@\n";
} else {
    echo "Compte administrateur déjà existant.\n";
}

echo "Installation terminée. Tu peux maintenant ouvrir index.php et admin/login.php.\n";
