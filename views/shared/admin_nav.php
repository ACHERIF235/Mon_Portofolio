<header class="admin-header">
    <div class="container">
        <div class="admin-header-top" style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
            <div style="display: flex; align-items: center; gap: 0.8rem;">
                <div style="font-size: 1.25rem; font-weight: 800; background: linear-gradient(135deg, var(--accent) 0%, #f59e0b 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">📊 Portfolio Admin</div>
                <span style="color: #e2e8f0; font-size: 0.85rem;">Bonjour, <?= View::escape($adminEmail ?? 'admin') ?></span>
            </div>
            <button id="adminMenuToggle" class="admin-menu-toggle" aria-label="Menu" style="background:none; border:none; color:#fff; font-size:1.5rem; cursor:pointer; display:none; padding: 0.5rem; outline:none;">
                ☰
            </button>
        </div>
        <nav class="admin-nav" id="adminNav">
            <a href="dashboard.php" title="Tableau de bord">📈 Tableau de bord</a>
            <a href="theme.php" title="Thème & Couleurs">🎨 Thème</a>
            <a href="settings.php" title="Paramètres">⚙️ Paramètres</a>
            <a href="profile.php" title="Mon Profil">👤 Mon Profil</a>
            <a href="manage.php?type=skills" title="Compétences">⭐ Compétences</a>
            <a href="manage.php?type=projects" title="Projets">🎯 Projets</a>
            <a href="manage.php?type=education" title="Formations">🎓 Formations</a>
            <a href="manage.php?type=associations" title="Associations">🤝 Associations</a>
            <a href="manage.php?type=languages" title="Langues">🌐 Langues</a>
            <?php
            $unreadMessages = 0;
            try {
                $unreadMessages = (int)(db_fetch('SELECT COUNT(*) as cnt FROM inbox_messages WHERE is_read = 0')['cnt'] ?? 0);
            } catch (Exception $e) {}
            ?>
            <a href="manage.php?type=contacts" title="Contacts">📧 Contacts</a>
            <a href="manage.php?type=inbox" title="Messages" style="display:flex; justify-content:space-between; align-items:center;">
                <span>💬 Messages</span>
                <?php if($unreadMessages > 0): ?>
                    <span style="background:var(--accent, #c9a227); color:#000; font-size:0.75rem; font-weight:bold; padding:0.1rem 0.5rem; border-radius:1rem; margin-left:0.5rem;"><?= $unreadMessages ?></span>
                <?php endif; ?>
            </a>
            <a href="logout.php" class="logout" title="Déconnexion">🚪 Déconnexion</a>
        </nav>
    </div>
</header>

<style>
    /* GARANTIE DU COMPORTEMENT MOBILE UNE BONNE FOIS POUR TOUTES */
    @media (max-width: 768px) {
        .admin-header {
            padding: 0.5rem 0 !important;
        }
        
        .admin-header .container {
            display: flex !important;
            flex-direction: row !important;
            justify-content: space-between !important;
            align-items: center !important;
            flex-wrap: wrap !important;
        }
        
        .admin-header-top {
            width: 100% !important;
        }

        #adminMenuToggle {
            display: block !important;
        }

        #adminNav {
            position: absolute !important;
            top: 100% !important;
            left: 0 !important;
            right: 0 !important;
            background: #0f172a !important;
            padding: 1.5rem !important;
            flex-direction: column !important;
            display: none !important; /* Cache fermement par defaut */
            gap: 0.8rem !important;
            box-shadow: 0 15px 30px rgba(0,0,0,0.5) !important;
            border-bottom: 1px solid rgba(255,255,255,0.1) !important;
            z-index: 9999 !important;
            width: 100% !important;
            margin-top: -0.5rem !important; /* Remonte le menu vers le haut */
        }

        #adminNav.show-nav {
            display: flex !important; /* Force l'affichage au clic */
        }
        
        #adminNav a {
            width: 100% !important;
            display: block !important;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. GESTION DU MENU MOBILE
        const toggle = document.getElementById('adminMenuToggle');
        const nav = document.getElementById('adminNav');
        
        if(toggle && nav) {
            toggle.addEventListener('click', function(e) {
                e.stopPropagation();
                nav.classList.toggle('show-nav');
            });
            
            // Ferme le menu si on clique en dehors
            document.addEventListener('click', function(e) {
                if(!nav.contains(e.target) && !toggle.contains(e.target) && nav.classList.contains('show-nav')) {
                    nav.classList.remove('show-nav');
                }
            });
        }

        // 2. ANIMATIONS AU SCROLL (Dashboard & Manage)
        const observerOptions = {
            threshold: 0.1, // Déclenche quand 10% de l'élément est visible
            rootMargin: "0px 0px -50px 0px"
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    // On arrête d'observer une fois l'animation jouée
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        // Cibler automatiquement toutes les cartes et lignes de tableau du panel admin
        const elementsToAnimate = document.querySelectorAll('.admin-stat-card, .admin-card, .form-container, .data-table tbody tr');
        
        elementsToAnimate.forEach((el, index) => {
            el.classList.add('scroll-animate');
            // Créer un effet "stagger" (cascade) basé sur la position
            el.style.transitionDelay = `${(index % 15) * 0.05}s`;
            observer.observe(el);
        });
    });
</script>
