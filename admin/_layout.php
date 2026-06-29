<?php
require_once __DIR__ . '/../core/auth.php';
require_once __DIR__ . '/_helpers.php';

require_admin();
$user = admin_user();
$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portfolio</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body { background: #f4f6fb; }
        .admin-shell { width: min(1100px, calc(100% - 32px)); margin: 0 auto; padding: 2rem 0; }
        .admin-nav { display: flex; flex-wrap: wrap; gap: 0.85rem; margin-bottom: 1.75rem; }
        .admin-card { background: #fff; border-radius: 1.5rem; padding: 1.5rem; box-shadow: 0 18px 40px rgba(15,23,42,0.08); }
        .admin-nav a { display: inline-flex; padding: 0.75rem 1rem; border-radius: 999px; background: #eef2f7; color: #15233b; font-weight: 700; }
        .admin-nav a.logout { margin-left: auto; background: #f8d7da; color: #842029; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 0.95rem 0.75rem; border-bottom: 1px solid #eaf0f6; text-align: left; }
        th { color: #344054; font-weight: 700; }
        .form-field { margin-bottom: 1rem; }
        .form-field label { display: block; margin-bottom: 0.45rem; font-weight: 700; }
        .form-field input, .form-field textarea, .form-field select { width: 100%; padding: 0.95rem 1rem; border: 1px solid #dce4ed; border-radius: 0.95rem; }
        .form-row { display: grid; gap: 1rem; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); }
        .admin-actions { margin-top: 1.5rem; display: flex; gap: 1rem; }
        .admin-alert { padding: 1rem 1.25rem; border-radius: 1rem; margin-bottom: 1.25rem; }
        .admin-alert.success { background: #e6f4ea; color: #1f6b3b; }
        .admin-alert.error { background: #f8d7da; color: #842029; }
        @media (max-width: 720px) { .admin-nav { flex-direction: column; } }
    </style>
</head>
<body>
         <div class="admin-layout">
    
    <!-- Sidebar -->
    <aside class="admin-sidebar">
        <div class="sidebar-title">Portfolio Admin</div>

        <nav class="admin-nav">
            <a href="dashboard.php">Tableau de bord</a>
            <a href="settings.php">Paramètres</a>
            <a href="manage.php?type=skills">Compétences</a>
            <a href="manage.php?type=projects">Projets</a>
            <a href="manage.php?type=education">Formations</a>
            <a href="manage.php?type=associations">Associations</a>
            <a href="manage.php?type=languages">Langues</a>
            <a href="manage.php?type=contacts">Contacts</a>
            <a href="manage.php?type=inbox">Messages</a>
            <a href="logout.php" class="logout">Déconnexion</a>
        </nav>
    </aside>

    <!-- Contenu principal -->
    <main class="admin-content">
        <header class="admin-header">
            <span>Bonjour, <?= htmlspecialchars($user['email']) ?></span>
        </header>
    </main>
        <?php if ($flash): ?>
            <div class="admin-alert <?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['message']) ?></div>
        <?php endif; ?>
