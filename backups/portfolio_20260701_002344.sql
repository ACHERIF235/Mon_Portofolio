-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database: portfolio
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `admin_users`
--

DROP TABLE IF EXISTS `admin_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_users`
--

LOCK TABLES `admin_users` WRITE;
/*!40000 ALTER TABLE `admin_users` DISABLE KEYS */;
INSERT INTO `admin_users` VALUES (1,'admin@portfolio.local','$2y$10$wO5gspvagnbq5mKh5qft3.rq6ay0jvl8wzxKXecfd4tymdetx6r9S','Admin Portfolio');
/*!40000 ALTER TABLE `admin_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `associations`
--

DROP TABLE IF EXISTS `associations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `associations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title_fr` varchar(255) NOT NULL,
  `title_en` varchar(255) NOT NULL,
  `organization` varchar(255) NOT NULL,
  `period` varchar(120) NOT NULL,
  `description_fr` text NOT NULL,
  `description_en` text NOT NULL,
  `order_index` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `associations`
--

LOCK TABLES `associations` WRITE;
/*!40000 ALTER TABLE `associations` DISABLE KEYS */;
INSERT INTO `associations` VALUES (1,'Secrétaire Général Adjoint','Deputy General Secretary','AMETS — Amicale des Étudiants Tchadiens à Saint-Louis','2020 – 2021','Gestion des activités de l\'association et support aux membres.','Managed association activities and supported members.',1),(2,'Secrétaire Général','General Secretary','AMETS — Amicale des Étudiants Tchadiens à Saint-Louis','2022 – 2023','Coordination des événements et communication interne.','Coordinated events and internal communication.',2);
/*!40000 ALTER TABLE `associations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contacts`
--

DROP TABLE IF EXISTS `contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contacts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(120) NOT NULL,
  `label_fr` varchar(120) NOT NULL,
  `label_en` varchar(120) NOT NULL,
  `value` varchar(255) NOT NULL,
  `order_index` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contacts`
--

LOCK TABLES `contacts` WRITE;
/*!40000 ALTER TABLE `contacts` DISABLE KEYS */;
INSERT INTO `contacts` VALUES (1,'email','Email','Email','alifa.acherif1@ugb.edu.sn',1),(2,'phone','Téléphone','Phone','+235 66 45 39 03',2);
/*!40000 ALTER TABLE `contacts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `education`
--

DROP TABLE IF EXISTS `education`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `education` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title_fr` varchar(255) NOT NULL,
  `title_en` varchar(255) NOT NULL,
  `organization` varchar(255) NOT NULL,
  `period` varchar(120) NOT NULL,
  `description_fr` text NOT NULL,
  `description_en` text NOT NULL,
  `order_index` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `education`
--

LOCK TABLES `education` WRITE;
/*!40000 ALTER TABLE `education` DISABLE KEYS */;
INSERT INTO `education` VALUES (1,'Licence en Sciences Appliquées — Informatique','Bachelor of Applied Sciences — Computer Science','UFR de Science Appliquée à la Technologie, UGB','Décembre 2020 – Avril 2024','Études en systèmes d\'information, programmation, réseaux et intelligence artificielle.','Studies in information systems, programming, networks, and artificial intelligence.',1),(2,'Baccalauréat','High School Diploma','Lycée-Collège les Faucons, N\'Djamena','Octobre 2019 – Août 2020','Formation générale et scientifique avant les études supérieures.','General and scientific education before higher studies.',2),(3,'BEF','Fundamental Studies Certificate','Collège Privé Al-Nour, Am Timan','Octobre 2014 – Juillet 2015','Diplôme fondamental en sciences et mathématiques.','Fundamental diploma in science and mathematics.',3);
/*!40000 ALTER TABLE `education` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inbox_messages`
--

DROP TABLE IF EXISTS `inbox_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inbox_messages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` datetime NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `is_replied` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inbox_messages`
--

LOCK TABLES `inbox_messages` WRITE;
/*!40000 ALTER TABLE `inbox_messages` DISABLE KEYS */;
INSERT INTO `inbox_messages` VALUES (2,'Acherif Ahmat Alifa','mahamatatteib@mattlegal.sn','salut','2026-05-19 12:00:11',0,0),(3,'Acherif Ahmat Alifa','acherifalifa5@gmail.com','bonjour🤝','2026-06-29 15:56:18',1,0),(4,'Acherif Ahmat Alifa','acherifalifa5@gmail.com','bjr','2026-06-29 16:11:47',1,0),(5,'Issa  Mahamat','acherifahmatalifa4@gmail.com','c\'est nouveau je veux discuter','2026-06-29 16:18:33',1,0),(6,'ALi MAHAMAT','acherifalifa5@gmail.com','oui','2026-06-29 16:24:20',1,0);
/*!40000 ALTER TABLE `inbox_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `languages`
--

DROP TABLE IF EXISTS `languages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `languages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name_fr` varchar(120) NOT NULL,
  `name_en` varchar(120) NOT NULL,
  `level` varchar(80) NOT NULL,
  `order_index` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `languages`
--

LOCK TABLES `languages` WRITE;
/*!40000 ALTER TABLE `languages` DISABLE KEYS */;
INSERT INTO `languages` VALUES (1,'Français','French','Courant',1),(2,'Arabe','Arabic','Courant',2),(3,'Anglais','English','Intermédiaire',3);
/*!40000 ALTER TABLE `languages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `projects` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title_fr` varchar(255) NOT NULL,
  `title_en` varchar(255) NOT NULL,
  `description_fr` text NOT NULL,
  `description_en` text NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `order_index` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `projects`
--

LOCK TABLES `projects` WRITE;
/*!40000 ALTER TABLE `projects` DISABLE KEYS */;
INSERT INTO `projects` VALUES (1,'CashFlow Chad','CashFlow Chad','Application mobile Flutter/SQLite/Riverpod pour agents mobile money au Tchad, avec interface bilingue, mode hors-ligne et gestion de licences.','Flutter/SQLite/Riverpod mobile app for mobile money agents in Chad, with bilingual interface, offline mode, and license management.','https://github.com/ACHERIF235/cadhflow-chad',1),(2,'SahelTech Solutions','SahelTech Solutions','Startup tech à N\'Djamena spécialisée en SaaS, cybersécurité et fintech pour PME.','Tech startup in N\'Djamena specialized in SaaS, cybersecurity, and fintech for SMEs.','https://github.com/ACHERIF235',2),(3,'Abatcha Family WiFi','Abatcha Family WiFi','Gestion d\'un hotspot communautaire MikroTik + Starlink avec portail captif bilingue.','Management of a community MikroTik + Starlink WiFi hotspot with bilingual captive portal.',NULL,3),(4,'Module Prompt Engineering','Prompt Engineering Module','Module interactif HTML en français sur les frameworks prompt pour l\'Afrique francophone.','Interactive HTML module in French covering prompt engineering frameworks for francophone Africa.',NULL,4);
/*!40000 ALTER TABLE `projects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(100) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (1,'hero_name_fr','Acherif AHMAT ALIFA'),(2,'hero_name_en','Acherif AHMAT ALIFA'),(3,'hero_title_fr','informaticien'),(4,'hero_title_en','IT cumpter'),(5,'hero_location_fr','N\'Djamena, Tchad'),(6,'hero_location_en','N\'Djamena, Chad'),(7,'hero_intro_fr','Spécialiste en Systèmes d\'Information, j\'allie compétences techniques et aptitudes pédagogiques pour l\'accompagnement d\'équipes terrain.'),(8,'hero_intro_en','Information systems specialist combining technical skills and teaching ability to support field teams.'),(9,'about_text_fr','Je gère le matériel informatique, forme les utilisateurs et supervise la collecte de données en conformité avec les normes établies. Co-fondateur de SahelTech Solutions, je développe des solutions SaaS, fintech et des outils pour PME africaines.'),(10,'about_text_en','I manage IT equipment, train users, and oversee data collection in compliance with standards. As co-founder of SahelTech Solutions, I develop SaaS, fintech, and tools for African SMEs.'),(11,'github_url','https://github.com/ACHERIF235'),(12,'contact_email','alifa.acherif1@ugb.edu.sn'),(13,'contact_phone','+235 66 45 39 03'),(14,'accent_color','#c9a227'),(15,'profile_photo','1779185195-b89772a6c625d41b.png'),(16,'theme_mode','dark');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `skills`
--

DROP TABLE IF EXISTS `skills`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `skills` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category` varchar(120) NOT NULL,
  `name_fr` varchar(255) NOT NULL,
  `name_en` varchar(255) NOT NULL,
  `level` varchar(80) NOT NULL,
  `order_index` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `skills`
--

LOCK TABLES `skills` WRITE;
/*!40000 ALTER TABLE `skills` DISABLE KEYS */;
INSERT INTO `skills` VALUES (1,'Mobile','Flutter / Dart','Flutter / Dart','Avancé',1),(2,'Backend','PHP, Python, R, C, C++','PHP, Python, R, C, C++','Intermédiaire',2),(3,'Web','HTML5, CSS3','HTML5, CSS3','Avancé',3),(4,'Bases de données','MySQL, PostgreSQL, SQLite','MySQL, PostgreSQL, SQLite','Intermédiaire',4),(5,'IA & Data','Data Science, Machine Learning, IA générative','Data Science, Machine Learning, Generative AI','Intermédiaire',5),(6,'Réseaux','Starlink, MikroTik, WiFi hotspot','Starlink, MikroTik, WiFi hotspot','Intermédiaire',6);
/*!40000 ALTER TABLE `skills` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-07-01  0:23:45
