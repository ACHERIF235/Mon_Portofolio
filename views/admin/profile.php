<?php
$title = 'Mon Profil';
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
            <h2>👤 Mon Profil</h2>
            <p>Modifiez vos identifiants de connexion au tableau de bord administrateur.</p>
            <form method="post" class="admin-form">
                <input type="hidden" name="csrf_token" value="<?= View::escape(generate_csrf_token()) ?>">
                
                <div style="margin-bottom: 2rem;">
                    <h3 style="margin-top:0; color:#1e293b; font-size:1.1rem;">Identifiants</h3>
                    <div class="form-row">
                        <div class="admin-form-group">
                            <label>Nouvel Email de connexion</label>
                            <input type="email" name="admin_email" value="<?= View::escape($currentUser['email'] ?? '') ?>" required>
                        </div>
                        <div class="admin-form-group">
                            <label>Nouveau Mot de passe</label>
                            <input type="password" name="admin_password" placeholder="Laisser vide pour ne pas modifier">
                        </div>
                    </div>
                </div>

                <div class="admin-actions">
                    <button class="admin-btn admin-btn-primary" type="submit">💾 Enregistrer le profil</button>
                </div>
            </form>
        </section>
    </div>
</div>
<?php require __DIR__ . '/../shared/footer.php'; ?>
