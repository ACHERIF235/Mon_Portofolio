<?php
/**
 * Configuration exemple pour Supabase
 * Copiez ce fichier en config.php et adaptez les valeurs
 */

return [
    // === CONFIGURATION SUPABASE ===
    'db_type' => 'supabase', // 'mysql' ou 'supabase'
    
    // URL de votre projet Supabase
    'supabase_url' => 'https://your-project.supabase.co',
    
    // Clé API (anon/public key)
    'supabase_key' => 'your-anon-key-here',
    
    // Token d'authentification (optionnel, pour les opérations admin)
    'supabase_auth_token' => 'your-service-role-key-here',
    
    // === CONFIGURATION LÉGACY (GARDÉE POUR ROLLBACK) ===
    'db_host' => '127.0.0.1',
    'db_name' => 'portfolio',
    'db_user' => 'root',
    'db_pass' => '',
    
    // === CONFIGURATION COMMUNE ===
    'uploads_dir' => __DIR__ . '/assets/uploads',
    'allowed_image_types' => ['image/jpeg', 'image/png', 'image/webp'],
    'allowed_doc_types' => ['application/pdf'],
];
