<?php
$config = require 'config.php';
$pdo = new PDO('mysql:host='.$config['db_host'].';dbname='.$config['db_name'], $config['db_user'], $config['db_pass']);
$pdo->exec('ALTER TABLE inbox_messages ADD COLUMN is_replied TINYINT(1) DEFAULT 0 AFTER is_read');
echo "Column is_replied added.\n";
