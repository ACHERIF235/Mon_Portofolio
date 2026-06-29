<?php
$title = 'Admin Dashboard';
$lang = 'fr';
$accent = '#c9a227';
$basePath = '../';
$adminEmail = $_SESSION['admin_email'] ?? 'admin';
require __DIR__ . '/../shared/head.php';
?>
<div class="admin-shell">
    <?php require __DIR__ . '/../shared/admin_nav.php'; ?>
    <div class="container">
        <?php if (!empty($_SESSION['flash'])): ?>
            <?php $flash = get_flash(); ?>
            <div class="admin-alert <?= View::escape($flash['type']) ?>"><?= View::escape($flash['message']) ?></div>
        <?php endif; ?>
        <section class="admin-card">
            <h2>📊 Tableau de bord</h2>
            <p>Accédez rapidement aux sections pour gérer le contenu de votre portfolio.</p>
            <div class="admin-stats-grid">
                <?php foreach ($counts as $key => $count): ?>
                    <div class="admin-stat-card">
                        <strong><?= View::escape($count) ?></strong>
                        <span><?= View::escape($key) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>
</div>
<?php require __DIR__ . '/../shared/footer.php'; ?>