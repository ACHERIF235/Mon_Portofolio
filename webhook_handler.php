<?php
/**
 * Handler pour les webhooks Supabase
 * Appelé par Supabase lors des modifications de la base
 * Permet de synchroniser Supabase → MySQL
 */

require_once __DIR__ . '/core/SyncService.php';

// Vérifier la méthode HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Récupérer le payload
$payload = json_decode(file_get_contents('php://input'), true);

if (!$payload) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid payload']);
    exit;
}

// Vérifier la signature Supabase (optionnel, recommandé en production)
$signature = $_SERVER['HTTP_SUPABASE_SIGNATURE'] ?? '';
if ($signature && !verifySupabaseSignature($signature, $payload)) {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid signature']);
    exit;
}

try {
    $syncService = new SyncService();
    $result = $syncService->syncFromSupabase($payload);
    
    http_response_code(200);
    echo json_encode(['status' => 'success', 'data' => $result]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
    
    // Log l'erreur
    error_log("Webhook error: " . $e->getMessage());
}

/**
 * Vérifie la signature du webhook Supabase
 * À implémenter avec votre secret key
 */
function verifySupabaseSignature(string $signature, array $payload): bool
{
    // En production, vérifier la signature HMAC
    // $secret = getenv('SUPABASE_WEBHOOK_SECRET');
    // $expected = hash_hmac('sha256', json_encode($payload), $secret);
    // return hash_equals($expected, $signature);
    
    // Pour le développement, accepter tout
    return true;
}
