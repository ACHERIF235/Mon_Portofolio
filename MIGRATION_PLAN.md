# Plan de Migration PHP MVC → Architecture Serverless (Vercel + Supabase)

## 1. Architecture Actuelle

```
┌─────────────────────────────────────────────────────────┐
│                    Architecture Actuelle                 │
├─────────────────────────────────────────────────────────┤
│                                                         │
│  ┌──────────────┐      ┌──────────────┐               │
│  │   Views      │◄─────│ Controllers  │               │
│  │  (PHP/HTML)  │      │   (PHP)      │               │
│  └──────────────┘      └──────┬───────┘               │
│                                │                        │
│                         ┌──────▼───────┐               │
│                         │    Models    │               │
│                         │   (PHP)      │               │
│                         └──────┬───────┘               │
│                                │                        │
│                         ┌──────▼───────┐               │
│                         │  core/db.php │               │
│                         │   (PDO)      │               │
│                         └──────┬───────┘               │
│                                │                        │
│                         ┌──────▼───────┐               │
│                         │    MySQL     │               │
│                         │  (Local)     │               │
│                         └──────────────┘               │
│                                                         │
│  Déploiement: XAMPP (localhost)                        │
│  Session: PHP native                                    │
│  Uploads: Local filesystem                              │
│  Email: PHP mail()                                      │
└─────────────────────────────────────────────────────────┘
```

## 2. Architecture Cible

```
┌─────────────────────────────────────────────────────────┐
│                  Architecture Cible                      │
├─────────────────────────────────────────────────────────┤
│                                                         │
│  ┌──────────────┐      ┌──────────────┐               │
│  │   Frontend    │◄─────│  API Routes  │               │
│  │  (Next.js)    │      │  (PHP/Node)  │               │
│  └──────────────┘      └──────┬───────┘               │
│                                │                        │
│                         ┌──────▼───────┐               │
│                         │ Repositories │               │
│                         │  (PHP/TS)    │               │
│                         └──────┬───────┘               │
│                                │                        │
│                    ┌───────────┴───────────┐            │
│                    │                       │            │
│             ┌──────▼──────┐        ┌──────▼──────┐     │
│             │   MySQL     │        │  Supabase   │     │
│             │  (Legacy)   │        │ PostgreSQL  │     │
│             └─────────────┘        └──────┬──────┘     │
│                                            │            │
│                                     ┌──────▼──────┐   │
│                                     │ Supabase    │   │
│                                     │ Auth        │   │
│                                     └─────────────┘   │
│                                                         │
│  Déploiement: Vercel (serverless)                      │
│  Session: Supabase Auth / JWT                          │
│  Uploads: Supabase Storage                             │
│  Email: Supabase Email / Resend                        │
└─────────────────────────────────────────────────────────┘
```

## 3. Architecture Hybride (Phase de Migration)

```
┌─────────────────────────────────────────────────────────┐
│              Architecture Hybride (Transition)          │
├─────────────────────────────────────────────────────────┤
│                                                         │
│  ┌──────────────┐      ┌──────────────┐               │
│  │   Views      │◄─────│ Controllers  │               │
│  │  (PHP/HTML)  │      │   (PHP)      │               │
│  └──────────────┘      └──────┬───────┘               │
│                                │                        │
│                         ┌──────▼───────┐               │
│                         │ Repositories │               │
│                         │  (Nouveaux)  │               │
│                         └──────┬───────┘               │
│                                │                        │
│                    ┌───────────┴───────────┐            │
│                    │  DatabaseFactory      │            │
│                    │  (Switch MySQL/Supa)  │            │
│                    └───────────┬───────────┘            │
│                                │                        │
│                    ┌───────────┴───────────┐            │
│                    │                       │            │
│             ┌──────▼──────┐        ┌──────▼──────┐     │
│             │   MySQL     │◄──────►│  Supabase   │     │
│             │  (Primary)  │  Sync  │ PostgreSQL  │     │
│             └─────────────┘        └─────────────┘     │
│                                                         │
│  Déploiement: Vercel (PHP Runtime)                     │
│  Session: PHP native (transition)                      │
│  Uploads: Local → Supabase Storage                      │
│  Email: PHP mail() → Supabase Email                      │
└─────────────────────────────────────────────────────────┘
```

