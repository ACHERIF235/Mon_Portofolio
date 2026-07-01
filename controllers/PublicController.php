<?php
require_once __DIR__ . '/../core/bootstrap.php';
require_once __DIR__ . '/../models/SettingModel.php';
require_once __DIR__ . '/../models/SectionModel.php';
require_once __DIR__ . '/../models/MessageModel.php';

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
        $sent = send_email($destinationEmail, $subject, $body, $email);

        // Sur un environnement local (XAMPP), la fonction mail() échoue souvent sans configuration SMTP.
        // Puisque le message est bien sauvegardé en base de données, on force le succès si on est en local et qu'on a pas configuré Resend.
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
