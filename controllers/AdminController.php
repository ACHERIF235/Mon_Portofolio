<?php
require_once __DIR__ . '/../core/bootstrap.php';
require_once __DIR__ . '/../models/SettingModel.php';
require_once __DIR__ . '/../models/SectionModel.php';
require_once __DIR__ . '/../models/UploadModel.php';

class AdminController
{
    public static function requireAdmin(): void
    {
        require_admin();
    }

    public static function dashboard(): void
    {
        self::requireAdmin();
        $counts = [];
        foreach (SectionModel::getSections() as $key => $section) {
            if ($section['table'] === 'inbox_messages') {
                $counts[$key] = db_fetch('SELECT COUNT(*) AS total FROM ' . $section['table'])['total'] ?? 0;
            } else {
                $counts[$key] = db_fetch('SELECT COUNT(*) AS total FROM ' . $section['table'])['total'] ?? 0;
            }
        }
        View::render('admin/dashboard', ['counts' => $counts]);
    }

    public static function settings(): void
    {
        self::requireAdmin();
        $settings = SettingModel::all();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
                flash('Jeton CSRF invalide.', 'error');
                header('Location: settings.php');
                exit;
            }

            $fields = [
                'hero_name_fr', 'hero_name_en', 'hero_title_fr', 'hero_title_en',
                'hero_location_fr', 'hero_location_en', 'hero_intro_fr', 'hero_intro_en',
                'about_text_fr', 'about_text_en', 'github_url', 'contact_email', 'contact_phone'
            ];

            foreach ($fields as $field) {
                SettingModel::save($field, sanitize_text($_POST[$field] ?? ''));
            }

            $uploadDir = $GLOBALS['config']['uploads_dir'];
            $uploadError = false;
            if (!empty($_FILES['profile_photo']['name'])) {
                $filename = UploadModel::save($_FILES['profile_photo'], $GLOBALS['config']['allowed_image_types'], ['jpg', 'jpeg', 'png', 'webp'], $uploadDir);
                if ($filename) {
                    SettingModel::save('profile_photo', $filename);
                } else {
                    $uploadError = true;
                }
            }
            if (!empty($_FILES['resume_file']['name'])) {
                $filename = UploadModel::save($_FILES['resume_file'], $GLOBALS['config']['allowed_doc_types'], ['pdf'], $uploadDir);
                if ($filename) {
                    SettingModel::save('resume_file', $filename);
                } else {
                    $uploadError = true;
                }
            }

