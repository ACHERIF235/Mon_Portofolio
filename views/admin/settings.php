<?php
$title = 'Admin Settings';
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
            <h2>⚙️ Paramètres généraux</h2>
            <form method="post" enctype="multipart/form-data" class="admin-form">
                <input type="hidden" name="csrf_token" value="<?= View::escape(generate_csrf_token()) ?>">
                
                <div style="margin-bottom: 2rem;">
                    <h3 style="margin-top:0; color:#1e293b; font-size:1.1rem;">👤 Informations personnelles</h3>
                    <div class="form-row">
                        <div class="admin-form-group">
                            <label>Nom (FR)</label>
                            <input type="text" name="hero_name_fr" value="<?= View::escape($settings['hero_name_fr'] ?? 'Acherif AHMAT ALIFA') ?>">
                        </div>
                        <div class="admin-form-group">
                            <label>Nom (EN)</label>
                            <input type="text" name="hero_name_en" value="<?= View::escape($settings['hero_name_en'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="admin-form-group">
                            <label>Titre professionnel (FR)</label>
                            <input type="text" name="hero_title_fr" value="<?= View::escape($settings['hero_title_fr'] ?? '') ?>">
                        </div>
                        <div class="admin-form-group">
                            <label>Titre professionnel (EN)</label>
                            <input type="text" name="hero_title_en" value="<?= View::escape($settings['hero_title_en'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="admin-form-group">
                            <label>Localisation (FR)</label>
                            <input type="text" name="hero_location_fr" value="<?= View::escape($settings['hero_location_fr'] ?? '') ?>">
                        </div>
                        <div class="admin-form-group">
                            <label>Localisation (EN)</label>
                            <input type="text" name="hero_location_en" value="<?= View::escape($settings['hero_location_en'] ?? '') ?>">
                        </div>
                    </div>
                </div>

                <div style="margin-bottom: 2rem;">
                    <h3 style="margin-top:0; color:#1e293b; font-size:1.1rem;">📝 Textes de présentation</h3>
                    <div class="admin-form-group">
                        <label>Introduction (FR)</label>
                        <textarea name="hero_intro_fr" rows="4"><?= View::escape($settings['hero_intro_fr'] ?? '') ?></textarea>
                    </div>
                    <div class="admin-form-group">
                        <label>Introduction (EN)</label>
                        <textarea name="hero_intro_en" rows="4"><?= View::escape($settings['hero_intro_en'] ?? '') ?></textarea>
                    </div>
                    <div class="admin-form-group">
                        <label>À propos (FR)</label>
                        <textarea name="about_text_fr" rows="5"><?= View::escape($settings['about_text_fr'] ?? '') ?></textarea>
                    </div>
                    <div class="admin-form-group">
                        <label>À propos (EN)</label>
                        <textarea name="about_text_en" rows="5"><?= View::escape($settings['about_text_en'] ?? '') ?></textarea>
                    </div>
                </div>

                <div style="margin-bottom: 2rem;">
                    <h3 style="margin-top:0; color:#1e293b; font-size:1.1rem;">🔗 Liens & Réseaux</h3>
                    <div class="form-row">
                        <div class="admin-form-group">
                            <label>URL GitHub</label>
                            <input type="url" name="github_url" value="<?= View::escape($settings['github_url'] ?? 'https://github.com/ACHERIF235') ?>">
                        </div>
                    </div>
                </div>

                <div style="margin-bottom: 2rem;">
                    <h3 style="margin-top:0; color:#1e293b; font-size:1.1rem;">📞 Contact</h3>
                    <div class="form-row">
                        <div class="admin-form-group">
                            <label>Email de contact</label>
                            <input type="email" name="contact_email" value="<?= View::escape($settings['contact_email'] ?? 'alifa.acherif1@ugb.edu.sn') ?>">
                        </div>
                        <div class="admin-form-group">
                            <label>Téléphone</label>
                            <input type="text" name="contact_phone" value="<?= View::escape($settings['contact_phone'] ?? '+235 66 45 39 03') ?>">
                        </div>
                    </div>
                </div>

                <div style="margin-bottom: 2rem;">
                    <h3 style="margin-top:0; color:#1e293b; font-size:1.1rem;">📎 Fichiers</h3>
                    <div class="form-row">
                        <div class="admin-form-group">
                            <label>Photo de profil</label>
                            <input type="file" name="profile_photo" accept="image/*">
                        </div>
                        <div class="admin-form-group">
                            <label>CV (PDF)</label>
                            <input type="file" name="resume_file" accept="application/pdf">
                        </div>
                    </div>
                </div>

                <div class="admin-actions">
                    <button class="admin-btn admin-btn-primary" type="submit">💾 Enregistrer les paramètres</button>
                </div>
            </form>
        </section>
    </div>
</div>
<?php require __DIR__ . '/../shared/footer.php'; ?>