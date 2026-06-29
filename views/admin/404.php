<?php
$title = '404 Not Found';
$lang = 'fr';
$accent = '#c9a227';
$basePath = '../';
require __DIR__ . '/../shared/head.php';
?>
<main style="min-height:100vh; display:flex; align-items:center; justify-content:center; padding:2rem; background:linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
    <div style="max-width:600px; width:100%; text-align:center; background:#fff; border-radius:1.75rem; padding:3rem; box-shadow:0 20px 60px rgba(0,0,0,0.3); border: 1px solid #e2e8f0;">
        <h1 style="font-size:4rem; margin: 0 0 1rem; background:linear-gradient(135deg, #f59e0b 0%, #d97706 100%); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text;">🔍 404</h1>
        <h2 style="font-size:1.5rem; color:#1e293b; margin-bottom:1rem;">Page non trouvée</h2>
        <p style="color:#64748b; margin-bottom:2rem; line-height:1.6;"><?= View::escape($message ?? 'La page que vous recherchez n\'existe pas ou a été supprimée.') ?></p>
        <a class="admin-btn admin-btn-primary" href="dashboard.php">📊 Retour au tableau de bord</a>
    </div>
</main>
<?php require __DIR__ . '/../shared/footer.php'; ?>