            if ($uploadError) {
                flash('Les textes ont été enregistrés, mais l\'upload du fichier a échoué. Avez-vous créé le bucket public "uploads" sur Supabase ?', 'error');
            } else {
                flash('Paramètres enregistrés avec succès.');
            }
            header('Location: settings.php');
            exit;
        }

        View::render('admin/settings', ['settings' => $settings]);
    }

    public static function theme(): void
    {
        self::requireAdmin();
        $settings = SettingModel::all();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
                flash('Jeton CSRF invalide.', 'error');
                header('Location: theme.php');
                exit;
            }

            $fields = ['theme_mode', 'accent_color'];

            foreach ($fields as $field) {
                if (isset($_POST[$field])) {
                    SettingModel::save($field, sanitize_text($_POST[$field]));
                }
            }

            flash('Thème enregistré avec succès.');
            header('Location: theme.php');
            exit;
        }

        View::render('admin/theme', ['settings' => $settings]);
    }

    public static function manage(): void
    {
        self::requireAdmin();
        $type = $_GET['type'] ?? 'skills';
        if ($type === 'inbox') {
            // Auto mark all read is removed to keep unread visibility
        }
        $section = SectionModel::getSection($type);
        if (!$section) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
            View::render('admin/404', ['message' => 'Section introuvable.']);
            exit;
        }
        $rows = SectionModel::findAll($type);
        View::render('admin/manage', ['section' => $section, 'rows' => $rows, 'type' => $type]);
    }

    public static function edit(): void
    {
        self::requireAdmin();
        $type = $_GET['type'] ?? 'skills';
        $section = SectionModel::getSection($type);
        if (!$section) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
            View::render('admin/404', ['message' => 'Section introuvable.']);
            exit;
        }

        $item = null;
        if (!empty($_GET['id'])) {
            $item = SectionModel::find($type, (int)$_GET['id']);
            if (!$item) {
                header('Location: manage.php?type=' . urlencode($type));
                exit;
            }
        }

        View::render('admin/edit', ['section' => $section, 'item' => $item, 'type' => $type]);
    }

    public static function save(): void
    {
        self::requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: manage.php?type=skills');
            exit;
        }

        if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
            flash('Jeton CSRF invalide.', 'error');
            header('Location: manage.php?type=' . urlencode($_POST['type'] ?? 'skills'));
            exit;
        }

        $type = $_POST['type'] ?? '';
        $section = SectionModel::getSection($type);
        if (!$section) {
            header('Location: manage.php?type=skills');
            exit;
        }

        $data = [];
        foreach ($section['fields'] as $field) {
            $data[$field['name']] = sanitize_text($_POST[$field['name']] ?? '');
        }

        $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;
        SectionModel::save($type, $data, $id);

        flash($id ? 'Élément mis à jour.' : 'Élément créé.');
        header('Location: manage.php?type=' . urlencode($type));
        exit;
    }

    public static function delete(): void
    {
        self::requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: manage.php?type=skills');
            exit;
        }

        if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
            flash('Jeton CSRF invalide.', 'error');
            header('Location: manage.php?type=' . urlencode($_POST['type'] ?? 'skills'));
            exit;
        }

        $type = $_POST['type'] ?? '';
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            SectionModel::delete($type, $id);
            flash('Élément supprimé.');
        }
        header('Location: manage.php?type=' . urlencode($type));
        exit;
    }

    public static function toggleRead(): void
    {
        self::requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: manage.php?type=inbox');
            exit;
        }

        if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
            flash('Jeton CSRF invalide.', 'error');
            header('Location: manage.php?type=inbox');
            exit;
        }

        $id = (int)($_POST['id'] ?? 0);
        $status = (int)($_POST['status'] ?? 0);
        if ($id) {
            db_query("UPDATE inbox_messages SET is_read = :status WHERE id = :id", ['status' => $status, 'id' => $id]);
            flash('Statut mis à jour avec succès.');
        }

        header('Location: manage.php?type=inbox');
        exit;
    }

    public static function reply(): void
    {
        self::requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: manage.php?type=inbox');
            exit;
        }

        if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
            flash('Jeton CSRF invalide.', 'error');
            header('Location: manage.php?type=inbox');
            exit;
        }

        $id = isset($_POST['id']) ? (int)$_POST['id'] : null;
        $email = validate_email($_POST['email'] ?? '');
        $subject = sanitize_text($_POST['subject'] ?? 'Réponse à votre message');
        $message = sanitize_text($_POST['message'] ?? '');

        if (empty($email) || empty($message)) {
            flash('L\'adresse email ou le message est invalide.', 'error');
            header('Location: manage.php?type=inbox');
            exit;
        }

        $adminEmail = SettingModel::get('contact_email', 'alifa.acherif1@ugb.edu.sn');
        
        $sent = send_email($email, $subject, $message, $adminEmail);

        // Simulation de succès sur localhost
        if (!$sent && (in_array($_SERVER['HTTP_HOST'] ?? '', ['localhost', '127.0.0.1']))) {
            $sent = true;
        }

        if ($sent) {
            if ($id) {
                db_query("UPDATE inbox_messages SET is_replied = :replied, is_read = :read WHERE id = :id", ['replied' => 1, 'read' => 1, 'id' => $id]);
            }
            flash('Votre réponse a été envoyée avec succès.');
        } else {
            flash('Erreur lors de l\'envoi de la réponse.', 'error');
        }

        header('Location: manage.php?type=inbox');
        exit;
    }
}
