SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS `settings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(100) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `admin_users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `skills` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `category` varchar(120) NOT NULL,
  `name_fr` varchar(255) NOT NULL,
  `name_en` varchar(255) NOT NULL,
  `level` varchar(80) NOT NULL,
  `order_index` int unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `projects` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title_fr` varchar(255) NOT NULL,
  `title_en` varchar(255) NOT NULL,
  `description_fr` text NOT NULL,
  `description_en` text NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `order_index` int unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `education` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title_fr` varchar(255) NOT NULL,
  `title_en` varchar(255) NOT NULL,
  `organization` varchar(255) NOT NULL,
  `period` varchar(120) NOT NULL,
  `description_fr` text NOT NULL,
  `description_en` text NOT NULL,
  `order_index` int unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `associations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title_fr` varchar(255) NOT NULL,
  `title_en` varchar(255) NOT NULL,
  `organization` varchar(255) NOT NULL,
  `period` varchar(120) NOT NULL,
  `description_fr` text NOT NULL,
  `description_en` text NOT NULL,
  `order_index` int unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `languages` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name_fr` varchar(120) NOT NULL,
  `name_en` varchar(120) NOT NULL,
  `level` varchar(80) NOT NULL,
  `order_index` int unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `contacts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(120) NOT NULL,
  `label_fr` varchar(120) NOT NULL,
  `label_en` varchar(120) NOT NULL,
  `value` varchar(255) NOT NULL,
  `order_index` int unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `inbox_messages` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` datetime NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `is_replied` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `settings` (`key`, `value`) VALUES
('hero_name_fr', 'Acherif AHMAT ALIFA'),
('hero_name_en', 'Acherif AHMAT ALIFA'),
('hero_title_fr', 'Assistant informatique / Co-fondateur SahelTech Solutions'),
('hero_title_en', 'IT Assistant / Co-founder SahelTech Solutions'),
('hero_location_fr', 'N\'Djamena, Tchad'),
('hero_location_en', 'N\'Djamena, Chad'),
('hero_intro_fr', 'Spécialiste en Systèmes d\'Information, j\'allie compétences techniques et aptitudes pédagogiques pour l\'accompagnement d\'équipes terrain.'),
('hero_intro_en', 'Information systems specialist combining technical skills and teaching ability to support field teams.'),
('about_text_fr', 'Je gère le matériel informatique, forme les utilisateurs et supervise la collecte de données en conformité avec les normes établies. Co-fondateur de SahelTech Solutions, je développe des solutions SaaS, fintech et des outils pour PME africaines.'),
('about_text_en', 'I manage IT equipment, train users, and oversee data collection in compliance with standards. As co-founder of SahelTech Solutions, I develop SaaS, fintech, and tools for African SMEs.'),
('github_url', 'https://github.com/ACHERIF235'),
('contact_email', 'alifa.acherif1@ugb.edu.sn'),
('contact_phone', '+235 66 45 39 03'),
('accent_color', '#c9a227');

INSERT INTO `skills` (`category`, `name_fr`, `name_en`, `level`, `order_index`) VALUES
('Mobile', 'Flutter / Dart', 'Flutter / Dart', 'Avancé', 1),
('Backend', 'PHP, Python, R, C, C++', 'PHP, Python, R, C, C++', 'Intermédiaire', 2),
('Web', 'HTML5, CSS3', 'HTML5, CSS3', 'Avancé', 3),
('Bases de données', 'MySQL, PostgreSQL, SQLite', 'MySQL, PostgreSQL, SQLite', 'Intermédiaire', 4),
('IA & Data', 'Data Science, Machine Learning, IA générative', 'Data Science, Machine Learning, Generative AI', 'Intermédiaire', 5),
('Réseaux', 'Starlink, MikroTik, WiFi hotspot', 'Starlink, MikroTik, WiFi hotspot', 'Intermédiaire', 6);

INSERT INTO `projects` (`title_fr`, `title_en`, `description_fr`, `description_en`, `link`, `order_index`) VALUES
('CashFlow Chad', 'CashFlow Chad', 'Application mobile Flutter/SQLite/Riverpod pour agents mobile money au Tchad, avec interface bilingue, mode hors-ligne et gestion de licences.', 'Flutter/SQLite/Riverpod mobile app for mobile money agents in Chad, with bilingual interface, offline mode, and license management.', 'https://github.com/ACHERIF235/cadhflow-chad', 1),
('SahelTech Solutions', 'SahelTech Solutions', 'Startup tech à N\'Djamena spécialisée en SaaS, cybersécurité et fintech pour PME.', 'Tech startup in N\'Djamena specialized in SaaS, cybersecurity, and fintech for SMEs.', 'https://github.com/ACHERIF235', 2),
('Abatcha Family WiFi', 'Abatcha Family WiFi', 'Gestion d\'un hotspot communautaire MikroTik + Starlink avec portail captif bilingue.', 'Management of a community MikroTik + Starlink WiFi hotspot with bilingual captive portal.', NULL, 3),
('Module Prompt Engineering', 'Prompt Engineering Module', 'Module interactif HTML en français sur les frameworks prompt pour l\'Afrique francophone.', 'Interactive HTML module in French covering prompt engineering frameworks for francophone Africa.', NULL, 4);

INSERT INTO `education` (`title_fr`, `title_en`, `organization`, `period`, `description_fr`, `description_en`, `order_index`) VALUES
('Licence en Sciences Appliquées — Informatique', 'Bachelor of Applied Sciences — Computer Science', 'UFR de Science Appliquée à la Technologie, UGB', 'Décembre 2020 – Avril 2024', 'Études en systèmes d\'information, programmation, réseaux et intelligence artificielle.', 'Studies in information systems, programming, networks, and artificial intelligence.', 1),
('Baccalauréat', 'High School Diploma', 'Lycée-Collège les Faucons, N\'Djamena', 'Octobre 2019 – Août 2020', 'Formation générale et scientifique avant les études supérieures.', 'General and scientific education before higher studies.', 2),
('BEF', 'Fundamental Studies Certificate', 'Collège Privé Al-Nour, Am Timan', 'Octobre 2014 – Juillet 2015', 'Diplôme fondamental en sciences et mathématiques.', 'Fundamental diploma in science and mathematics.', 3);

INSERT INTO `associations` (`title_fr`, `title_en`, `organization`, `period`, `description_fr`, `description_en`, `order_index`) VALUES
('Secrétaire Général Adjoint', 'Deputy General Secretary', 'AMETS — Amicale des Étudiants Tchadiens à Saint-Louis', '2020 – 2021', 'Gestion des activités de l\'association et support aux membres.', 'Managed association activities and supported members.', 1),
('Secrétaire Général', 'General Secretary', 'AMETS — Amicale des Étudiants Tchadiens à Saint-Louis', '2022 – 2023', 'Coordination des événements et communication interne.', 'Coordinated events and internal communication.', 2);

INSERT INTO `languages` (`name_fr`, `name_en`, `level`, `order_index`) VALUES
('Français', 'French', 'Courant', 1),
('Arabe', 'Arabic', 'Courant', 2),
('Anglais', 'English', 'Intermédiaire', 3);

INSERT INTO `contacts` (`type`, `label_fr`, `label_en`, `value`, `order_index`) VALUES
('email', 'Email', 'Email', 'alifa.acherif1@ugb.edu.sn', 1),
('phone', 'Téléphone', 'Phone', '+235 66 45 39 03', 2);

SET FOREIGN_KEY_CHECKS = 1;
