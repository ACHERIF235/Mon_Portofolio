<?php
$title = 'Personnalisation du Thème';
$adminEmail = $_SESSION['admin_email'] ?? 'admin';
$basePath = '../';
$accent = $settings['accent_color'] ?? '#c9a227';
$themeMode = $settings['theme_mode'] ?? 'light';

require __DIR__ . '/../shared/head.php';
?>
<div class="admin-shell" style="padding-bottom: 0;">
    <?php require __DIR__ . '/../shared/admin_nav.php'; ?>
    <div class="container" style="max-width: 100%; padding: 1rem 2rem;">
        <?php if (!empty($_SESSION['flash'])): ?>
            <?php $flash = get_flash(); ?>
            <div class="admin-alert <?= View::escape($flash['type']) ?>"><?= View::escape($flash['message']) ?></div>
        <?php endif; ?>
        
        <div style="display: flex; flex-wrap: wrap; gap: 2rem; height: calc(100vh - 100px);">
            
            <!-- CONTROLS COLUMN -->
            <section class="admin-card scroll-animate" style="flex: 1; min-width: 350px; max-width: 500px; display: flex; flex-direction: column; overflow-y: auto; height: 100%;">
                <h2 style="margin-top:0; color:#1e293b; font-size:1.5rem; border-bottom: 2px solid var(--accent); padding-bottom: 0.5rem; display:inline-block;">🎨 Personnalisation Live</h2>
                <p style="color: #64748b; margin-bottom: 2rem;">Modifiez les couleurs ci-dessous. Le portfolio se mettra à jour automatiquement !</p>
                
                <form id="themeForm" method="post" action="theme.php" class="admin-form">
                    <input type="hidden" name="csrf_token" value="<?= View::escape(generate_csrf_token()) ?>">
                    
                    <div style="margin-bottom: 2.5rem;">
                        <h3 style="margin-top:0; color:#1e293b; font-size:1.1rem; margin-bottom: 1rem;">🌓 Mode d'affichage</h3>
                        
                        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                            <label style="flex: 1; min-width: 150px; cursor: pointer; border: 2px solid <?= $themeMode === 'light' ? 'var(--accent)' : '#e2e8f0' ?>; border-radius: 1rem; padding: 1.5rem 1rem; text-align: center; background: #fff; transition: all 0.3s; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
                                <input type="radio" name="theme_mode" value="light" <?= $themeMode === 'light' ? 'checked' : '' ?> style="display:none;" onchange="selectMode(this)">
                                <div style="font-size: 2rem; margin-bottom: 0.5rem;">☀️</div>
                                <strong style="display: block; color: #1e293b; font-size: 0.9rem;">Mode Clair</strong>
                            </label>
                            
                            <label style="flex: 1; min-width: 150px; cursor: pointer; border: 2px solid <?= $themeMode === 'dark' ? 'var(--accent)' : '#e2e8f0' ?>; border-radius: 1rem; padding: 1.5rem 1rem; text-align: center; background: #0f172a; transition: all 0.3s; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
                                <input type="radio" name="theme_mode" value="dark" <?= $themeMode === 'dark' ? 'checked' : '' ?> style="display:none;" onchange="selectMode(this)">
                                <div style="font-size: 2rem; margin-bottom: 0.5rem;">🌙</div>
                                <strong style="display: block; color: #f8fafc; font-size: 0.9rem;">Mode Sombre</strong>
                            </label>
                        </div>
                    </div>

                    <div style="margin-bottom: 2rem;">
                        <h3 style="margin-top:0; color:#1e293b; font-size:1.1rem; margin-bottom: 1rem;">🖌️ Couleur d'Accentuation</h3>
                        
                        <?php
                        $palettes = [
                            '#c9a227' => 'Or',
                            '#3b82f6' => 'Bleu',
                            '#10b981' => 'Émeraude',
                            '#ef4444' => 'Rouge',
                            '#8b5cf6' => 'Violet',
                            '#f97316' => 'Orange',
                            '#ec4899' => 'Rose',
                            '#14b8a6' => 'Teal'
                        ];
                        ?>
                        
                        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.5rem;">
                            <?php foreach($palettes as $hex => $name): ?>
                                <div class="color-swatch-container" style="text-align: center; cursor: pointer;" onclick="selectColor('<?= $hex ?>', this)">
                                    <div class="swatch-circle" data-color="<?= $hex ?>" style="width: 40px; height: 40px; background-color: <?= $hex ?>; border-radius: 50%; margin: 0 auto 0.5rem; box-shadow: 0 2px 5px rgba(0,0,0,0.2); border: 3px solid <?= $accent === $hex ? '#fff' : 'transparent' ?>; outline: <?= $accent === $hex ? '2px solid '.$hex : 'none' ?>;"></div>
                                    <div style="font-size: 0.7rem; color: #64748b; font-weight: 500;"><?= $name ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="form-row" style="align-items: center; background: #f1f5f9; padding: 1rem; border-radius: 0.5rem;">
                            <div class="admin-form-group" style="margin-bottom: 0; flex: 1;">
                                <label style="margin-bottom: 0.5rem;">Personnalisée (Hex)</label>
                                <div style="display: flex; gap: 0.5rem; align-items: center;">
                                    <div id="color_preview" style="width: 30px; height: 30px; border-radius: 4px; background-color: <?= View::escape($accent) ?>; border: 1px solid #cbd5e1;"></div>
                                    <input type="text" id="accent_color_input" name="accent_color" value="<?= View::escape($accent) ?>" onchange="selectColor(this.value, null)" style="font-family: monospace; width: 100%;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="admin-actions" style="margin-top: 1rem; border-top: 1px solid #e2e8f0; padding-top: 1rem;">
                        <button type="button" onclick="saveAndReload()" class="admin-btn admin-btn-primary" style="font-size: 1rem; width: 100%; text-align:center;">
                            🔄 Rafraîchir l'aperçu complet
                        </button>
                    </div>
                </form>
            </section>
            
            <!-- PREVIEW COLUMN -->
            <section class="admin-card scroll-animate" style="flex: 2; min-width: 300px; padding: 0; overflow: hidden; display: flex; flex-direction: column; background: #e2e8f0;">
                <div style="padding: 0.5rem 1rem; background: #cbd5e1; font-family: monospace; font-size: 0.8rem; color: #475569; display: flex; align-items: center; gap: 0.5rem;">
                    <div style="width:10px; height:10px; border-radius:50%; background:#ef4444;"></div>
                    <div style="width:10px; height:10px; border-radius:50%; background:#f59e0b;"></div>
                    <div style="width:10px; height:10px; border-radius:50%; background:#10b981;"></div>
                    <span style="margin-left: 1rem;">Aperçu en direct (Portfolio Public)</span>
                </div>
                <iframe id="previewIframe" src="../index.php" style="width: 100%; flex: 1; border: none; background: #fff;"></iframe>
            </section>
            
        </div>
    </div>
