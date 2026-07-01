<?php
$config = require __DIR__ . '/config.php';
$url = $config['supabase_url'] . "/rest/v1/settings";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
echo "Error: " . curl_error($ch) . "\n";