## 4. Plan de Migration Étape par Étape

### Phase 1: Préparation (Semaine 1)

**Objectif:** Mettre en place l'infrastructure sans toucher au code existant

- [ ] **1.1 Créer un projet Supabase**
  - Créer un compte sur supabase.com
  - Créer un nouveau projet "portfolio"
  - Noter: URL, anon key, service role key

- [ ] **1.2 Configurer la base PostgreSQL**
  - Convertir le schéma MySQL vers PostgreSQL
  - Créer les tables dans Supabase
  - Adapter les types de données (AUTO_INCREMENT → SERIAL)
  - Créer les indexes nécessaires

- [ ] **1.3 Migrer les données initiales**
  - Exporter les données MySQL
  - Convertir pour PostgreSQL
  - Importer dans Supabase
  - Vérifier l'intégrité des données

- [ ] **1.4 Configurer Supabase Storage**
  - Créer un bucket "uploads"
  - Configurer les politiques RLS (Row Level Security)
  - Migrer les fichiers existants

**Livrables:**
- Base Supabase prête avec toutes les tables
- Données migrées et vérifiées
- Bucket Storage configuré

---

### Phase 2: Abstraction de la Base de Données (Semaine 2)

**Objectif:** Créer la couche d'abstraction sans modifier les contrôleurs

- [ ] **2.1 Implémenter l'interface DatabaseInterface**
  - Créer `core/DatabaseInterface.php`
  - Définir les méthodes: query, fetch, fetchAll, execute, etc.

- [ ] **2.2 Créer MySQLDatabase**
  - Implémenter l'interface avec PDO
  - Garder la compatibilité avec le code existant

- [ ] **2.3 Créer SupabaseDatabase**
  - Implémenter l'interface avec l'API REST Supabase
  - Gérer les différences SQL/REST

- [ ] **2.4 Créer DatabaseFactory**
  - Factory pour choisir MySQL ou Supabase
  - Configuration via `config.php`

- [ ] **2.5 Tests unitaires**
  - Tester MySQLDatabase avec la base actuelle
  - Tester SupabaseDatabase avec la nouvelle base
  - Vérifier que les deux retournent les mêmes résultats

**Livrables:**
- Couche d'abstraction fonctionnelle
- Tests validant les deux implémentations
- Documentation d'utilisation

---

### Phase 3: Création des Repositories (Semaine 3)

**Objectif:** Remplacer progressivement les Models par des Repositories

- [ ] **3.1 Créer BaseRepository**
  - Méthodes CRUD communes
  - Gestion des transactions
  - Méthodes utilitaires

- [ ] **3.2 Migrer SettingModel → SettingRepository**
  - Créer `repositories/SettingRepository.php`
  - Adapter SettingModel pour utiliser le repository
  - Tests de régression

- [ ] **3.3 Migrer AdminUserModel → AdminUserRepository**
  - Créer `repositories/AdminUserRepository.php`
  - Gérer le hash des mots de passe
  - Tests de régression

- [ ] **3.4 Migrer SectionModel → SectionRepository**
  - Créer `repositories/SectionRepository.php`
  - Gérer les sections dynamiques
  - Tests de régression

- [ ] **3.5 Migrer MessageModel → MessageRepository**
  - Créer `repositories/MessageRepository.php`
  - Gérer les messages de contact
  - Tests de régression

- [ ] **3.6 Migrer UploadModel → StorageRepository**
  - Créer `repositories/StorageRepository.php`
  - Support local et Supabase Storage
  - Tests de régression

**Livrables:**
- Tous les repositories créés
- Models adaptés comme wrappers
- Tests de régression passants

---

### Phase 4: Migration des Contrôleurs (Semaine 4-5)

**Objectif:** Migrer les contrôleurs un par un vers les repositories

