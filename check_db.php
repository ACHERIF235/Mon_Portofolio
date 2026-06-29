<?php
$config = require 'config.php';
$pdo = new PDO('mysql:host='.$config['db_host'].';dbname='.$config['db_name'], $config['db_user'], $config['db_pass']);
$stmt = $pdo->query('DESCRIBE inbox_messages');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
