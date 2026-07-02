<?php
/**
 * EXEMPLE DE MIGRATION: Controller avec Repository Pattern
 * 
 * Ce fichier montre comment migrer un contrôleur existant
 * pour utiliser les nouveaux repositories.
 */

// ============================================================================
// 1. CONTROLLER ORIGINAL (Legacy) - controllers/PublicController.php
// ============================================================================
/*
class PublicController
{
    public static function home(): void
    {
        $lang = $_GET['lang'] ?? 'fr';
        if (!in_array($lang, ['fr', 'en'], true)) {
            $lang = 'fr';
        }

        $settings = SettingModel::all();
        $data = [
            'lang' => $lang,
            'settings' => $settings,
            'skills' => SectionModel::findAll('skills'),
            'projects' => SectionModel::findAll('projects'),
            'education' => SectionModel::findAll('education'),
            'associations' => SectionModel::findAll('associations'),
            'languages' => SectionModel::findAll('languages'),
            'contacts' => SectionModel::findAll('contacts'),
        ];

        View::render('public/home', $data);
    }

    public static function submitContact(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php');
            exit;
        }

        $name = sanitize_text($_POST['name'] ?? '');
        $email = validate_email($_POST['email'] ?? '');
        $message = sanitize_text($_POST['message'] ?? '');
        $lang = in_array($_POST['lang'] ?? 'fr', ['fr', 'en'], true) ? $_POST['lang'] : 'fr';
        $csrf = $_POST['csrf_token'] ?? null;

        if (!verify_csrf_token($csrf) || empty($name) || empty($email) || empty($message)) {
            header('Location: index.php?lang=' . $lang);
            exit;
        }

        MessageModel::create($name, $email, $message);
        
        // ... envoi d'email ...
    }
}
*/

// ============================================================================
// 2. CONTROLLER MIGRÉ (avec Dependency Injection)
// ============================================================================
require_once __DIR__ . '/../repositories/SettingRepository.php';
require_once __DIR__ . '/../repositories/SectionRepository.php';
require_once __DIR__ . '/../repositories/MessageRepository.php';

class PublicController
{
    // Injection de dépendances via propriétés statiques
    private static ?SettingRepository $settingRepo = null;
    private static ?SectionRepository $sectionRepo = null;
    private static ?MessageRepository $messageRepo = null;

    /**
     * Initialise les repositories (lazy loading)
     */
    private static function getSettingRepo(): SettingRepository
    {
        if (self::$settingRepo === null) {
            self::$settingRepo = new SettingRepository();
        }
        return self::$settingRepo;
    }

    private static function getSectionRepo(): SectionRepository
    {
        if (self::$sectionRepo === null) {
            self::$sectionRepo = new SectionRepository();
        }
        return self::$sectionRepo;
    }

    private static function getMessageRepo(): MessageRepository
    {
        if (self::$messageRepo === null) {
            self::$messageRepo = new MessageRepository();
        }
        return self::$messageRepo;
    }

    /**
     * Page d'accueil
     * MIGRATION: Utilise les repositories au lieu des Models
     */
    public static function home(): void
    {
        $lang = $_GET['lang'] ?? 'fr';
        if (!in_array($lang, ['fr', 'en'], true)) {
            $lang = 'fr';
        }

        // MIGRATION: Utilisation des repositories
        $settings = self::getSettingRepo()->getAllAsArray();
        $data = [
            'lang' => $lang,
            'settings' => $settings,
            'skills' => self::getSectionRepo()->findAllBySection('skills'),
            'projects' => self::getSectionRepo()->findAllBySection('projects'),
            'education' => self::getSectionRepo()->findAllBySection('education'),
            'associations' => self::getSectionRepo()->findAllBySection('associations'),
            'languages' => self::getSectionRepo()->findAllBySection('languages'),
            'contacts' => self::getSectionRepo()->findAllBySection('contacts'),
        ];

        View::render('public/home', $data);
    }

    /**
     * Soumission du formulaire de contact
     * MIGRATION: Utilise MessageRepository
     */
    public static function submitContact(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php');
            exit;
        }

        $name = sanitize_text($_POST['name'] ?? '');
        $email = validate_email($_POST['email'] ?? '');
        $message = sanitize_text($_POST['message'] ?? '');
        $lang = in_array($_POST['lang'] ?? 'fr', ['fr', 'en'], true) ? $_POST['lang'] : 'fr';
        $csrf = $_POST['csrf_token'] ?? null;

