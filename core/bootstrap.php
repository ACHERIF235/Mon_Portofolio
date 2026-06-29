<?php
$config = require __DIR__ . '/../config.php';

ini_set('session.cookie_httponly', '1');
ini_set('session.use_strict_mode', '1');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/View.php';
require_once __DIR__ . '/Security.php';
