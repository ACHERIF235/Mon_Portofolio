<?php
require_once __DIR__ . '/DatabaseInterface.php';
require_once __DIR__ . '/MySQLDatabase.php';
require_once __DIR__ . '/SupabaseDatabase.php';

/**
 * Factory pour créer l'instance de base de données appropriée
 * Permet de basculer entre MySQL et Supabase via configuration
 */
class DatabaseFactory
{
    private static ?DatabaseInterface $instance = null;

    /**
     * Retourne l'instance de base de données configurée
     * Utilise le fichier config.php pour déterminer le type
     */
    public static function getInstance(): DatabaseInterface
    {
        if (self::$instance === null) {
            $config = require __DIR__ . '/../config.php';
            
            // Détection du type de DB via config ou variable d'environnement
            $dbType = getenv('DB_TYPE') ?: $config['db_type'] ?: 'mysql';
            
            switch ($dbType) {
                case 'supabase':
                    $url = getenv('SUPABASE_URL') ?: $config['supabase_url'] ?: '';
                    $key = getenv('SUPABASE_KEY') ?: $config['supabase_key'] ?: '';
                    $authToken = getenv('SUPABASE_AUTH_TOKEN') ?: $config['supabase_auth_token'] ?: null;
                    
                    self::$instance = new SupabaseDatabase($url, $key, $authToken);
                    break;
                    
                case 'mysql':
                default:
                    self::$instance = new MySQLDatabase(
                        $config['db_host'],
                        $config['db_name'],
                        $config['db_user'],
                        $config['db_pass']
                    );
                    break;
            }
        }
        
        return self::$instance;
    }

    /**
     * Réinitialise l'instance (utile pour les tests)
     */
    public static function reset(): void
    {
        self::$instance = null;
    }
}
