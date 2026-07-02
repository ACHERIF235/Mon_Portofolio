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
                    // URL
                    $url = getenv('SUPABASE_URL') ?: (getenv('URL_SUPABASE') ?: ($_SERVER['SUPABASE_URL'] ?? ($_SERVER['URL_SUPABASE'] ?? ($_ENV['SUPABASE_URL'] ?? ($_ENV['URL_SUPABASE'] ?? ($config['supabase_url'] ?? ''))))));
                    
                    // Key
                    $key = getenv('SUPABASE_KEY') ?: (getenv('SUPABASE_API_KEY') ?: (getenv('CLÉ_PUBLISHABLE_SUPABASE') ?: (getenv('CLE_PUBLISHABLE_SUPABASE') ?: ($_SERVER['SUPABASE_KEY'] ?? ($_SERVER['SUPABASE_API_KEY'] ?? ($_SERVER['CLÉ_PUBLISHABLE_SUPABASE'] ?? ($_SERVER['CLE_PUBLISHABLE_SUPABASE'] ?? ($_ENV['SUPABASE_KEY'] ?? ($_ENV['SUPABASE_API_KEY'] ?? ($_ENV['CLÉ_PUBLISHABLE_SUPABASE'] ?? ($_ENV['CLE_PUBLISHABLE_SUPABASE'] ?? ($config['supabase_key'] ?? ''))))))))))));
                    
                    // Auth Token
                    $authToken = getenv('SUPABASE_AUTH_TOKEN') ?: (getenv('CLÉ_SECRET_SUPABASE') ?: (getenv('CLE_SECRET_SUPABASE') ?: ($_SERVER['SUPABASE_AUTH_TOKEN'] ?? ($_SERVER['CLÉ_SECRET_SUPABASE'] ?? ($_SERVER['CLE_SECRET_SUPABASE'] ?? ($_ENV['SUPABASE_AUTH_TOKEN'] ?? ($_ENV['CLÉ_SECRET_SUPABASE'] ?? ($_ENV['CLE_SECRET_SUPABASE'] ?? ($config['supabase_auth_token'] ?? null)))))))));
                    
                    if (empty($url) || empty($key)) {
                        throw new RuntimeException("Configuration Supabase manquante dans Vercel. URL trouvée: '" . ($url ? 'Oui' : 'Non') . "', Clé trouvée: '" . ($key ? 'Oui' : 'Non') . "'. Veuillez vérifier les 'Environment Variables' dans Vercel et REDÉPLOYER.");
                    }
                    
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
