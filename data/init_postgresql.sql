-- Schéma PostgreSQL pour Supabase
-- Converti depuis MySQL (data/init.sql)

-- Désactiver les triggers
SET session_replication_role = 'replica';

-- Table settings
CREATE TABLE IF NOT EXISTS settings (
    id SERIAL PRIMARY KEY,
    key VARCHAR(100) NOT NULL UNIQUE,
    value TEXT NOT NULL,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

-- Table admin_users
CREATE TABLE IF NOT EXISTS admin_users (
    id SERIAL PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

-- Table skills
CREATE TABLE IF NOT EXISTS skills (
    id SERIAL PRIMARY KEY,
    category VARCHAR(120) NOT NULL,
    name_fr VARCHAR(255) NOT NULL,
    name_en VARCHAR(255) NOT NULL,
    level VARCHAR(80) NOT NULL,
    order_index INTEGER NOT NULL DEFAULT 0,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

-- Table projects
CREATE TABLE IF NOT EXISTS projects (
    id SERIAL PRIMARY KEY,
    title_fr VARCHAR(255) NOT NULL,
    title_en VARCHAR(255) NOT NULL,
    description_fr TEXT NOT NULL,
    description_en TEXT NOT NULL,
    link VARCHAR(255),
    order_index INTEGER NOT NULL DEFAULT 0,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

-- Table education
CREATE TABLE IF NOT EXISTS education (
    id SERIAL PRIMARY KEY,
    title_fr VARCHAR(255) NOT NULL,
    title_en VARCHAR(255) NOT NULL,
    organization VARCHAR(255) NOT NULL,
    period VARCHAR(120) NOT NULL,
    description_fr TEXT NOT NULL,
    description_en TEXT NOT NULL,
    order_index INTEGER NOT NULL DEFAULT 0,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

-- Table associations
CREATE TABLE IF NOT EXISTS associations (
    id SERIAL PRIMARY KEY,
    title_fr VARCHAR(255) NOT NULL,
    title_en VARCHAR(255) NOT NULL,
    organization VARCHAR(255) NOT NULL,
    period VARCHAR(120) NOT NULL,
    description_fr TEXT NOT NULL,
    description_en TEXT NOT NULL,
    order_index INTEGER NOT NULL DEFAULT 0,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

-- Table languages
CREATE TABLE IF NOT EXISTS languages (
    id SERIAL PRIMARY KEY,
    name_fr VARCHAR(120) NOT NULL,
    name_en VARCHAR(120) NOT NULL,
    level VARCHAR(80) NOT NULL,
    order_index INTEGER NOT NULL DEFAULT 0,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

-- Table contacts
CREATE TABLE IF NOT EXISTS contacts (
    id SERIAL PRIMARY KEY,
    type VARCHAR(120) NOT NULL,
    label_fr VARCHAR(120) NOT NULL,
    label_en VARCHAR(120) NOT NULL,
    value VARCHAR(255) NOT NULL,
    order_index INTEGER NOT NULL DEFAULT 0,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

-- Table inbox_messages
CREATE TABLE IF NOT EXISTS inbox_messages (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    is_read BOOLEAN DEFAULT FALSE,
    is_replied BOOLEAN DEFAULT FALSE
);

-- Réactiver les triggers
SET session_replication_role = 'DEFAULT';

-- Données initiales
INSERT INTO settings (key, value) VALUES
('hero_name_fr', 'Acherif AHMAT ALIFA'),
('hero_name_en', 'Acherif AHMAT ALIFA'),
('hero_title_fr', 'Assistant informatique / Co-fondateur SahelTech Solutions'),
('hero_title_en', 'IT Assistant / Co-founder SahelTech Solutions'),
('hero_location_fr', 'N''Djamena, Tchad'),
('hero_location_en', 'N''Djamena, Chad'),
('hero_intro_fr', 'Spécialiste en Systèmes d''Information, j''allie compétences techniques et aptitudes pédagogiques pour l''accompagnement d''équipes terrain.'),
('hero_intro_en', 'Information systems specialist combining technical skills and teaching ability to support field teams.'),
('about_text_fr', 'Je gère le matériel informatique, forme les utilisateurs et supervise la collecte de données en conformité avec les normes établies. Co-fondateur de SahelTech Solutions, je développe des solutions SaaS, fintech et des outils pour PME africaines.'),
('about_text_en', 'I manage IT equipment, train users, and oversee data collection in compliance with standards. As co-founder of SahelTech Solutions, I develop SaaS, fintech, and tools for African SMEs.'),
('github_url', 'https://github.com/ACHERIF235'),
('contact_email', 'alifa.acherif1@ugb.edu.sn'),
('contact_phone', '+235 66 45 39 03'),
('accent_color', '#c9a227')
ON CONFLICT (key) DO NOTHING;

INSERT INTO skills (category, name_fr, name_en, level, order_index) VALUES
('Mobile', 'Flutter / Dart', 'Flutter / Dart', 'Avancé', 1),
('Backend', 'PHP, Python, R, C, C++', 'PHP, Python, R, C, C++', 'Intermédiaire', 2),
('Web', 'HTML5, CSS3', 'HTML5, CSS3', 'Avancé', 3),
('Bases de données', 'MySQL, PostgreSQL, SQLite', 'MySQL, PostgreSQL, SQLite', 'Intermédiaire', 4),
('IA & Data', 'Data Science, Machine Learning, IA générative', 'Data Science, Machine Learning, Generative AI', 'Intermédiaire', 5),
('Réseaux', 'Starlink, MikroTik, WiFi hotspot', 'Starlink, MikroTik, WiFi hotspot', 'Intermédiaire', 6);

INSERT INTO projects (title_fr, title_en, description_fr, description_en, link, order_index) VALUES
('CashFlow Chad', 'CashFlow Chad', 'Application mobile Flutter/SQLite/Riverpod pour agents mobile money au Tchad, avec interface bilingue, mode hors-ligne et gestion de licences.', 'Flutter/SQLite/Riverpod mobile app for mobile money agents in Chad, with bilingual interface, offline mode, and license management.', 'https://github.com/ACHERIF235/cadhflow-chad', 1),
('SahelTech Solutions', 'SahelTech Solutions', 'Startup tech à N''Djamena spécialisée en SaaS, cybersécurité et fintech pour PME.', 'Tech startup in N''Djamena specialized in SaaS, cybersecurity, and fintech for SMEs.', 'https://github.com/ACHERIF235', 2),
('Abatcha Family WiFi', 'Abatcha Family WiFi', 'Gestion d''un hotspot communautaire MikroTik + Starlink avec portail captif bilingue.', 'Management of a community MikroTik + Starlink WiFi hotspot with bilingual captive portal.', NULL, 3),
('Module Prompt Engineering', 'Prompt Engineering Module', 'Module interactif HTML en français sur les frameworks prompt pour l''Afrique francophone.', 'Interactive HTML module in French covering prompt engineering frameworks for francophone Africa.', NULL, 4);

INSERT INTO education (title_fr, title_en, organization, period, description_fr, description_en, order_index) VALUES
('Licence en Sciences Appliquées — Informatique', 'Bachelor of Applied Sciences — Computer Science', 'UFR de Science Appliquée à la Technologie, UGB', 'Décembre 2020 – Avril 2024', 'Études en systèmes d''information, programmation, réseaux et intelligence artificielle.', 'Studies in information systems, programming, networks, and artificial intelligence.', 1),
('Baccalauréat', 'High School Diploma', 'Lycée-Collège les Faucons, N''Djamena', 'Octobre 2019 – Août 2020', 'Formation générale et scientifique avant les études supérieures.', 'General and scientific education before higher studies.', 2),
('BEF', 'Fundamental Studies Certificate', 'Collège Privé Al-Nour, Am Timan', 'Octobre 2014 – Juillet 2015', 'Diplôme fondamental en sciences et mathématiques.', 'Fundamental diploma in science and mathematics.', 3);

INSERT INTO associations (title_fr, title_en, organization, period, description_fr, description_en, order_index) VALUES
('Secrétaire Général Adjoint', 'Deputy General Secretary', 'AMETS — Amicale des Étudiants Tchadiens à Saint-Louis', '2020 – 2021', 'Gestion des activités de l''association et support aux membres.', 'Managed association activities and supported members.', 1),
('Secrétaire Général', 'General Secretary', 'AMETS — Amicale des Étudiants Tchadiens à Saint-Louis', '2022 – 2023', 'Coordination des événements et communication interne.', 'Coordinated events and internal communication.', 2);

INSERT INTO languages (name_fr, name_en, level, order_index) VALUES
('Français', 'French', 'Courant', 1),
('Arabe', 'Arabic', 'Courant', 2),
('Anglais', 'English', 'Intermédiaire', 3);

INSERT INTO contacts (type, label_fr, label_en, value, order_index) VALUES
('email', 'Email', 'Email', 'alifa.acherif1@ugb.edu.sn', 1),
('phone', 'Téléphone', 'Phone', '+235 66 45 39 03', 2);

-- Créer l'administrateur par défaut
-- Le mot de passe 'Admin123!' sera hashé par l'application
INSERT INTO admin_users (email, password_hash, name) VALUES
('admin@portfolio.local', '$2y$10$placeholder_hash_to_be_updated', 'Admin Portfolio')
ON CONFLICT (email) DO NOTHING;
