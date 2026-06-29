<?php
$title = 'Admin Edit';
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
            <h2><?= $item ? '✏️ Modifier' : '➕ Ajouter' ?> : <?= View::escape($section['name']) ?></h2>
            <form method="post" action="save.php" class="admin-form">
                <input type="hidden" name="csrf_token" value="<?= View::escape(generate_csrf_token()) ?>">
                <input type="hidden" name="type" value="<?= View::escape($type) ?>">
                <?php if ($item): ?>
                    <input type="hidden" name="id" value="<?= View::escape((string)$item['id']) ?>">
                <?php endif; ?>
                <?php foreach ($section['fields'] as $field): ?>
                    <div class="admin-form-group">
                        <label><?= View::escape($field['label']) ?></label>
                        <?php if (in_array($field['name'], ['description_fr', 'description_en'], true)): ?>
                            <textarea name="<?= View::escape($field['name']) ?>" rows="5"><?= View::escape($item[$field['name']] ?? '') ?></textarea>
                        <?php else: ?>
                            <input type="text" name="<?= View::escape($field['name']) ?>" value="<?= View::escape($item[$field['name']] ?? '') ?>">
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
                <div class="admin-actions">
                    <button type="submit" class="admin-btn admin-btn-primary"><?= $item ? '💾 Mettre à jour' : '✅ Créer' ?></button>
                    <a class="admin-btn admin-btn-secondary" href="manage.php?type=<?= urlencode($type) ?>">❌ Annuler</a>
                </div>
            </form>
        </section>
    </div>
</div>
<?php require __DIR__ . '/../shared/footer.php'; ?>