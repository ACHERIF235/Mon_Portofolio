<?php
$title = 'Admin login';
$lang = 'fr';
$accent = '#c9a227';
$basePath = '../';
require __DIR__ . '/../shared/head.php';
?>
<div class="admin-login-container">
    <div class="admin-login-card">
        <h1>🔐 Connexion Admin</h1>
        <p>Connectez-vous avec votre compte administrateur pour gérer le portfolio.</p>
        <?php if (!empty($error)): ?>
            <div class="admin-alert error">
                ❌ <?= View::escape($error) ?>
            </div>
        <?php endif; ?>
        <form method="post" class="admin-form">
            <div class="admin-form-group">
                <label for="email">📧 Email</label>
                <input type="email" id="email" name="email" required placeholder="admin@example.com">
            </div>
            <div class="admin-form-group">
                <label for="password">🔑 Mot de passe</label>
                <input type="password" id="password" name="password" required placeholder="Entrez votre mot de passe">
            </div>
            <button class="admin-btn admin-btn-primary" type="submit" style="width: 100%; justify-content: center;">Se connecter</button>
        </form>
    </div>
</div>
<?php require __DIR__ . '/../shared/footer.php'; ?>