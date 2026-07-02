<?php
// Proxy script for Vercel
$file = $_GET['file'] ?? 'index.php';

// Prevent directory traversal attacks
$file = str_replace(['../', '..\\'], '', $file);

$path = __DIR__ . '/../' . $file;

// Define specific variables that might be needed by scripts
$_SERVER['SCRIPT_NAME'] = '/' . $file;
$_SERVER['PHP_SELF'] = '/' . $file;

if (file_exists($path) && is_file($path) && pathinfo($path, PATHINFO_EXTENSION) === 'php') {
    require $path;
} else {
    // Fallback to index
    require __DIR__ . '/../index.php';
}
