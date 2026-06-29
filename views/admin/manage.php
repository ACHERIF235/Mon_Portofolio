<?php
$title = 'Admin Manage';
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
            <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; margin-bottom:1.5rem; flex-wrap:wrap;">
                <div>
                    <h2 style="margin-bottom:0.5rem;">📋 <?= View::escape($section['name']) ?></h2>
                    <p>Gérez les éléments de cette section : ajoutez, modifiez ou supprimez les entrées.</p>
                </div>
                <?php if ($type !== 'inbox'): ?>
                    <a class="admin-btn admin-btn-primary" href="edit.php?type=<?= urlencode($type) ?>">➕ Ajouter un élément</a>
                <?php endif; ?>
            </div>

            <?php if (empty($rows)): ?>
                <div style="padding:2rem; background:linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border-radius:1.25rem; text-align:center; border: 1px solid #e2e8f0;">
                    <p style="color:#64748b; margin:0; font-size:1rem;">
                        📭 Aucun élément pour le moment. 
                        <?php if ($type !== 'inbox'): ?>
                            <a href="edit.php?type=<?= urlencode($type) ?>" style="color: var(--accent); font-weight:700;">Créez votre premier élément</a>
                        <?php endif; ?>
                    </p>
                </div>
            <?php else: ?>
                <div style="overflow-x:auto; border-radius:1rem; border: 1px solid #e2e8f0;">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <?php foreach ($section['fields'] as $field): ?>
                                    <th><?= View::escape($field['label']) ?></th>
                                <?php endforeach; ?>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rows as $row): ?>
                                <?php $isUnread = ($type === 'inbox' && empty($row['is_read'])); ?>
                                <tr style="<?= $isUnread ? 'font-weight:bold; background:rgba(255,255,255,0.05);' : '' ?>">
                                    <td>
                                        <strong><?= View::escape((string)$row['id']) ?></strong>
                                        <?php if ($type === 'inbox'): ?>
                                            <br>
                                            <?php if (!empty($row['is_replied'])): ?>
                                                <span style="font-size:0.75rem; background:#10b981; color:#000; padding:0.1rem 0.4rem; border-radius:1rem;">Répondu</span>
                                            <?php elseif (!empty($row['is_read'])): ?>
                                                <span style="font-size:0.75rem; background:#64748b; color:#fff; padding:0.1rem 0.4rem; border-radius:1rem;">Lu</span>
                                            <?php else: ?>
                                                <span style="font-size:0.75rem; background:var(--accent); color:#000; padding:0.1rem 0.4rem; border-radius:1rem;">Non lu</span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                    <?php foreach ($section['fields'] as $field): ?>
                                        <td><?= View::escape((string)($row[$field['name']] ?? '')) ?></td>
                                    <?php endforeach; ?>
                                    <td>
                                        <div style="display:flex; gap:0.5rem; flex-wrap:wrap;">
                                            <?php if ($type !== 'inbox'): ?>
                                                <a class="admin-btn admin-btn-secondary" href="edit.php?type=<?= urlencode($type) ?>&id=<?= urlencode((string)$row['id']) ?>" style="padding:0.6rem 1rem; font-size:0.85rem;">✏️ Modifier</a>
                                            <?php else: ?>
                                                <button type="button" class="admin-btn admin-btn-primary" onclick="openReplyModal('<?= View::escape($row['email'] ?? '') ?>', <?= (int)$row['id'] ?>)" style="padding:0.6rem 1rem; font-size:0.85rem; color:#000;">✉️ Répondre</button>
                                                <form method="post" action="toggle_read.php" style="display:inline;">
                                                    <input type="hidden" name="csrf_token" value="<?= View::escape(generate_csrf_token()) ?>">
                                                    <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
                                                    <input type="hidden" name="status" value="<?= empty($row['is_read']) ? '1' : '0' ?>">
                                                    <button type="submit" class="admin-btn admin-btn-secondary" style="padding:0.6rem 1rem; font-size:0.85rem;">
                                                        <?= empty($row['is_read']) ? '👁️ Marquer lu' : '👀 Marquer non lu' ?>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                            <form method="post" action="delete.php" style="display:inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet élément ?');">
                                                <input type="hidden" name="csrf_token" value="<?= View::escape(generate_csrf_token()) ?>">
                                                <input type="hidden" name="type" value="<?= View::escape($type) ?>">
                                                <input type="hidden" name="id" value="<?= View::escape((string)$row['id']) ?>">
                                                <button type="submit" class="admin-btn admin-btn-danger" style="padding:0.6rem 1rem; font-size:0.85rem;">🗑️ Supprimer</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>
    </div>
</div>
<?php if ($type === 'inbox'): ?>
<div id="replyModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:1000; align-items:center; justify-content:center; backdrop-filter:blur(4px);">
    <div style="background:#0f172a; border:1px solid #334155; padding:2rem; border-radius:1.5rem; width:100%; max-width:500px; box-shadow:0 10px 25px rgba(0,0,0,0.5);">
        <h3 style="margin-top:0; margin-bottom:1.5rem; color:#fff;">✉️ Répondre au message</h3>
        <form method="post" action="reply.php">
            <input type="hidden" name="csrf_token" value="<?= View::escape(generate_csrf_token()) ?>">
            <input type="hidden" name="id" id="replyMessageId" value="">
            <div style="margin-bottom:1rem;">
                <label style="display:block; margin-bottom:0.5rem; color:#94a3b8; font-size:0.9rem; font-weight:bold;">Destinataire</label>
                <input type="email" id="replyEmail" name="email" readonly required style="width:100%; padding:0.8rem; background:rgba(0,0,0,0.2); border:1px solid #334155; border-radius:0.75rem; color:#94a3b8; cursor:not-allowed;">
            </div>
            <div style="margin-bottom:1rem;">
                <label style="display:block; margin-bottom:0.5rem; color:#94a3b8; font-size:0.9rem; font-weight:bold;">Sujet</label>
                <input type="text" name="subject" value="Réponse à votre message" required style="width:100%; padding:0.8rem; background:rgba(0,0,0,0.2); border:1px solid #334155; border-radius:0.75rem; color:#e2e8f0; outline:none;">
            </div>
            <div style="margin-bottom:1.5rem;">
                <label style="display:block; margin-bottom:0.5rem; color:#94a3b8; font-size:0.9rem; font-weight:bold;">Votre message</label>
                <textarea name="message" rows="6" required style="width:100%; padding:0.8rem; background:rgba(0,0,0,0.2); border:1px solid #334155; border-radius:0.75rem; color:#e2e8f0; resize:vertical; outline:none;"></textarea>
            </div>
            <div style="display:flex; justify-content:flex-end; gap:1rem;">
                <button type="button" onclick="document.getElementById('replyModal').style.display='none'" class="admin-btn admin-btn-secondary" style="padding:0.75rem 1.5rem;">Annuler</button>
                <button type="submit" class="admin-btn admin-btn-primary" style="padding:0.75rem 1.5rem; color:#000;">Envoyer la réponse</button>
            </div>
        </form>
    </div>
</div>
<script>
function openReplyModal(email, id) {
    document.getElementById('replyEmail').value = email;
    document.getElementById('replyMessageId').value = id;
    document.getElementById('replyModal').style.display = 'flex';
}
</script>
<?php endif; ?>
<?php require __DIR__ . '/../shared/footer.php'; ?>