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

    private static function resolveConfigValue(array $envNames, array $serverNames, array $envVarNames, mixed $default = null): mixed
    {
        foreach ($envNames as $name) {
            $value = getenv($name);
            if ($value !== false && $value !== '') {
                return $value;
            }
        }

        foreach ($serverNames as $name) {
            $value = $_SERVER[$name] ?? null;
            if ($value !== null && $value !== '') {
                return $value;
            }
        }

        foreach ($envVarNames as $name) {
            $value = $_ENV[$name] ?? null;
            if ($value !== null && $value !== '') {
                return $value;
            }
        }

        return $default;
    }

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
                    
                    $key = self::resolveConfigValue(
                        ['SUPABASE_KEY', 'SUPABASE_ANON_KEY', 'SUPABASE_PUBLISHABLE_KEY', 'NEXT_PUBLIC_SUPABASE_ANON_KEY', 'SUPABASE_API_KEY', 'CLÉ_PUBLISHABLE_SUPABASE', 'CLE_PUBLISHABLE_SUPABASE'],
                        ['SUPABASE_KEY', 'SUPABASE_ANON_KEY', 'SUPABASE_PUBLISHABLE_KEY', 'NEXT_PUBLIC_SUPABASE_ANON_KEY', 'SUPABASE_API_KEY', 'CLÉ_PUBLISHABLE_SUPABASE', 'CLE_PUBLISHABLE_SUPABASE'],
                        ['SUPABASE_KEY', 'SUPABASE_ANON_KEY', 'SUPABASE_PUBLISHABLE_KEY', 'NEXT_PUBLIC_SUPABASE_ANON_KEY', 'SUPABASE_API_KEY', 'CLÉ_PUBLISHABLE_SUPABASE', 'CLE_PUBLISHABLE_SUPABASE'],
                        $config['supabase_key'] ?? ''
                    );
                    
                    $authToken = self::resolveConfigValue(
                        ['SUPABASE_AUTH_TOKEN', 'SUPABASE_SERVICE_ROLE_KEY', 'SUPABASE_SERVICE_ROLE', 'CLÉ_SECRET_SUPABASE', 'CLE_SECRET_SUPABASE'],
                        ['SUPABASE_AUTH_TOKEN', 'SUPABASE_SERVICE_ROLE_KEY', 'SUPABASE_SERVICE_ROLE', 'CLÉ_SECRET_SUPABASE', 'CLE_SECRET_SUPABASE'],
                        ['SUPABASE_AUTH_TOKEN', 'SUPABASE_SERVICE_ROLE_KEY', 'SUPABASE_SERVICE_ROLE', 'CLÉ_SECRET_SUPABASE', 'CLE_SECRET_SUPABASE'],
                        $config['supabase_auth_token'] ?? null
                    );
                    
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
