# Portfolio Web Project 🚀

Site web de portfolio personnel "Serverless" créé avec HTML, CSS, JavaScript, PHP 8 et conçu pour être déployé sur **Vercel** avec une base de données **Supabase** (PostgreSQL).

## 🌟 Nouvelles fonctionnalités (Architecture Serverless)

- **Déploiement Vercel** : Le projet est 100% compatible avec Vercel (sans serveur Apache/Nginx dédié).
- **Base de données Supabase** : Utilisation de Supabase (PostgreSQL via API REST) pour stocker toutes les données (auparavant MySQL).
- **Stockage Cloud (Supabase Storage)** : Les fichiers uploadés (photos, CV) sont envoyés directement dans un bucket public sur Supabase, car Vercel est stateless.
- **Authentification JWT** : Le panneau d'administration utilise désormais des JSON Web Tokens sécurisés stockés dans des cookies au lieu des sessions natives PHP (`$_SESSION`), ce qui le rend compatible avec le Serverless.
- **Envoi d'e-mails via Resend** : Intégration de l'API Resend pour l'envoi d'e-mails depuis le formulaire de contact, garantissant une délivrabilité parfaite depuis Vercel.

## 📂 Structure du projet

- `index.php` - page publique responsive (Entry point)
- `admin/` - espace d'administration pour modifier les contenus
- `models/` - modèles de données et accès à la base
- `controllers/` - contrôleurs MVC pour la logique métier
- `views/` - templates HTML séparés
- `core/` - cœur de l'application (auth JWT, DatabaseFactory, utilitaires)
- `assets/` - CSS, JS, images statiques
- `vercel.json` - configuration officielle pour le déploiement sur Vercel
- `setup_supabase_v2.php` & `data/init_postgresql.sql` - scripts d'initialisation du schéma PostgreSQL

## 🚀 Déploiement sur Vercel

1. Assurez-vous d'avoir un compte **Vercel** et d'y lier votre dépôt GitHub.
2. Dans les paramètres de votre projet sur Vercel, allez dans l'onglet **Environment Variables** et ajoutez :
   - `DB_TYPE` = `supabase`
   - `SUPABASE_URL` = `https://votre-projet.supabase.co`
   - `SUPABASE_KEY` = `votre-cle-anon-publique`
   - `SUPABASE_AUTH_TOKEN` = `votre-cle-secrete`
   - `RESEND_API_KEY` = `re_votre_cle_resend` (Pour les e-mails)
3. C'est tout ! Vercel détectera `vercel.json` et déploiera le site automatiquement.
4. **Important** : Allez sur le Dashboard Supabase, dans la section **Storage**, et créez un bucket nommé `uploads`. Réglez-le sur **Public**.

## 💻 Installation locale (Mode développement)

Le code inclut toujours une compatibilité pour un fonctionnement 100% local (XAMPP/WAMP) si nécessaire.

1. Installez XAMPP ou WAMP.
2. Copiez ce projet dans `htdocs` ou `www`.
3. Renommez le fichier `.env.example` en `.env` ou configurez le fichier `config.php` pour y mettre vos variables.
4. Lancez le serveur puis ouvrez `http://localhost/portofolio/setup_supabase_v2.php` pour installer les tables (si vous testez Supabase) ou `install.php` (si vous utilisez MySQL).
5. Ouvre ensuite `http://localhost/portofolio/`.

## 🔐 Compte administrateur par défaut

- **Email** : `admin@portfolio.local`
- **Mot de passe** : `Admin123!`

## ⚙️ Administration

- Accès via `/admin/login.php` (ou `http://localhost/portofolio/admin/login.php` en local).
- Permet de gérer vos projets, vos compétences, vos informations personnelles, vos thèmes de couleurs, et surtout de **lire et répondre aux messages de contact** reçus !

## 🌍 Langues
- Le site propose un basculement complet Français / Anglais (FR/EN) sur le front-end.
