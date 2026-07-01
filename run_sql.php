<?php
$pgHost = 'db.apfqtfkizqsoeujhtzxe.supabase.co';
$pgPort = 5432;
$pgDb = 'postgres';
$pgUser = 'postgres';
$pgPassword = '1JoipsQRIsxocklJ';

$dsn = "pgsql:host=$pgHost;port=$pgPort;dbname=$pgDb;options='--client_encoding=UTF8'";

try {
    $pdo = new PDO($dsn, $pgUser, $pgPassword, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    $sqlFile = __DIR__ . '/data/init_postgresql.sql';
    $sql = file_get_contents($sqlFile);
    
    // Remove SET session_replication_role as it requires superuser privileges
    $sql = str_replace("SET session_replication_role = 'replica';", "", $sql);
    $sql = str_replace("SET session_replication_role = 'DEFAULT';", "", $sql);

    $pdo->exec($sql);
    echo "SQL Executed Successfully!\n";
    
    $tables = ['settings', 'admin_users', 'skills', 'projects', 'education', 'associations', 'languages', 'contacts', 'inbox_messages'];
    foreach ($tables as $table) {
        $result = $pdo->query("SELECT COUNT(*) as count FROM $table")->fetch();
        echo "Table $table: {$result['count']} lignes\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
