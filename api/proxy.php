<?php
// Proxy script for Vercel
$file = $_GET['file'] ?? 'index.php';

// Prevent directory traversal attacks
$file = str_replace(['../', '..\\'], '', $file);

$path = __DIR__ . '/../' . $file;
$defaultPath = __DIR__ . '/../index.php';

// Define specific variables that might be needed by scripts
$_SERVER['SCRIPT_NAME'] = '/' . $file;
$_SERVER['PHP_SELF'] = '/' . $file;

if (file_exists($path) && is_file($path) && pathinfo($path, PATHINFO_EXTENSION) === 'php') {
    require $path;
} elseif (file_exists($defaultPath) && is_file($defaultPath)) {
    require $defaultPath;
} else {
    http_response_code(404);
    echo 'Entry point not found.';
}
