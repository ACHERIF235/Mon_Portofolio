# Portfolio Web Project

Site web de portfolio personnel créé avec HTML, CSS, JavaScript, PHP et MySQL.

## Structure du projet

- `index.php` - page publique responsive
- `admin/` - espace d'administration pour modifier les contenus
- `models/` - modèles de données et accès à la base
- `controllers/` - contrôleurs MVC pour la logique métier
- `views/` - templates HTML séparés
- `core/` - fichiers PHP partagés pour la base de données, l'authentification et les utilitaires
- `assets/css/style.css` - styles visuels
- `assets/js/main.js` - comportement client
- `assets/uploads/` - dossier d'uploads pour la photo de profil et le CV
- `data/init.sql` - schéma MySQL et données initiales

## Installation locale

1. Installe XAMPP ou WAMP.
2. Copie ce projet dans `htdocs` ou `www`.
3. Crée une base de données `portfolio`.
4. Vérifie `config.php` et ajuste les paramètres MySQL si besoin.
5. Lance le serveur puis ouvre `http://localhost/portofolio/install.php` dans ton navigateur pour créer les tables et insérer les données initiales.
6. Ouvre ensuite `http://localhost/portofolio/`.

## Compte administrateur par défaut

- Email : `admin@portfolio.local`
- Mot de passe : `Admin123!`

## Admin

- Accède à `http://localhost/portofolio/admin/login.php`
- Tu pourras modifier les textes, les projets, les compétences, les formations, les associations, les langues, les contacts, la couleur du thème, la photo de profil et le CV.

## Notes

- Formulaire contact avec envoi d'email réel via PHP mail()
- Validation front-end ajoutée en JavaScript pour le formulaire de contact
- Pages administratives protégées avec 403 / 404 pour les accès non autorisés ou sections invalides

- Les images et documents sont stockés sous `assets/uploads/`.
- Le site propose un basculement `FR/EN`.