- [ ] **4.1 Migrer AuthController**
  - Utiliser AdminUserRepository
  - Tester login/logout
  - Vérifier la compatibilité

- [ ] **4.2 Migrer PublicController**
  - Utiliser SettingRepository, SectionRepository, MessageRepository
  - Tester la page publique
  - Tester le formulaire de contact

- [ ] **4.3 Migrer AdminController**
  - Utiliser tous les repositories
  - Tester chaque section admin
  - Vérifier les uploads

- [ ] **4.4 Tests d'intégration**
  - Tester le flux utilisateur complet
  - Vérifier toutes les fonctionnalités
  - Tests de performance

**Livrables:**
- Tous les contrôleurs migrés
- Tests d'intégration passants
- Documentation de migration

---

### Phase 5: Synchronisation MySQL ↔ Supabase (Semaine 6)

**Objectif:** Mettre en place la synchronisation bidirectionnelle

- [ ] **5.1 Créer le service de synchronisation**
  - `core/SyncService.php`
  - Détection des modifications (timestamps)
  - Résolution des conflits

- [ ] **5.2 Implémenter la sync MySQL → Supabase**
  - Trigger sur MySQL ou polling
  - Conversion des données si nécessaire
  - Gestion des erreurs

- [ ] **5.3 Implémenter la sync Supabase → MySQL**
  - Webhooks Supabase
  - Mise à jour de MySQL
  - Gestion des erreurs

- [ ] **5.4 Monitoring et logs**
  - Logs de synchronisation
  - Alertes en cas d'échec
  - Dashboard de monitoring

**Livrables:**
- Service de synchronisation fonctionnel
- Monitoring en place
- Documentation de la sync

---

### Phase 6: Déploiement sur Vercel (Semaine 7)

**Objectif:** Déployer l'application sur Vercel avec PHP

- [ ] **6.1 Préparer le projet pour Vercel**
  - Créer `vercel.json`
  - Configurer le runtime PHP
  - Adapter les chemins absolus

- [ ] **6.2 Configurer les variables d'environnement**
  - DB_TYPE=mysql (initial)
  - Credentials MySQL (si hébergé externe)
  - Credentials Supabase

- [ ] **6.3 Gérer les sessions**
  - Configurer Vercel pour les sessions
  - Ou migrer vers Supabase Auth
  - Tests de session

- [ ] **6.4 Gérer les uploads**
  - Configurer Supabase Storage
  - Adapter le code pour utiliser Storage
  - Tests d'upload

- [ ] **6.5 Déploiement initial**
  - Déployer sur Vercel
  - Tests de smoke
  - Monitoring

**Livrables:**
- Application déployée sur Vercel
- Sessions fonctionnelles
- Uploads vers Supabase

---

### Phase 7: Bascule vers Supabase (Semaine 8)

**Objectif:** Passer de MySQL à Supabase comme base primaire

- [ ] **7.1 Basculer la configuration**
  - Changer DB_TYPE=supabase
  - Redémarrer l'application
  - Tests de régression complets

- [ ] **7.2 Observer pendant 1 semaine**
  - Monitoring des performances
  - Vérification des logs
  - Tests utilisateurs

- [ ] **7.3 Optimiser si nécessaire**
  - Indexes Supabase
  - Cache si nécessaire
  - Optimisations des requêtes

- [ ] **7.4 Arrêter la synchronisation**
  - Une fois stable, arrêter la sync
  - Garder MySQL en backup
  - Nettoyer le code de sync

**Livrables:**
- Application 100% sur Supabase
- MySQL conservé comme backup
- Documentation finale

---

### Phase 8: Optimisations et Modernisation (Semaine 9-10)

**Objectif:** Moderniser l'application pour tirer parti de Supabase

- [ ] **8.1 Migrer l'authentification vers Supabase Auth**
  - Remplacer PHP sessions par Supabase Auth
  - Utiliser JWT tokens
  - Adapter les contrôleurs