        if (!verify_csrf_token($csrf) || empty($name) || empty($email) || empty($message)) {
            header('Location: index.php?lang=' . $lang);
            exit;
        }

        // MIGRATION: Utilisation du repository
        $saved = self::getMessageRepo()->createMessage($name, $email, $message);
        
        if (!$saved) {
            // Gestion d'erreur
            error_log("Failed to save contact message from $email");
        }

        // ... envoi d'email (inchangé) ...
        
        $destinationEmail = 'alifa.acherif1@ugb.edu.sn';
        $subject = $lang === 'fr' ? 'Nouveau message de contact' : 'New contact message';
        $body = sprintf(
            "%s: %s\n%s: %s\n\n%s:\n%s",
            $lang === 'fr' ? 'Nom' : 'Name',
            $name,
            $lang === 'fr' ? 'Email' : 'Email',
            $email,
            $lang === 'fr' ? 'Message' : 'Message',
            $message
        );
        $headers = 'From: no-reply@' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . "\r\n";
        $headers .= 'Reply-To: ' . $email . "\r\n";
        $headers .= 'Content-Type: text/plain; charset=UTF-8\r\n';
        $sent = @mail($destinationEmail, $subject, $body, $headers);

        if (!$sent && (in_array($_SERVER['HTTP_HOST'] ?? '', ['localhost', '127.0.0.1']))) {
            $sent = true;
        }

        $redirect = 'index.php?lang=' . $lang . '&sent=' . ($sent ? '1' : '0');
        if (!$sent) {
            $redirect .= '&mail_error=1';
        }

        header('Location: ' . $redirect);
        exit;
    }
}

// ============================================================================
// 3. CONTROLLER AVEC INJECTION DE DÉPENDANCES (Alternative)
// ============================================================================
/*
// Cette approche utilise l'injection via constructeur
// Plus testable et plus flexible, mais nécessite des changements plus importants

class PublicControllerDI
{
    private SettingRepository $settingRepo;
    private SectionRepository $sectionRepo;
    private MessageRepository $messageRepo;

    public function __construct(
        ?SettingRepository $settingRepo = null,
        ?SectionRepository $sectionRepo = null,
        ?MessageRepository $messageRepo = null
    ) {
        $this->settingRepo = $settingRepo ?? new SettingRepository();
        $this->sectionRepo = $sectionRepo ?? new SectionRepository();
        $this->messageRepo = $messageRepo ?? new MessageRepository();
    }

    public function home(): void
    {
        $lang = $_GET['lang'] ?? 'fr';
        if (!in_array($lang, ['fr', 'en'], true)) {
            $lang = 'fr';
        }

        $settings = $this->settingRepo->getAllAsArray();
        $data = [
            'lang' => $lang,
            'settings' => $settings,
            'skills' => $this->sectionRepo->findAllBySection('skills'),
            'projects' => $this->sectionRepo->findAllBySection('projects'),
            'education' => $this->sectionRepo->findAllBySection('education'),
            'associations' => $this->sectionRepo->findAllBySection('associations'),
            'languages' => $this->sectionRepo->findAllBySection('languages'),
            'contacts' => $this->sectionRepo->findAllBySection('contacts'),
        ];

        View::render('public/home', $data);
    }

    // ... autres méthodes ...
}
*/

// ============================================================================
// 4. AVANTAGES DE LA MIGRATION
// ============================================================================
/*
1. ABSTACTION DE LA BASE DE DONNÉES
   - Le contrôleur ne sait plus si c'est MySQL ou Supabase
   - Le changement de DB se fait dans config.php uniquement

2. TESTABILITÉ
   - On peut injecter des mock repositories pour les tests
   - Pas besoin de base de données réelle pour tester les contrôleurs

3. PERFORMANCE
   - Les repositories peuvent implémenter du cache
   - Les batch operations réduisent le nombre de requêtes

4. MAINTENABILITÉ
   - La logique d'accès aux données est centralisée
   - Plus facile de modifier les requêtes SQL

5. MIGRATION PROGRESSIVE
   - On peut migrer un contrôleur à la fois
   - Les anciens modèles continuent de fonctionner
*/