</div>

<script>
    function selectMode(radioInput) {
        document.querySelectorAll('input[name="theme_mode"]').forEach(input => {
            input.parentElement.style.borderColor = '#e2e8f0';
        });
        radioInput.parentElement.style.borderColor = 'var(--accent)';
        autoSave();
    }

    function selectColor(hex, swatchElement) {
        document.getElementById('accent_color_input').value = hex;
        document.getElementById('color_preview').style.backgroundColor = hex;
        
        // Update selection UI
        document.querySelectorAll('.swatch-circle').forEach(circle => {
            circle.style.border = '3px solid transparent';
            circle.style.outline = 'none';
        });
        if(swatchElement) {
            const circle = swatchElement.querySelector('.swatch-circle');
            circle.style.border = '3px solid #fff';
            circle.style.outline = '2px solid ' + hex;
        }
        
        // Update admin accent variable visually for consistency
        document.documentElement.style.setProperty('--accent', hex);
        autoSave();
    }

    function autoSave() {
        const formData = new FormData(document.getElementById('themeForm'));
        // We use fetch to post the form data silently to theme.php
        fetch('theme.php', {
            method: 'POST',
            body: formData
        }).then(response => {
            if(response.ok) {
                // Reload the iframe to see changes immediately
                document.getElementById('previewIframe').contentWindow.location.reload();
            }
        }).catch(err => console.error("Erreur lors de la sauvegarde :", err));
    }
    
    function saveAndReload() {
        autoSave();
    }
</script>

<?php require __DIR__ . '/../shared/footer.php'; ?>