- [ ] **8.2 Implémenter le Realtime**
  - Utiliser Supabase Realtime pour les notifications
  - Mises à jour en temps réel
  - WebSocket support

- [ ] **8.3 Optimiser le frontend**
  - Considérer migration vers Next.js
  - Ou optimiser le PHP existant
  - Améliorer les performances

- [ ] **8.4 Nettoyage**
  - Supprimer le code legacy
  - Supprimer les Models wrappers
  - Nettoyer les commentaires

**Livrables:**
- Application modernisée
- Authentification Supabase
- Code nettoyé

---

## 5. Stratégie de Synchronisation MySQL ↔ Supabase

### 5.1 Architecture de Synchronisation

```
┌─────────────────────────────────────────────────────────┐
│           Architecture de Synchronisation               │
├─────────────────────────────────────────────────────────┤
│                                                         │
│  Application                                           │
│       │                                                 │
│       ├──► Écriture MySQL (primaire)                   │
│       │         │                                       │
│       │         └──► SyncService ──► Supabase           │
│       │                                                 │
│       └──► Lecture MySQL (primaire)                    │
│                                                         │
│  Webhook Supabase (optionnel)                          │
│       │                                                 │
│       └──► SyncService ──► MySQL (backup)              │
│                                                         │
│  Monitoring                                             │
│       │                                                 │
│       └──► Logs + Alertes                               │
│                                                         │
└─────────────────────────────────────────────────────────┘
```

### 5.2 Implémentation de la Sync

**Approche 1: Polling (plus simple)**

```php
// core/SyncService.php
class SyncService
{
    private DatabaseInterface $mysql;
    private DatabaseInterface $supabase;
    
    public function syncToSupabase(): array
    {
        $results = [];
        
        // Sync settings
        $results['settings'] = $this->syncTable('settings');
        
        // Sync admin_users
        $results['admin_users'] = $this->syncTable('admin_users');
        
        // ... autres tables
        
        return $results;
    }
    
    private function syncTable(string $table): array
    {
        // Récupérer les données modifiées depuis MySQL
        $mysqlData = $this->mysql->fetchAll(
            "SELECT * FROM $table WHERE updated_at > :last_sync",
            ['last_sync' => $this->getLastSyncTime($table)]
        );
        
        // Envoyer vers Supabase
        foreach ($mysqlData as $row) {
            $this->upsertToSupabase($table, $row);
        }
        
        // Mettre à jour le timestamp de sync
        $this->updateLastSyncTime($table);
        
        return ['synced' => count($mysqlData)];
    }
}
```

**Approche 2: Webhooks Supabase (plus temps réel)**

```php
// webhook_handler.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payload = json_decode(file_get_contents('php://input'), true);
    
    // Vérifier la signature Supabase
    if (!verifySupabaseSignature($_SERVER['HTTP_SUPABASE_SIGNATURE'], $payload)) {
        http_response_code(401);
        exit;
    }
    
    $syncService = new SyncService();
    $syncService->syncFromSupabase($payload);
    
    http_response_code(200);
    echo json_encode(['status' => 'synced']);
}
```

### 5.3 Gestion des Conflits

**Stratégie: Last Write Wins avec log**

```php
private function resolveConflict(string $table, array $mysqlRow, array $supabaseRow): array
{
    // Comparer les timestamps
    $mysqlTime = strtotime($mysqlRow['updated_at']);
    $supabaseTime = strtotime($supabaseRow['updated_at']);
    
    if ($mysqlTime > $supabaseTime) {
        // MySQL gagne
        $this->logConflict($table, $mysqlRow['id'], 'mysql_wins');
        return $mysqlRow;
    } else {
        // Supabase gagne
        $this->logConflict($table, $supabaseRow['id'], 'supabase_wins');
        return $supabaseRow;
    }
}
```

### 5.4 Monitoring

