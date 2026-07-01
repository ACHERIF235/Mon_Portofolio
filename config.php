<?php
return [
    // === TYPE DE BASE DE DONNÉES ===
    // 'mysql' pour l'ancienne base (local)
    // 'supabase' pour la nouvelle base (cloud)
    'db_type' => getenv('DB_TYPE') ?: '',
    
    // === CONFIGURATION SUPABASE ===
    'supabase_url' => getenv('SUPABASE_URL') ?: '',
    'supabase_key' => getenv('SUPABASE_KEY') ?: '',
    'supabase_auth_token' => getenv('SUPABASE_AUTH_TOKEN') ?: '',
    
    // === CONFIGURATION MYSQL (LEGACY - GARDÉE POUR ROLLBACK) ===
    'db_host' => '127.0.0.1',
    'db_name' => 'portfolio',
    'db_user' => 'root',
    'db_pass' => '',
    
    // === CONFIGURATION COMMUNE ===
    'uploads_dir' => __DIR__ . '/assets/uploads',
    'allowed_image_types' => ['image/jpeg', 'image/png', 'image/webp'],
    'allowed_doc_types' => ['application/pdf'],
];