```php
// core/SyncMonitor.php
class SyncMonitor
{
    public function logSync(string $table, int $count, string $status): void
    {
        $log = [
            'timestamp' => date('Y-m-d H:i:s'),
            'table' => $table,
            'count' => $count,
            'status' => $status,
            'duration_ms' => $this->getDuration()
        ];
        
        file_put_contents(
            __DIR__ . '/../logs/sync.log',
            json_encode($log) . "\n",
            FILE_APPEND
        );
    }
    
    public function alertIfFailed(string $table, string $error): void
    {
        // Envoyer une alerte email ou Slack
        $this->sendAlert("Sync failed for $table: $error");
    }
}
```

---

## 6. Questions à Clarifier

### 6.1 Infrastructure

1. **Hébergement MySQL actuel**: Est-ce que MySQL restera hébergé localement ou sera-t-il migré vers un cloud (AWS RDS, DigitalOcean, etc.) ?

2. **Volume de données**: Quelle est la taille actuelle de la base de données ? Combien de lignes par table ?

3. **Traffic**: Combien de visiteurs par jour ? Combien d'opérations d'écriture par jour ?

4. **Budget**: Y a-t-il un budget pour l'hébergement Supabase (tier gratuit ou payant) ?

### 6.2 Fonctionnalités

5. **Authentification**: Voulez-vous migrer vers Supabase Auth ou garder PHP sessions pendant la transition ?

6. **Stockage**: Les fichiers uploadés doivent-ils être migrés vers Supabase Storage ou rester en local ?

7. **Email**: Voulez-vous utiliser Supabase Email ou un autre service (SendGrid, Resend, etc.) ?

8. **Realtime**: Avez-vous besoin de fonctionnalités temps réel (notifications, mise à jour en direct) ?

### 6.3 Timeline

9. **Urgence**: Y a-t-il une deadline pour la migration ?

10. **Ressources**: Combien de développeurs travailleront sur la migration ?

11. **Maintenance**: Qui maintiendra l'application après la migration ?

### 6.4 Technique

12. **PHP Version**: Quelle version de PHP est utilisée ? (Vercel supporte PHP 8.1+)

13. **Dépendances**: Y a-t-il des dépendances PHP externes (Composer) ?

14. **Custom SQL**: Y a-t-il des requêtes SQL complexes ou des procédures stockées ?

15. **Tests**: Existe-t-il déjà des tests unitaires ou d'intégration ?

---

## 7. Risques et Mitigations

| Risque | Probabilité | Impact | Mitigation |
|--------|-------------|--------|------------|
| Perte de données lors de la migration | Faible | Critique | Backup complet avant migration, tests de sync |
| Différences SQL MySQL/PostgreSQL | Moyenne | Moyen | Tests complets, conversion manuelle si nécessaire |
| Performance dégradée sur Supabase | Faible | Moyen | Monitoring, optimisation des requêtes, indexes |
| Problèmes de session sur Vercel | Moyenne | Moyen | Tests de session, alternative Supabase Auth |
| Coûts Supabase élevés | Faible | Moyen | Monitoring de l'utilisation, optimisation |
| Temps de migration sous-estimé | Moyenne | Moyen | Plan avec buffer, priorisation des features |

---

## 8. Checklist de Validation

### Avant de commencer

- [ ] Backup complet de la base MySQL
- [ ] Backup des fichiers uploads
- [ ] Documentation de l'architecture actuelle
- [ ] Tests de régression existants

### Après chaque phase

- [ ] Tests de régression passants
- [ ] Documentation mise à jour
- [ ] Code review effectuée
- [ ] Backup incrémental

### Avant le déploiement en production

- [ ] Tests complets sur environnement de staging
- [ ] Performance tests
- [ ] Security audit
- [ ] Plan de rollback préparé

---

## 9. Ressources

- [Supabase Documentation](https://supabase.com/docs)
- [Vercel PHP Runtime](https://vercel.com/docs/runtimes/php)
- [MySQL to PostgreSQL Migration Guide](https://www.postgresql.org/docs/current/migration.html)
- [Repository Pattern](https://martinfowler.com/eaaCatalog/repository.html)
- [Strangler Pattern](https://martinfowler.com/bliki/StranglerFigApplication.html)
