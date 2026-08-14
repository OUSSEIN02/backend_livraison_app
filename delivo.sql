-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : ven. 14 août 2026 à 09:09
-- Version du serveur : 8.3.0
-- Version de PHP : 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `delivo`
--

-- --------------------------------------------------------

--
-- Structure de la table `cache`
--

DROP TABLE IF EXISTS `cache`;
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel-cache-otp_satar@gmail.com', 's:6:\"837859\";', 1786003882),
('laravel-cache-otp_attempts_satar@gmail.com', 'i:1;', 1786003882),
('laravel-cache-email_verified_tchaboueassana@gmail.com', 'b:1;', 1786037192);

-- --------------------------------------------------------

--
-- Structure de la table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `delivery_locations`
--

DROP TABLE IF EXISTS `delivery_locations`;
CREATE TABLE IF NOT EXISTS `delivery_locations` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` bigint UNSIGNED NOT NULL,
  `livreur_id` bigint UNSIGNED NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `heading` decimal(6,2) NOT NULL DEFAULT '0.00',
  `speed` decimal(6,2) NOT NULL DEFAULT '0.00',
  `accuracy` decimal(6,2) NOT NULL DEFAULT '0.00',
  `phase` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `recorded_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `delivery_locations_order_id_recorded_at_index` (`order_id`,`recorded_at`),
  KEY `delivery_locations_livreur_id_index` (`livreur_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `invoices`
--

DROP TABLE IF EXISTS `invoices`;
CREATE TABLE IF NOT EXISTS `invoices` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `invoice_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en_attente',
  `issued_at` timestamp NULL DEFAULT NULL,
  `pdf_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoices_invoice_number_unique` (`invoice_number`),
  KEY `invoices_user_id_foreign` (`user_id`),
  KEY `invoices_order_id_foreign` (`order_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `invoices`
--

INSERT INTO `invoices` (`id`, `invoice_number`, `user_id`, `order_id`, `amount`, `status`, `issued_at`, `pdf_path`, `created_at`, `updated_at`) VALUES
(1, 'FAC-2026-0001', 9, 3, 0.00, 'payee', '2026-08-08 20:54:18', 'invoices/FAC-2026-0001.pdf', '2026-08-08 20:54:18', '2026-08-08 20:54:18');

-- --------------------------------------------------------

--
-- Structure de la table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `litiges`
--

DROP TABLE IF EXISTS `litiges`;
CREATE TABLE IF NOT EXISTS `litiges` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` bigint UNSIGNED NOT NULL,
  `seller_id` bigint UNSIGNED NOT NULL,
  `livreur_id` bigint UNSIGNED DEFAULT NULL,
  `type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Ex: Colis non reçu, Produit endommagé, Retard, etc.',
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en_attente' COMMENT 'en_attente, en_cours, resolu',
  `priorite` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'moyenne' COMMENT 'haute, moyenne, basse',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `litiges_order_id_foreign` (`order_id`),
  KEY `litiges_seller_id_foreign` (`seller_id`),
  KEY `litiges_livreur_id_foreign` (`livreur_id`),
  KEY `litiges_status_index` (`status`),
  KEY `litiges_priorite_index` (`priorite`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `livreurs`
--

DROP TABLE IF EXISTS `livreurs`;
CREATE TABLE IF NOT EXISTS `livreurs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `numero_plaque` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Numéro d''immatriculation du véhicule',
  `etat_moto` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'État du véhicule: excellent, bon, moyen, correct',
  `experience` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Expérience: oui ou non',
  `zones_livraison` json NOT NULL COMMENT 'Tableau des zones de livraison sélectionnées',
  `photo_identite_path` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Chemin de la photo selfie / identité',
  `photo_piece_identite_path` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Chemin de la photo de la pièce d''identité',
  `photo_moto_path` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Chemin de la photo du véhicule',
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en_attente' COMMENT 'Statut de validation du dossier',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `livreurs_user_id_foreign` (`user_id`),
  KEY `livreurs_status_index` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `livreurs`
--

INSERT INTO `livreurs` (`id`, `user_id`, `numero_plaque`, `etat_moto`, `experience`, `zones_livraison`, `photo_identite_path`, `photo_piece_identite_path`, `photo_moto_path`, `status`, `created_at`, `updated_at`, `is_read`) VALUES
(1, 10, '233', 'moyen', 'oui', '\"[\\\"Nyanga (Tchibanga)\\\",\\\"Ogoou\\\\u00e9-Ivindo (Makokou)\\\"]\"', 'livreurs/identite/6a77a72a7e26f.webp', 'livreurs/pieces/6a77a72b05cc3.webp', 'livreurs/motos/6a77a72b8a36e.webp', 'valide', '2026-08-08 21:01:16', '2026-08-08 21:09:58', 0),
(2, 12, '2333', 'bon', 'oui', '\"[\\\"Nyanga (Tchibanga)\\\",\\\"Ogoou\\\\u00e9-Ivindo (Makokou)\\\"]\"', 'livreurs/identite/6a7c50fd3a757.webp', 'livreurs/pieces/6a7c50fd5959d.webp', 'livreurs/motos/6a7c50fd5dc88.webp', 'en_attente', '2026-08-12 09:54:53', '2026-08-12 09:54:53', 0);

-- --------------------------------------------------------

--
-- Structure de la table `livreur_locations`
--

DROP TABLE IF EXISTS `livreur_locations`;
CREATE TABLE IF NOT EXISTS `livreur_locations` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `speed` decimal(8,2) DEFAULT NULL,
  `heading` decimal(5,2) DEFAULT NULL,
  `accuracy` decimal(8,2) DEFAULT NULL,
  `status` enum('available','busy','offline') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'available',
  `last_seen_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `livreur_locations_user_id_last_seen_at_index` (`user_id`,`last_seen_at`),
  KEY `livreur_locations_latitude_longitude_index` (`latitude`,`longitude`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `livreur_locations`
--

INSERT INTO `livreur_locations` (`id`, `user_id`, `latitude`, `longitude`, `speed`, `heading`, `accuracy`, `status`, `last_seen_at`, `created_at`, `updated_at`) VALUES
(1, 10, 6.42763980, 2.34518170, 0.00, 0.00, 3.30, 'available', '2026-08-13 17:01:56', '2026-08-11 16:37:56', '2026-08-13 17:01:56'),
(2, 12, 6.42757790, 2.34525730, 0.00, 0.00, 20.20, 'available', '2026-08-12 11:11:58', '2026-08-12 09:56:22', '2026-08-12 11:11:58'),
(3, 11, 6.42763850, 2.34525490, 7.82, 343.91, 46.58, 'available', '2026-08-13 16:54:04', '2026-08-12 11:12:58', '2026-08-13 16:54:04');

-- --------------------------------------------------------

--
-- Structure de la table `messages`
--

DROP TABLE IF EXISTS `messages`;
CREATE TABLE IF NOT EXISTS `messages` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `sender_id` bigint UNSIGNED NOT NULL,
  `receiver_id` bigint UNSIGNED NOT NULL,
  `delivery_id` bigint UNSIGNED DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `messages_receiver_id_foreign` (`receiver_id`),
  KEY `messages_delivery_id_foreign` (`delivery_id`),
  KEY `messages_sender_id_receiver_id_created_at_index` (`sender_id`,`receiver_id`,`created_at`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_07_17_165253_create_sellers_table', 1),
(5, '2026_07_17_165334_create_delivery_persons_table', 1),
(6, '2026_07_17_165416_create_clients_table', 1),
(7, '2026_07_17_165447_create_orders_table', 1),
(8, '2026_07_17_165530_create_deliveries_table', 1),
(9, '2026_07_17_165603_create_payments_table', 1),
(10, '2026_07_17_165633_create_ratings_table', 1),
(11, '2026_07_17_165701_create_messages_table', 1),
(12, '2026_07_17_165727_create_subscriptions_table', 1),
(13, '2026_07_17_165756_create_transactions_table', 1),
(14, '2026_07_30_140442_create_sellers_table', 2),
(15, '2026_07_31_061352_create_permissions_table', 3),
(16, '2026_07_31_061548_create_roles_table', 4),
(17, '2026_07_31_061902_create_role_user_table', 5),
(18, '2026_07_31_062036_create_permission_role_table', 6),
(19, '2019_12_14_000001_create_personal_access_tokens_table', 7),
(20, '2026_08_01_094959_create_orders_table', 8),
(21, '2026_08_02_070458_create_invoices_table', 9),
(22, '2026_08_02_190626_create_livreurs_table', 10),
(23, '2026_08_03_200353_create_litiges_table', 11),
(24, '2026_08_09_204509_create_zones_table', 12),
(25, '2026_08_10_100005_create_order_delivery_requests_table', 13),
(26, '2026_08_11_101021_create_livreur_locations_table', 14),
(27, '2026_08_13_161609_create_delivery_locations_table', 15);

-- --------------------------------------------------------

--
-- Structure de la table `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `pickup_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pickup_address` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pickup_lat` decimal(10,8) DEFAULT NULL,
  `pickup_lng` decimal(11,8) DEFAULT NULL,
  `dropoff_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dropoff_address` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dropoff_lat` decimal(10,8) DEFAULT NULL,
  `dropoff_lng` decimal(11,8) DEFAULT NULL,
  `weight` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_fragile` tinyint(1) NOT NULL DEFAULT '0',
  `declared_value` decimal(10,2) NOT NULL DEFAULT '0.00',
  `package_photo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delivery_type` enum('immediat','programme') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'immediat',
  `scheduled_date` timestamp NULL DEFAULT NULL,
  `instructions` text COLLATE utf8mb4_unicode_ci,
  `status` enum('en_attente','assignee','en_cours','livree','annulee','litige','payee') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en_attente',
  `total_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `picked_up_at` timestamp NULL DEFAULT NULL,
  `delivered_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `tarif_base` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tarif_km_applied` decimal(8,2) NOT NULL DEFAULT '0.00',
  `assignation_status` enum('en_attente_recherche','recherche_en_cours','elargissement','assignee','echec_assignation') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en_attente_recherche',
  `rayon_recherche_km` int NOT NULL DEFAULT '2',
  `tentatives` int NOT NULL DEFAULT '0',
  `zone_id` bigint UNSIGNED DEFAULT NULL,
  `livreur_id` bigint UNSIGNED DEFAULT NULL,
  `distance_km` decimal(10,2) DEFAULT NULL,
  `last_lat` decimal(10,8) DEFAULT NULL,
  `last_lng` decimal(11,8) DEFAULT NULL,
  `last_heading` decimal(6,2) DEFAULT NULL,
  `last_speed` decimal(6,2) DEFAULT NULL,
  `last_location_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `orders_user_id_foreign` (`user_id`),
  KEY `orders_status_index` (`status`),
  KEY `orders_delivery_type_index` (`delivery_type`),
  KEY `orders_pickup_lat_pickup_lng_index` (`pickup_lat`,`pickup_lng`),
  KEY `orders_zone_id_foreign` (`zone_id`),
  KEY `orders_livreur_id_foreign` (`livreur_id`)
) ENGINE=MyISAM AUTO_INCREMENT=78 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `pickup_name`, `pickup_address`, `pickup_lat`, `pickup_lng`, `dropoff_name`, `dropoff_address`, `dropoff_lat`, `dropoff_lng`, `weight`, `is_fragile`, `declared_value`, `package_photo`, `delivery_type`, `scheduled_date`, `instructions`, `status`, `total_amount`, `picked_up_at`, `delivered_at`, `created_at`, `updated_at`, `deleted_at`, `tarif_base`, `tarif_km_applied`, `assignation_status`, `rayon_recherche_km`, `tentatives`, `zone_id`, `livreur_id`, `distance_km`, `last_lat`, `last_lng`, `last_heading`, `last_speed`, `last_location_at`) VALUES
(1, 9, 'CC5R+447', 'CC5R+447, Libreville, Gabon', 0.40779720, 9.44028330, 'clautaire', '9H9P+4C3, Franceville, Gabon', -1.63223010, 13.58605830, '25', 1, 25000.00, 'orders/package_photos/6a76e3df36a8f.webp', 'immediat', NULL, 'Je veux que quand tu arrives la bas que tu me fasses signe et je te dirai comment procéder ensuite.', 'en_attente', 0.00, NULL, NULL, '2026-08-08 07:07:59', '2026-08-08 07:07:59', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 9, 'Avenue Codje Tovalou Quénum', 'Avenue Codje Tovalou Quénum, 7ème Arrondissement, Cotonou, Littoral, Bénin', 0.41305963, 9.47690226, 'sossou', 'Quartier Sèmè, Awhanléko, Zoca, Abomey-Calavi, Atlantique, Bénin', 6.45386370, 2.35424500, '25', 1, 45000.00, 'orders/package_photos/6a7706eb6a1c2.webp', 'immediat', NULL, 'je voudrais que tu m\'appelles dès que tu récupères le colis', 'payee', 0.00, NULL, NULL, '2026-08-08 09:37:31', '2026-08-08 20:21:51', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 9, 'FC46+R38', 'FC46+R38, Libreville, Gabon', 0.45702960, 9.41021530, 'kossi jacques', '185, Rue du pont Pirah, Libreville, Gabon', 0.38324180, 9.44670100, '12', 1, 12000.00, 'orders/package_photos/6a779fde57fd0.webp', 'immediat', NULL, 'JE VOUDRAIS que tu me fasses signe des que tu recupères le colis', 'payee', 0.00, NULL, NULL, '2026-08-08 20:30:06', '2026-08-08 20:30:18', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 9, 'calavi', 'Quartier Sèmè, Awhanléko, Zoca, Abomey-Calavi, Atlantique, Bénin', 6.45386370, 2.35424500, 'sosou', 'Avenue Codje Tovalou Quénum, 7ème Arrondissement, Cotonou, Littoral, Bénin', 6.36769530, 2.42525070, '25', 0, 2000.00, 'orders/package_photos/6a7a0faba09f7.webp', 'immediat', NULL, 'je voudrais que tu me fasses signes une fois au lieu de récupération', 'en_attente', 0.00, NULL, NULL, '2026-08-10 16:51:40', '2026-08-10 16:51:40', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(5, 9, 'Zogbadjè', 'Zogbadjè, Abomey-Calavi, Atlantique, Bénin', 6.42362800, 2.33479140, 'clautaire', 'Parana, Rue de l\'espoire, Tokpa-Zoungo-Nord, Seminaire, Abomey-Calavi, Atlantique, Bénin', 6.43145910, 2.33130390, '2.4', 1, 2500.00, 'orders/package_photos/6a7a1108d5eb9.webp', 'immediat', NULL, 'je voudrais que tu me fasses signe dès que tu arrives au lieu de récupération.', 'en_attente', 0.00, NULL, NULL, '2026-08-10 16:57:29', '2026-08-10 16:57:29', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(6, 9, 'Quartier Sèmè', 'Zogbadjè, Abomey-Calavi, Atlantique, Bénin', 6.42362800, 2.33479140, 'issa', 'Carrefour Abattoir, Tokplégbé, 1er Arrondissement, Cotonou, Littoral, Bénin', 6.36893950, 2.48531280, '25kg', 1, 5000.00, 'orders/package_photos/6a7a3cf523ab1.webp', 'immediat', NULL, 'je voudrais que dès que tu arrives au client que tu me fasses signe', 'en_attente', 0.00, NULL, NULL, '2026-08-10 20:04:53', '2026-08-10 20:04:53', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(7, 9, 'Quartier Sèmè', 'Zogbadjè, Abomey-Calavi, Atlantique, Bénin', 6.42362800, 2.33479140, 'issa', 'Carrefour Abattoir, Tokplégbé, 1er Arrondissement, Cotonou, Littoral, Bénin', 6.36893950, 2.48531280, '25kg', 1, 5000.00, 'orders/package_photos/6a7a3d050500a.webp', 'immediat', NULL, 'je voudrais que dès que tu arrives au client que tu me fasses signe', 'en_attente', 0.00, NULL, NULL, '2026-08-10 20:05:09', '2026-08-10 20:05:09', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(8, 9, 'Quartier Sèmè', 'Zogbadjè, Abomey-Calavi, Atlantique, Bénin', 6.42362800, 2.33479140, 'issa', 'Carrefour Abattoir, Tokplégbé, 1er Arrondissement, Cotonou, Littoral, Bénin', 6.36893950, 2.48531280, '25kg', 1, 5000.00, 'orders/package_photos/6a7a3d224387c.webp', 'immediat', NULL, 'je voudrais que dès que tu arrives au client que tu me fasses signe', 'en_attente', 0.00, NULL, NULL, '2026-08-10 20:05:38', '2026-08-10 20:05:38', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(9, 9, 'Quartier Sèmè', 'Zogbadjè, Abomey-Calavi, Atlantique, Bénin', 6.42362800, 2.33479140, 'issa', 'Carrefour Abattoir, Tokplégbé, 1er Arrondissement, Cotonou, Littoral, Bénin', 6.36893950, 2.48531280, '25kg', 1, 5000.00, 'orders/package_photos/6a7a3db9666f6.webp', 'immediat', NULL, 'je voudrais que dès que tu arrives au client que tu me fasses signe', 'en_attente', 0.00, NULL, NULL, '2026-08-10 20:08:09', '2026-08-10 20:08:09', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(10, 9, 'Quartier Sèmè', 'Zogbadjè, Abomey-Calavi, Atlantique, Bénin', 6.42362800, 2.33479140, 'issa', 'Carrefour Abattoir, Tokplégbé, 1er Arrondissement, Cotonou, Littoral, Bénin', 6.36893950, 2.48531280, '25kg', 1, 5000.00, 'orders/package_photos/6a7a3de6799cc.webp', 'immediat', NULL, 'je voudrais que dès que tu arrives au client que tu me fasses signe', 'en_attente', 0.00, NULL, NULL, '2026-08-10 20:08:54', '2026-08-10 20:08:54', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(11, 9, 'Quartier Sèmè', 'Zogbadjè, Abomey-Calavi, Atlantique, Bénin', 6.42362800, 2.33479140, 'issa', 'Carrefour Abattoir, Tokplégbé, 1er Arrondissement, Cotonou, Littoral, Bénin', 6.36893950, 2.48531280, '25kg', 1, 5000.00, 'orders/package_photos/6a7a3f470efdd.webp', 'immediat', NULL, 'je voudrais que dès que tu arrives au client que tu me fasses signe', 'en_attente', 0.00, NULL, NULL, '2026-08-10 20:14:47', '2026-08-10 20:14:47', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(12, 9, 'Quartier Sèmè', 'Zogbadjè, Abomey-Calavi, Atlantique, Bénin', 6.42362800, 2.33479140, 'issa', 'Carrefour Abattoir, Tokplégbé, 1er Arrondissement, Cotonou, Littoral, Bénin', 6.36893950, 2.48531280, '25kg', 1, 5000.00, 'orders/package_photos/6a7a4213ae988.webp', 'immediat', NULL, 'je voudrais que dès que tu arrives au client que tu me fasses signe', 'en_attente', 0.00, NULL, NULL, '2026-08-10 20:26:43', '2026-08-10 20:26:43', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(13, 9, 'Quartier Sèmè', 'Zogbadjè, Abomey-Calavi, Atlantique, Bénin', 6.42362800, 2.33479140, 'issa', 'Carrefour Abattoir, Tokplégbé, 1er Arrondissement, Cotonou, Littoral, Bénin', 6.36893950, 2.48531280, '25kg', 1, 5000.00, 'orders/package_photos/6a7a444fa1de0.webp', 'immediat', NULL, 'je voudrais que dès que tu arrives au client que tu me fasses signe', 'en_attente', 0.00, NULL, NULL, '2026-08-10 20:36:15', '2026-08-10 20:36:15', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(14, 9, 'Quartier Sèmè', 'Zogbadjè, Abomey-Calavi, Atlantique, Bénin', 6.42362800, 2.33479140, 'issa', 'Carrefour Abattoir, Tokplégbé, 1er Arrondissement, Cotonou, Littoral, Bénin', 6.36893950, 2.48531280, '25kg', 1, 5000.00, 'orders/package_photos/6a7a447623a83.webp', 'immediat', NULL, 'je voudrais que dès que tu arrives au client que tu me fasses signe', 'en_attente', 0.00, NULL, NULL, '2026-08-10 20:36:54', '2026-08-10 20:36:54', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(15, 11, 'Garderie Baby care Bidossessi', 'Quartier Sèmè, Awhanléko, Zoca, Abomey-Calavi, Atlantique, Bénin', 6.45386370, 2.35424500, 'dossou', 'Dandji, 1er Arrondissement, Cotonou, Littoral, Bénin', 6.36995460, 2.48603410, '13', 0, 2000.00, 'orders/package_photos/6a7aede041506.webp', 'immediat', NULL, 'je voudrais que si tu arrives au lieu de récupération tu m\'appelles', 'en_attente', 0.00, NULL, NULL, '2026-08-11 08:39:44', '2026-08-11 08:39:44', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(16, 11, 'Quartier Sèmè', 'Quartier Sèmè, Awhanléko, Zoca, Abomey-Calavi, Atlantique, Bénin', 6.45386370, 2.35424500, 'issa tchaboue', 'Dandji, 1er Arrondissement, Cotonou, Littoral, Bénin', 6.36995460, 2.48603410, '13', 0, 5000.00, 'orders/package_photos/6a7b07d3b73c9.webp', 'immediat', NULL, 'dès que tu réceptionnés le colis fais moi signe', 'en_attente', 0.00, NULL, NULL, '2026-08-11 10:30:27', '2026-08-11 10:30:27', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(17, 11, 'Quartier Sèmè', 'Quartier Sèmè, Awhanléko, Zoca, Abomey-Calavi, Atlantique, Bénin', 6.45386370, 2.35424500, 'issa tchaboue', 'Dandji, 1er Arrondissement, Cotonou, Littoral, Bénin', 6.36995460, 2.48603410, '13', 0, 5000.00, 'orders/package_photos/6a7b08865ae88.webp', 'immediat', NULL, 'dès que tu réceptionnés le colis fais moi signe', 'en_attente', 0.00, NULL, NULL, '2026-08-11 10:33:26', '2026-08-11 10:33:26', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(18, 11, 'Quartier Sèmè', 'Quartier Sèmè, Awhanléko, Zoca, Abomey-Calavi, Atlantique, Bénin', 6.45386370, 2.35424500, 'issa tchaboue', 'Dandji, 1er Arrondissement, Cotonou, Littoral, Bénin', 6.36995460, 2.48603410, '13', 0, 5000.00, 'orders/package_photos/6a7b0896ddd57.webp', 'immediat', NULL, 'dès que tu réceptionnés le colis fais moi signe', 'en_attente', 0.00, NULL, NULL, '2026-08-11 10:33:43', '2026-08-11 10:33:43', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(19, 11, 'Quartier Sèmè', 'Quartier Sèmè, Awhanléko, Zoca, Abomey-Calavi, Atlantique, Bénin', 6.45386370, 2.35424500, 'Dandji', 'Dandji, 1er Arrondissement, Cotonou, Littoral, Bénin', 6.36995460, 2.48603410, '25', 0, 3600.00, 'orders/package_photos/6a7b5094b6b41.webp', 'immediat', NULL, 'je voudrais que tu me fasses signes dès que tu récupères le colis', 'en_attente', 0.00, NULL, NULL, '2026-08-11 15:40:53', '2026-08-11 15:40:53', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(20, 11, 'Quartier Sèmè', 'Quartier Sèmè, Awhanléko, Zoca, Abomey-Calavi, Atlantique, Bénin', 6.45386370, 2.35424500, 'Dandji', 'Dandji, 1er Arrondissement, Cotonou, Littoral, Bénin', 6.36995460, 2.48603410, '25', 0, 3600.00, 'orders/package_photos/6a7b5131c3a0f.webp', 'immediat', NULL, 'je voudrais que tu me fasses signes dès que tu récupères le colis', 'en_attente', 0.00, NULL, NULL, '2026-08-11 15:43:29', '2026-08-11 15:43:29', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(21, 11, 'Quartier Sèmè', 'Quartier Sèmè, Awhanléko, Zoca, Abomey-Calavi, Atlantique, Bénin', 6.45386370, 2.35424500, 'Dandji', 'Dandji, 1er Arrondissement, Cotonou, Littoral, Bénin', 6.36995460, 2.48603410, '25', 0, 3600.00, 'orders/package_photos/6a7b5235ce9b2.webp', 'immediat', NULL, 'je voudrais que tu me fasses signes dès que tu récupères le colis', 'en_attente', 0.00, NULL, NULL, '2026-08-11 15:47:49', '2026-08-11 15:47:49', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(22, 11, 'Quartier Sèmè', 'Quartier Sèmè, Awhanléko, Zoca, Abomey-Calavi, Atlantique, Bénin', 6.45386370, 2.35424500, 'Dandji', 'Dandji, 1er Arrondissement, Cotonou, Littoral, Bénin', 6.36995460, 2.48603410, '25', 0, 3600.00, 'orders/package_photos/6a7b5293c6e35.webp', 'immediat', NULL, 'je voudrais que tu me fasses signes dès que tu récupères le colis', 'en_attente', 0.00, NULL, NULL, '2026-08-11 15:49:23', '2026-08-11 15:49:23', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(23, 11, 'Tokpa-Zoungo-Nord', 'Tokpa-Zoungo-Nord, Zogbadjè, Tchinangbégbo, Abomey-Calavi, Atlantique, Bénin', 6.42854055, 2.34551199, 'Dandji', 'Dandji, 1er Arrondissement, Cotonou, Littoral, Bénin', 6.36995460, 2.48603410, '12', 0, 2388.00, 'orders/package_photos/6a7b56cbdc2f6.webp', 'immediat', NULL, 'Merci de me faire signe dès la récupération', 'en_attente', 0.00, NULL, NULL, '2026-08-11 16:07:24', '2026-08-11 16:07:24', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(24, 11, 'Tokpa-Zoungo-Nord', 'Tokpa-Zoungo-Nord, Zogbadjè, Tchinangbégbo, Abomey-Calavi, Atlantique, Bénin', 6.42791253, 2.34518073, 'Dandji', 'Dandji, 1er Arrondissement, Cotonou, Littoral, Bénin', 6.36995460, 2.48603410, '14', 0, 2500.00, 'orders/package_photos/6a7b5e0f5038c.webp', 'immediat', NULL, 'cggvv', 'en_attente', 0.00, NULL, NULL, '2026-08-11 16:38:23', '2026-08-11 16:38:23', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(25, 11, 'Tokpa-Zoungo-Nord', 'Tokpa-Zoungo-Nord, Zogbadjè, Tchinangbégbo, Abomey-Calavi, Atlantique, Bénin', 6.42752905, 2.34495409, 'Dandji', 'Dandji, 1er Arrondissement, Cotonou, Littoral, Bénin', 6.36995460, 2.48603410, '12', 0, 2500.00, 'orders/package_photos/6a7b60b338f0c.webp', 'immediat', NULL, 'ccvv cdsse', 'en_attente', 0.00, NULL, NULL, '2026-08-11 16:49:39', '2026-08-11 16:49:39', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(26, 11, 'Tokpa-Zoungo-Nord', 'Tokpa-Zoungo-Nord, Zogbadjè, Tchinangbégbo, Abomey-Calavi, Atlantique, Bénin', 6.42752905, 2.34495409, 'Dandji', 'Dandji, 1er Arrondissement, Cotonou, Littoral, Bénin', 6.36995460, 2.48603410, '12', 0, 2500.00, 'orders/package_photos/6a7b619bcd702.webp', 'immediat', NULL, 'ccvv cdsse', 'en_attente', 0.00, NULL, NULL, '2026-08-11 16:53:31', '2026-08-11 16:53:31', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(27, 11, 'Tokpa-Zoungo-Nord', 'Tokpa-Zoungo-Nord, Zogbadjè, Tchinangbégbo, Abomey-Calavi, Atlantique, Bénin', 6.42752905, 2.34495409, 'Dandji', 'Dandji, 1er Arrondissement, Cotonou, Littoral, Bénin', 6.36995460, 2.48603410, '12', 0, 2500.00, 'orders/package_photos/6a7b6271235ca.webp', 'immediat', NULL, 'ccvv cdsse', 'en_attente', 0.00, NULL, NULL, '2026-08-11 16:57:05', '2026-08-11 16:57:05', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(28, 11, 'Tokpa-Zoungo-Nord', 'Tokpa-Zoungo-Nord, Zogbadjè, Tchinangbégbo, Abomey-Calavi, Atlantique, Bénin', 6.42752905, 2.34495409, 'Dandji', 'Dandji, 1er Arrondissement, Cotonou, Littoral, Bénin', 6.36995460, 2.48603410, '12', 0, 2500.00, 'orders/package_photos/6a7b6331afd83.webp', 'immediat', NULL, 'ccvv cdsse', 'en_attente', 0.00, NULL, NULL, '2026-08-11 17:00:17', '2026-08-11 17:00:17', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(29, 11, 'Tokpa-Zoungo-Nord', 'Tokpa-Zoungo-Nord, Zogbadjè, Tchinangbégbo, Abomey-Calavi, Atlantique, Bénin', 6.42752905, 2.34495409, 'Dandji', 'Dandji, 1er Arrondissement, Cotonou, Littoral, Bénin', 6.36995460, 2.48603410, '12', 0, 2500.00, 'orders/package_photos/6a7b6422cde89.webp', 'immediat', NULL, 'ccvv cdsse', 'en_attente', 0.00, NULL, NULL, '2026-08-11 17:04:19', '2026-08-11 17:04:19', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(30, 11, 'Tokpa-Zoungo-Nord', 'Tokpa-Zoungo-Nord, Zogbadjè, Tchinangbégbo, Abomey-Calavi, Atlantique, Bénin', 6.42854954, 2.34552406, 'Boulevard des Bantu', 'Dandji, 1er Arrondissement, Cotonou, Littoral, Bénin', 6.36995459, 2.48603426, '25', 0, 2430.00, 'orders/package_photos/6a7c10f147e84.webp', 'immediat', NULL, 'ffgvvvvsss', 'en_attente', 0.00, NULL, NULL, '2026-08-12 05:21:37', '2026-08-12 05:21:37', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(31, 11, 'Tokpa-Zoungo-Nord', 'Tokpa-Zoungo-Nord, Zogbadjè, Tchinangbégbo, Abomey-Calavi, Atlantique, Bénin', 6.42854954, 2.34552406, 'Boulevard des Bantu', 'Dandji, 1er Arrondissement, Cotonou, Littoral, Bénin', 6.36995459, 2.48603426, '25', 0, 2430.00, 'orders/package_photos/6a7c2071363d2.webp', 'immediat', NULL, 'ffgvvvvsss', 'en_attente', 0.00, NULL, NULL, '2026-08-12 06:27:45', '2026-08-12 06:27:45', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(32, 11, 'Tokpa-Zoungo-Nord', 'Tokpa-Zoungo-Nord, Zogbadjè, Tchinangbégbo, Abomey-Calavi, Atlantique, Bénin', 6.42855121, 2.34551132, 'Boulevard des Bantu', 'Boulevard des Bantu, PK5, Plein-ciel, 3ème Arrondissement, Libreville, Estuaire, Gabon', 0.41620008, 9.46729995, '24', 0, 12369.00, 'orders/package_photos/6a7c222f0c5c5.webp', 'immediat', NULL, 'cvvvbb', 'en_attente', 0.00, NULL, NULL, '2026-08-12 06:35:11', '2026-08-12 06:35:11', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(33, 11, 'Tokpa-Zoungo-Nord', 'Tokpa-Zoungo-Nord, Zogbadjè, Tchinangbégbo, Abomey-Calavi, Atlantique, Bénin', 6.42833065, 2.34548148, 'issa', 'Dandji, 1er Arrondissement, Cotonou, Littoral, Bénin', 6.36995460, 2.48603410, '35', 0, 2500.00, 'orders/package_photos/6a7c24dd5e437.webp', 'immediat', NULL, 'cgvvbbv', 'en_attente', 0.00, NULL, NULL, '2026-08-12 06:46:37', '2026-08-12 06:46:37', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(34, 11, 'Tokpa-Zoungo-Nord', 'Tokpa-Zoungo-Nord, Zogbadjè, Tchinangbégbo, Abomey-Calavi, Atlantique, Bénin', 6.42840895, 2.34552808, 'Dandji', 'Dandji, 1er Arrondissement, Cotonou, Littoral, Bénin', 6.36995460, 2.48603410, '34', 0, 2536.00, 'orders/package_photos/6a7c274140bfd.webp', 'immediat', NULL, 'ccvv', 'en_attente', 0.00, NULL, NULL, '2026-08-12 06:56:49', '2026-08-12 06:56:49', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(35, 11, 'Tokpa-Zoungo-Nord', 'Tokpa-Zoungo-Nord, Zogbadjè, Tchinangbégbo, Abomey-Calavi, Atlantique, Bénin', 6.42866216, 2.34554317, 'bilal', 'Dandji, 1er Arrondissement, Cotonou, Littoral, Bénin', 6.36995460, 2.48603410, '24', 0, 2500.00, 'orders/package_photos/6a7c2b4888404.webp', 'immediat', NULL, 'fggv', 'en_attente', 0.00, NULL, NULL, '2026-08-12 07:14:00', '2026-08-12 07:14:00', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, 11, NULL, NULL, NULL, NULL, NULL, NULL),
(36, 11, 'Tokpa-Zoungo-Nord', 'Tokpa-Zoungo-Nord, Zogbadjè, Tchinangbégbo, Abomey-Calavi, Atlantique, Bénin', 6.42887605, 2.34523471, 'bilal', 'Dandji, 1er Arrondissement, Cotonou, Littoral, Bénin', 6.36995460, 2.48603410, '24', 0, 2566.00, 'orders/package_photos/6a7c641886cea.webp', 'immediat', NULL, 'ggvbbb', 'en_attente', 0.00, NULL, NULL, '2026-08-12 11:16:24', '2026-08-12 11:16:24', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(37, 11, 'Tokpa-Zoungo-Nord', 'Tokpa-Zoungo-Nord, Zogbadjè, Tchinangbégbo, Abomey-Calavi, Atlantique, Bénin', 6.42861551, 2.34550327, 'bilal tchaboue', 'Dandji, 1er Arrondissement, Cotonou, Littoral, Bénin', 6.36995460, 2.48603410, '33', 0, 2300.00, 'orders/package_photos/6a7ca1410b28c.webp', 'immediat', NULL, 'ccvvvv', 'en_attente', 0.00, NULL, NULL, '2026-08-12 15:37:21', '2026-08-12 15:37:21', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(38, 11, 'Tokpa-Zoungo-Nord', 'Tokpa-Zoungo-Nord, Zogbadjè, Tchinangbégbo, Abomey-Calavi, Atlantique, Bénin', 6.42877743, 2.34558206, 'Adam bilal', 'Carrefour Abattoir, Tokplégbé, 1er Arrondissement, Cotonou, Littoral, Bénin', 6.36893950, 2.48531280, '33', 0, 2500.00, 'orders/package_photos/6a7ca2abc65f5.webp', 'immediat', NULL, 'hhjj', 'en_attente', 0.00, NULL, NULL, '2026-08-12 15:43:23', '2026-08-12 15:43:23', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(39, 11, 'Tchinangbégbo', 'Tchinangbégbo, Abomey-Calavi, Atlantique, Bénin', 6.42880209, 2.34556228, 'Yasmine', 'Déplacez la carte pour choisir le lieu…', 6.43426134, 2.33821537, '33', 0, 250.00, 'orders/package_photos/6a7ca42e6dc75.webp', 'immediat', NULL, 'ghbbbn', 'en_cours', 0.00, NULL, NULL, '2026-08-12 15:49:50', '2026-08-12 15:50:01', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, 10, NULL, NULL, NULL, NULL, NULL, NULL),
(40, 11, 'Tchinangbégbo', 'Tchinangbégbo, Abomey-Calavi, Atlantique, Bénin', 6.42880209, 2.34556228, 'Yasmine', 'Déplacez la carte pour choisir le lieu…', 6.43426134, 2.33821537, '33', 0, 250.00, 'orders/package_photos/6a7ca46dad246.webp', 'immediat', NULL, 'ghbbbn', 'en_cours', 0.00, NULL, NULL, '2026-08-12 15:50:53', '2026-08-12 15:50:58', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, 10, NULL, NULL, NULL, NULL, NULL, NULL),
(41, 11, 'Tchinangbégbo', 'Tchinangbégbo, Abomey-Calavi, Atlantique, Bénin', 6.42880209, 2.34556228, 'Yasmine', 'Déplacez la carte pour choisir le lieu…', 6.43426134, 2.33821537, '33', 0, 250.00, 'orders/package_photos/6a7ca9de76608.webp', 'immediat', NULL, 'ghbbbn', 'en_cours', 0.00, NULL, NULL, '2026-08-12 16:14:06', '2026-08-12 16:14:25', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, 10, NULL, NULL, NULL, NULL, NULL, NULL),
(42, 11, 'Tokpa-Zoungo-Nord', 'Tokpa-Zoungo-Nord, Zogbadjè, Abomey-Calavi, Atlantique, Bénin', 6.42854855, 2.34568499, 'yasso', 'Déplacez la carte pour choisir le lieu…', 6.43204946, 2.34268259, '34', 0, 2588.00, 'orders/package_photos/6a7cadad9ad45.webp', 'immediat', NULL, 'gggg', 'en_cours', 0.00, NULL, NULL, '2026-08-12 16:30:21', '2026-08-12 16:30:36', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, 10, NULL, NULL, NULL, NULL, NULL, NULL),
(43, 11, 'Déplacez la carte pour choisir le lieu…', 'Déplacez la carte pour choisir le lieu…', 6.42880608, 2.34534904, 'bilalgh', 'Déplacez la carte pour choisir le lieu…', 6.43217473, 2.34275635, '34', 0, 222.00, 'orders/package_photos/6a7cb037f3d3b.webp', 'immediat', NULL, 'ccvv', 'en_cours', 0.00, NULL, NULL, '2026-08-12 16:41:12', '2026-08-12 16:41:23', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, 10, NULL, NULL, NULL, NULL, NULL, NULL),
(44, 11, 'Tokpa-Zoungo-Nord', 'Tokpa-Zoungo-Nord, Zogbadjè, Tchinangbégbo, Abomey-Calavi, Atlantique, Bénin', 6.42874511, 2.34566588, 'maman yasmine', 'Dandji, 1er Arrondissement, Cotonou, Littoral, Bénin', 6.36995460, 2.48603410, '23', 0, 2500.00, 'orders/package_photos/6a7cf7cac1004.webp', 'immediat', NULL, 'Des ton arrivé fais moi signe', 'en_cours', 0.00, NULL, NULL, '2026-08-12 21:46:35', '2026-08-12 21:46:46', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(45, 11, 'Tokpa-Zoungo-Nord', 'Tokpa-Zoungo-Nord, Zogbadjè, Tchinangbégbo, Abomey-Calavi, Atlantique, Bénin', 6.42874511, 2.34566588, 'maman yasmine', 'Dandji, 1er Arrondissement, Cotonou, Littoral, Bénin', 6.36995460, 2.48603410, '23', 0, 2500.00, 'orders/package_photos/6a7cf7fa6e2b7.webp', 'immediat', NULL, 'Des ton arrivé fais moi signe', 'en_cours', 0.00, NULL, NULL, '2026-08-12 21:47:22', '2026-08-12 21:47:39', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(46, 11, 'Tokpa-Zoungo-Nord', 'Tokpa-Zoungo-Nord, Zogbadjè, Tchinangbégbo, Abomey-Calavi, Atlantique, Bénin', 6.42874511, 2.34566588, 'maman yasmine', 'Dandji, 1er Arrondissement, Cotonou, Littoral, Bénin', 6.36995460, 2.48603410, '23', 0, 2500.00, 'orders/package_photos/6a7cf89215dcd.webp', 'immediat', NULL, 'Des ton arrivé fais moi signe', 'en_cours', 0.00, NULL, NULL, '2026-08-12 21:49:54', '2026-08-12 21:50:07', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(47, 11, 'Tokpa-Zoungo-Nord', 'Tokpa-Zoungo-Nord, Zogbadjè, Tchinangbégbo, Abomey-Calavi, Atlantique, Bénin', 6.42874511, 2.34566588, 'maman yasmine', 'Dandji, 1er Arrondissement, Cotonou, Littoral, Bénin', 6.36995460, 2.48603410, '23', 0, 2500.00, 'orders/package_photos/6a7cfa6f2c14c.webp', 'immediat', NULL, 'Des ton arrivé fais moi signe', 'en_cours', 0.00, NULL, NULL, '2026-08-12 21:57:51', '2026-08-12 21:58:02', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(48, 11, 'Tokpa-Zoungo-Nord', 'Tokpa-Zoungo-Nord, Zogbadjè, Tchinangbégbo, Abomey-Calavi, Atlantique, Bénin', 6.42855687, 2.34559011, 'abiba', 'Dandji, 1er Arrondissement, Cotonou, Littoral, Bénin', 6.36995460, 2.48603410, '25', 0, 2300.00, 'orders/package_photos/6a7d72a7de209.webp', 'immediat', NULL, 'vvvvv', 'en_cours', 0.00, NULL, NULL, '2026-08-13 06:30:48', '2026-08-13 06:31:03', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(49, 11, 'Tokpa-Zoungo-Nord', 'Tokpa-Zoungo-Nord, Zogbadjè, Tchinangbégbo, Abomey-Calavi, Atlantique, Bénin', 6.42855687, 2.34559011, 'abiba', 'Dandji, 1er Arrondissement, Cotonou, Littoral, Bénin', 6.36995460, 2.48603410, '25', 0, 2300.00, 'orders/package_photos/6a7d74a648746.webp', 'immediat', NULL, 'vvvvv', 'en_cours', 0.00, NULL, NULL, '2026-08-13 06:39:18', '2026-08-13 06:39:28', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(50, 11, 'Tokpa-Zoungo-Nord', 'Tokpa-Zoungo-Nord, Zogbadjè, Tchinangbégbo, Abomey-Calavi, Atlantique, Bénin', 6.42855687, 2.34559011, 'abiba', 'Dandji, 1er Arrondissement, Cotonou, Littoral, Bénin', 6.36995460, 2.48603410, '25', 0, 2300.00, 'orders/package_photos/6a7d78ea9d17b.webp', 'immediat', NULL, 'vvvvv', 'en_cours', 0.00, NULL, NULL, '2026-08-13 06:57:30', '2026-08-13 06:57:52', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(51, 11, 'Tokpa-Zoungo-Nord', 'Tokpa-Zoungo-Nord, Zogbadjè, Tchinangbégbo, Abomey-Calavi, Atlantique, Bénin', 6.42855687, 2.34559011, 'abiba', 'Dandji, 1er Arrondissement, Cotonou, Littoral, Bénin', 6.36995460, 2.48603410, '25', 0, 2300.00, 'orders/package_photos/6a7d7b2a79d53.webp', 'immediat', NULL, 'vvvvv', 'en_cours', 0.00, NULL, NULL, '2026-08-13 07:07:06', '2026-08-13 07:07:11', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(52, 11, 'Tokpa-Zoungo-Nord', 'Tokpa-Zoungo-Nord, Zogbadjè, Tchinangbégbo, Abomey-Calavi, Atlantique, Bénin', 6.42859985, 2.34577317, 'Déplacez la carte pour choisir le lieu…', 'Déplacez la carte pour choisir le lieu…', 6.43226535, 2.34207239, NULL, 0, 0.00, 'orders/package_photos/6a7d7febca45d.webp', 'immediat', NULL, 'cgvvvv', 'en_cours', 0.00, NULL, NULL, '2026-08-13 07:27:23', '2026-08-13 07:27:42', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(53, 11, 'Tokpa-Zoungo-Nord', 'Tokpa-Zoungo-Nord, Zogbadjè, Tchinangbégbo, Abomey-Calavi, Atlantique, Bénin', 6.42859985, 2.34577317, 'Déplacez la carte pour choisir le lieu…', 'Déplacez la carte pour choisir le lieu…', 6.43226535, 2.34207239, NULL, 0, 0.00, 'orders/package_photos/6a7d805723999.webp', 'immediat', NULL, 'cgvvvv', 'en_cours', 0.00, NULL, NULL, '2026-08-13 07:29:11', '2026-08-13 07:29:40', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(54, 11, 'Tokpa-Zoungo-Nord', 'Tokpa-Zoungo-Nord, Seminaire, Tchinangbégbo, Abomey-Calavi, Atlantique, Bénin', 6.42878509, 2.34561659, 'Déplacez la carte pour choisir le lieu…', 'Déplacez la carte pour choisir le lieu…', 6.36865009, 2.42791850, '45', 0, 2300.00, 'orders/package_photos/6a7d9a5cbb6b5.webp', 'immediat', NULL, 'cvvvbb', 'en_cours', 0.00, NULL, NULL, '2026-08-13 09:20:12', '2026-08-13 09:20:17', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(55, 11, 'Tokpa-Zoungo-Nord', 'Tokpa-Zoungo-Nord, Seminaire, Tchinangbégbo, Abomey-Calavi, Atlantique, Bénin', 6.42878509, 2.34561659, 'Déplacez la carte pour choisir le lieu…', 'Déplacez la carte pour choisir le lieu…', 6.36865009, 2.42791850, '45', 0, 2300.00, 'orders/package_photos/6a7d9e088f41c.webp', 'immediat', NULL, 'cvvvbb', 'en_cours', 0.00, NULL, NULL, '2026-08-13 09:35:52', '2026-08-13 09:35:57', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(56, 11, 'Tokpa-Zoungo-Nord', 'Tokpa-Zoungo-Nord, Seminaire, Tchinangbégbo, Abomey-Calavi, Atlantique, Bénin', 6.42878509, 2.34561659, 'Déplacez la carte pour choisir le lieu…', 'Déplacez la carte pour choisir le lieu…', 6.36865009, 2.42791850, '45', 0, 2300.00, 'orders/package_photos/6a7da4624469a.webp', 'immediat', NULL, 'cvvvbb', 'en_cours', 0.00, NULL, NULL, '2026-08-13 10:02:58', '2026-08-13 10:03:20', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(57, 11, 'Zogbadjè', 'Zogbadjè, Tchinangbégbo, Abomey-Calavi, Atlantique, Bénin', 6.43292969, 2.34140720, 'Dandji', 'Dandji, 1er Arrondissement, Cotonou, Littoral, Bénin', 6.36995460, 2.48603410, '34', 0, 2560.00, 'orders/package_photos/6a7da7345d4a5.webp', 'immediat', NULL, 'ccvvvv', 'en_cours', 0.00, NULL, NULL, '2026-08-13 10:15:00', '2026-08-13 10:15:05', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(58, 11, '7ème Arrondissement', '7ème Arrondissement, Cotonou, Littoral, Bénin', 6.43204180, 2.34228965, 'Déplacez la carte pour choisir le lieu…', 'Déplacez la carte pour choisir le lieu…', 6.37603157, 2.39198763, '45', 0, 2533.00, 'orders/package_photos/6a7dad13bd090.webp', 'immediat', NULL, 'ccvvvvv', 'en_cours', 0.00, NULL, NULL, '2026-08-13 10:40:04', '2026-08-13 10:40:10', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(59, 11, 'Déplacez la carte pour choisir le lieu…', 'Déplacez la carte pour choisir le lieu…', 6.43253255, 2.34204959, 'issa tchaboue', 'Déplacez la carte pour choisir le lieu…', 6.39286600, 2.35524874, '35', 0, 2369.00, 'orders/package_photos/6a7dae32565a4.webp', 'immediat', NULL, 'ggvlvv', 'en_cours', 0.00, NULL, NULL, '2026-08-13 10:44:50', '2026-08-13 10:44:54', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(60, 11, 'Déplacez la carte pour choisir le lieu…', 'Déplacez la carte pour choisir le lieu…', 6.43253255, 2.34204959, 'issa tchaboue', 'Déplacez la carte pour choisir le lieu…', 6.39286600, 2.35524874, '35', 0, 2369.00, 'orders/package_photos/6a7dae7f938a8.webp', 'immediat', NULL, 'ggvlvv', 'en_cours', 0.00, NULL, NULL, '2026-08-13 10:46:07', '2026-08-13 10:46:11', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(61, 11, 'Déplacez la carte pour choisir le lieu…', 'Déplacez la carte pour choisir le lieu…', 6.43253255, 2.34204959, 'issa tchaboue', 'Déplacez la carte pour choisir le lieu…', 6.39286600, 2.35524874, '35', 0, 2369.00, 'orders/package_photos/6a7dafbbbf6af.webp', 'immediat', NULL, 'ggvlvv', 'en_cours', 0.00, NULL, NULL, '2026-08-13 10:51:23', '2026-08-13 10:51:28', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(62, 11, 'Déplacez la carte pour choisir le lieu…', 'Déplacez la carte pour choisir le lieu…', 6.43253255, 2.34204959, 'issa tchaboue', 'Déplacez la carte pour choisir le lieu…', 6.39286600, 2.35524874, '35', 0, 2369.00, 'orders/package_photos/6a7db2ab2cffc.webp', 'immediat', NULL, 'ggvlvv', 'en_cours', 0.00, NULL, NULL, '2026-08-13 11:03:55', '2026-08-13 11:04:04', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(63, 11, 'Déplacez la carte pour choisir le lieu…', 'Déplacez la carte pour choisir le lieu…', 6.43253255, 2.34204959, 'issa tchaboue', 'Déplacez la carte pour choisir le lieu…', 6.39286600, 2.35524874, '35', 0, 2369.00, 'orders/package_photos/6a7db5c89d9dc.webp', 'immediat', NULL, 'ggvlvv', 'en_cours', 0.00, NULL, NULL, '2026-08-13 11:17:12', '2026-08-13 11:17:19', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(64, 11, 'Déplacez la carte pour choisir le lieu…', 'Déplacez la carte pour choisir le lieu…', 6.43253255, 2.34204959, 'issa tchaboue', 'Déplacez la carte pour choisir le lieu…', 6.39286600, 2.35524874, '35', 0, 2369.00, 'orders/package_photos/6a7dbfba46b54.webp', 'immediat', NULL, 'ggvlvv', 'en_cours', 0.00, NULL, NULL, '2026-08-13 11:59:38', '2026-08-13 11:59:43', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(65, 11, 'Tokpa-Zoungo-Sud', 'Tokpa-Zoungo-Sud, Zogbadjè, Tchinangbégbo, Abomey-Calavi, Atlantique, Bénin', 6.43238396, 2.34265208, 'bilal', 'Dandji, 1er Arrondissement, Cotonou, Littoral, Bénin', 6.36995460, 2.48603410, '24', 0, 2566.00, 'orders/package_photos/6a7dc6d75fb92.webp', 'immediat', NULL, 'cvvvvbb', 'en_cours', 0.00, NULL, NULL, '2026-08-13 12:29:59', '2026-08-13 12:30:07', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(66, 11, 'Tokpa-Zoungo-Sud', 'Tokpa-Zoungo-Sud, Zogbadjè, Tchinangbégbo, Abomey-Calavi, Atlantique, Bénin', 6.43238396, 2.34265208, 'bilal', 'Dandji, 1er Arrondissement, Cotonou, Littoral, Bénin', 6.36995460, 2.48603410, '24', 0, 2566.00, 'orders/package_photos/6a7dc8b1c12b9.webp', 'immediat', NULL, 'cvvvvbb', 'en_cours', 0.00, NULL, NULL, '2026-08-13 12:37:53', '2026-08-13 12:37:59', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(67, 11, 'Tokpa-Zoungo-Sud', 'Tokpa-Zoungo-Sud, Zogbadjè, Tchinangbégbo, Abomey-Calavi, Atlantique, Bénin', 6.43238396, 2.34265208, 'bilal', 'Dandji, 1er Arrondissement, Cotonou, Littoral, Bénin', 6.36995460, 2.48603410, '24', 0, 2566.00, 'orders/package_photos/6a7dcf0868f53.webp', 'immediat', NULL, 'cvvvvbb', 'en_cours', 0.00, NULL, NULL, '2026-08-13 13:04:56', '2026-08-13 13:05:11', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(68, 11, 'Poissonnerie la concorde', 'Poissonnerie la concorde, Rue de l\'espoire, Tokpa-Zoungo-Nord, Seminaire, Abomey-Calavi, Atlantique, Bénin', 6.43247425, 2.34236877, 'bilale', 'Avenue Codje Tovalou Quénum, 7ème Arrondissement, Cotonou, Littoral, Bénin', 6.36769530, 2.42525070, '34', 0, 2222.00, 'orders/package_photos/6a7de865b7171.webp', 'immediat', NULL, 'gvvvvv', 'en_cours', 0.00, NULL, NULL, '2026-08-13 14:53:10', '2026-08-13 14:53:45', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(71, 11, 'Tokpa-Zoungo-Nord', 'Tokpa-Zoungo-Nord, Zogbadjè, Tchinangbégbo, Abomey-Calavi, Atlantique, Bénin', 6.42866315, 2.34546572, 'alaxka', 'Menontin, Kouhounou, 9ème Arrondissement, Cotonou, Littoral, Bénin', 6.38873974, 2.37142246, '34', 0, 566.00, 'orders/package_photos/6a7df71036966.webp', 'immediat', NULL, 'ccvvv', 'en_cours', 0.00, NULL, NULL, '2026-08-13 15:55:44', '2026-08-13 15:55:49', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(72, 11, 'Tokpa-Zoungo-Nord', 'Tokpa-Zoungo-Nord, Zogbadjè, Tchinangbégbo, Abomey-Calavi, Atlantique, Bénin', 6.42866315, 2.34546572, 'alaxka', 'Menontin, Kouhounou, 9ème Arrondissement, Cotonou, Littoral, Bénin', 6.38873974, 2.37142246, '34', 0, 566.00, 'orders/package_photos/6a7df761ed53a.webp', 'immediat', NULL, 'ccvvv', 'en_cours', 0.00, NULL, NULL, '2026-08-13 15:57:06', '2026-08-13 15:57:21', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(73, 11, 'Tokpa-Zoungo-Nord', 'Tokpa-Zoungo-Nord, Zogbadjè, Tchinangbégbo, Abomey-Calavi, Atlantique, Bénin', 6.42866315, 2.34546572, 'alaxka', 'Menontin, Kouhounou, 9ème Arrondissement, Cotonou, Littoral, Bénin', 6.38873974, 2.37142246, '34', 0, 566.00, 'orders/package_photos/6a7df7841757b.webp', 'immediat', NULL, 'ccvvv', 'en_cours', 0.00, NULL, NULL, '2026-08-13 15:57:40', '2026-08-13 15:57:48', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(74, 11, 'Tokpa-Zoungo-Nord', 'Tokpa-Zoungo-Nord, Zogbadjè, Tchinangbégbo, Abomey-Calavi, Atlantique, Bénin', 6.42866315, 2.34546572, 'alaxka', 'Menontin, Kouhounou, 9ème Arrondissement, Cotonou, Littoral, Bénin', 6.38873974, 2.37142246, '34', 0, 566.00, 'orders/package_photos/6a7e0100015a6.webp', 'immediat', NULL, 'ccvvv', 'en_cours', 0.00, NULL, NULL, '2026-08-13 16:38:08', '2026-08-13 16:38:13', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(75, 11, 'Tokpa-Zoungo-Nord', 'Tokpa-Zoungo-Nord, Zogbadjè, Tchinangbégbo, Abomey-Calavi, Atlantique, Bénin', 6.42866315, 2.34546572, 'alaxka', 'Menontin, Kouhounou, 9ème Arrondissement, Cotonou, Littoral, Bénin', 6.38873974, 2.37142246, '34', 0, 566.00, 'orders/package_photos/6a7e02ccb6ad2.webp', 'immediat', NULL, 'ccvvv', 'en_cours', 0.00, NULL, NULL, '2026-08-13 16:45:48', '2026-08-13 16:45:58', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(76, 11, 'Tokpa-Zoungo-Nord', 'Tokpa-Zoungo-Nord, Zogbadjè, Tchinangbégbo, Abomey-Calavi, Atlantique, Bénin', 6.42866315, 2.34546572, 'alaxka', 'Menontin, Kouhounou, 9ème Arrondissement, Cotonou, Littoral, Bénin', 6.38873974, 2.37142246, '34', 0, 566.00, 'orders/package_photos/6a7e037cdaceb.webp', 'immediat', NULL, 'ccvvv', 'en_cours', 0.00, NULL, NULL, '2026-08-13 16:48:45', '2026-08-13 16:48:49', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(77, 11, 'Tokpa-Zoungo-Nord', 'Tokpa-Zoungo-Nord, Zogbadjè, Tchinangbégbo, Abomey-Calavi, Atlantique, Bénin', 6.42866315, 2.34546572, 'alaxka', 'Menontin, Kouhounou, 9ème Arrondissement, Cotonou, Littoral, Bénin', 6.38873974, 2.37142246, '34', 0, 566.00, 'orders/package_photos/6a7e04926bd3e.webp', 'immediat', NULL, 'ccvvv', 'en_cours', 0.00, NULL, NULL, '2026-08-13 16:53:22', '2026-08-13 16:53:26', NULL, 0.00, 0.00, 'en_attente_recherche', 2, 0, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `order_delivery_requests`
--

DROP TABLE IF EXISTS `order_delivery_requests`;
CREATE TABLE IF NOT EXISTS `order_delivery_requests` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` bigint UNSIGNED NOT NULL,
  `livreur_id` bigint UNSIGNED NOT NULL,
  `rayon_km` int NOT NULL,
  `distance_au_livreur` decimal(8,2) NOT NULL,
  `status` enum('pending','accepted','refused','expired') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `sent_at` timestamp NOT NULL,
  `responded_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_delivery_requests_order_id_status_index` (`order_id`,`status`),
  KEY `order_delivery_requests_livreur_id_status_index` (`livreur_id`,`status`),
  KEY `order_delivery_requests_expires_at_index` (`expires_at`)
) ENGINE=MyISAM AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `order_delivery_requests`
--

INSERT INTO `order_delivery_requests` (`id`, `order_id`, `livreur_id`, `rayon_km`, `distance_au_livreur`, `status`, `sent_at`, `responded_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(14, 51, 10, 2, 0.10, 'accepted', '2026-08-13 07:07:06', '2026-08-13 07:07:11', '2026-08-13 07:09:06', '2026-08-13 07:07:06', '2026-08-13 07:07:11'),
(2, 39, 10, 2, 0.14, 'accepted', '2026-08-12 15:49:53', '2026-08-12 15:50:01', '2026-08-12 15:51:53', '2026-08-12 15:49:53', '2026-08-12 15:50:01'),
(3, 40, 10, 2, 0.14, 'accepted', '2026-08-12 15:50:57', '2026-08-12 15:50:58', '2026-08-12 15:52:57', '2026-08-12 15:50:57', '2026-08-12 15:50:58'),
(4, 41, 10, 2, 0.13, 'accepted', '2026-08-12 16:14:07', '2026-08-12 16:14:25', '2026-08-12 16:16:07', '2026-08-12 16:14:07', '2026-08-12 16:14:25'),
(5, 42, 10, 2, 0.11, 'accepted', '2026-08-12 16:30:22', '2026-08-12 16:30:36', '2026-08-12 16:32:22', '2026-08-12 16:30:22', '2026-08-12 16:30:36'),
(6, 43, 10, 2, 0.13, 'accepted', '2026-08-12 16:41:17', '2026-08-12 16:41:23', '2026-08-12 16:43:17', '2026-08-12 16:41:17', '2026-08-12 16:41:23'),
(7, 44, 10, 2, 0.13, 'accepted', '2026-08-12 21:46:35', '2026-08-12 21:46:46', '2026-08-12 21:48:35', '2026-08-12 21:46:35', '2026-08-12 21:46:46'),
(8, 45, 10, 2, 0.14, 'accepted', '2026-08-12 21:47:23', '2026-08-12 21:47:39', '2026-08-12 21:49:23', '2026-08-12 21:47:23', '2026-08-12 21:47:39'),
(9, 46, 10, 2, 0.13, 'accepted', '2026-08-12 21:49:55', '2026-08-12 21:50:07', '2026-08-12 21:51:55', '2026-08-12 21:49:55', '2026-08-12 21:50:07'),
(10, 47, 10, 2, 0.14, 'accepted', '2026-08-12 21:57:52', '2026-08-12 21:58:02', '2026-08-12 21:59:52', '2026-08-12 21:57:52', '2026-08-12 21:58:02'),
(11, 48, 10, 2, 0.11, 'accepted', '2026-08-13 06:30:48', '2026-08-13 06:31:03', '2026-08-13 06:32:48', '2026-08-13 06:30:48', '2026-08-13 06:31:03'),
(12, 49, 10, 2, 0.09, 'accepted', '2026-08-13 06:39:18', '2026-08-13 06:39:28', '2026-08-13 06:41:18', '2026-08-13 06:39:18', '2026-08-13 06:39:28'),
(13, 50, 10, 2, 0.12, 'accepted', '2026-08-13 06:57:31', '2026-08-13 06:57:52', '2026-08-13 06:59:31', '2026-08-13 06:57:31', '2026-08-13 06:57:52'),
(15, 52, 10, 2, 0.13, 'accepted', '2026-08-13 07:27:24', '2026-08-13 07:27:42', '2026-08-13 07:29:24', '2026-08-13 07:27:24', '2026-08-13 07:27:42'),
(16, 53, 10, 2, 0.14, 'accepted', '2026-08-13 07:29:11', '2026-08-13 07:29:40', '2026-08-13 07:31:11', '2026-08-13 07:29:11', '2026-08-13 07:29:40'),
(17, 54, 10, 2, 0.13, 'accepted', '2026-08-13 09:20:13', '2026-08-13 09:20:17', '2026-08-13 09:22:13', '2026-08-13 09:20:13', '2026-08-13 09:20:17'),
(18, 55, 10, 2, 0.14, 'accepted', '2026-08-13 09:35:53', '2026-08-13 09:35:57', '2026-08-13 09:37:53', '2026-08-13 09:35:53', '2026-08-13 09:35:57'),
(19, 56, 10, 2, 0.14, 'accepted', '2026-08-13 10:03:18', '2026-08-13 10:03:20', '2026-08-13 10:05:18', '2026-08-13 10:03:18', '2026-08-13 10:03:20'),
(20, 57, 10, 2, 0.72, 'accepted', '2026-08-13 10:15:00', '2026-08-13 10:15:06', '2026-08-13 10:17:00', '2026-08-13 10:15:00', '2026-08-13 10:15:06'),
(21, 58, 10, 2, 0.58, 'accepted', '2026-08-13 10:40:04', '2026-08-13 10:40:10', '2026-08-13 10:42:04', '2026-08-13 10:40:04', '2026-08-13 10:40:10'),
(22, 59, 10, 2, 0.64, 'accepted', '2026-08-13 10:44:50', '2026-08-13 10:44:54', '2026-08-13 10:46:50', '2026-08-13 10:44:50', '2026-08-13 10:44:54'),
(23, 60, 10, 2, 0.65, 'accepted', '2026-08-13 10:46:08', '2026-08-13 10:46:11', '2026-08-13 10:48:08', '2026-08-13 10:46:08', '2026-08-13 10:46:11'),
(24, 61, 10, 2, 0.65, 'accepted', '2026-08-13 10:51:24', '2026-08-13 10:51:28', '2026-08-13 10:53:24', '2026-08-13 10:51:24', '2026-08-13 10:51:28'),
(25, 62, 10, 2, 0.65, 'accepted', '2026-08-13 11:03:55', '2026-08-13 11:04:05', '2026-08-13 11:05:55', '2026-08-13 11:03:55', '2026-08-13 11:04:05'),
(26, 63, 10, 2, 0.66, 'accepted', '2026-08-13 11:17:13', '2026-08-13 11:17:19', '2026-08-13 11:19:13', '2026-08-13 11:17:13', '2026-08-13 11:17:19'),
(27, 64, 10, 2, 0.65, 'accepted', '2026-08-13 11:59:38', '2026-08-13 11:59:43', '2026-08-13 12:01:38', '2026-08-13 11:59:38', '2026-08-13 11:59:43'),
(28, 65, 10, 2, 0.60, 'accepted', '2026-08-13 12:30:01', '2026-08-13 12:30:08', '2026-08-13 12:32:01', '2026-08-13 12:30:01', '2026-08-13 12:30:08'),
(29, 66, 10, 2, 0.61, 'accepted', '2026-08-13 12:37:54', '2026-08-13 12:37:59', '2026-08-13 12:39:54', '2026-08-13 12:37:54', '2026-08-13 12:37:59'),
(30, 67, 10, 2, 0.61, 'accepted', '2026-08-13 13:04:57', '2026-08-13 13:05:11', '2026-08-13 13:06:57', '2026-08-13 13:04:57', '2026-08-13 13:05:11'),
(31, 68, 10, 2, 0.63, 'accepted', '2026-08-13 14:53:36', '2026-08-13 14:53:45', '2026-08-13 14:55:36', '2026-08-13 14:53:36', '2026-08-13 14:53:45'),
(32, 69, 10, 2, 0.63, 'expired', '2026-08-13 14:58:19', NULL, '2026-08-13 15:00:19', '2026-08-13 14:58:19', '2026-08-13 15:51:13'),
(33, 70, 10, 2, 0.12, 'accepted', '2026-08-13 15:54:16', '2026-08-13 15:54:26', '2026-08-13 15:56:16', '2026-08-13 15:54:16', '2026-08-13 15:54:26'),
(34, 70, 11, 2, 0.12, 'pending', '2026-08-13 15:54:16', NULL, '2026-08-13 15:56:16', '2026-08-13 15:54:16', '2026-08-13 15:54:16'),
(35, 71, 10, 2, 0.12, 'accepted', '2026-08-13 15:55:45', '2026-08-13 15:55:49', '2026-08-13 15:57:45', '2026-08-13 15:55:45', '2026-08-13 15:55:49'),
(36, 71, 11, 2, 0.12, 'pending', '2026-08-13 15:55:45', NULL, '2026-08-13 15:57:45', '2026-08-13 15:55:45', '2026-08-13 15:55:45'),
(37, 72, 11, 2, 0.12, 'pending', '2026-08-13 15:57:07', NULL, '2026-08-13 15:59:07', '2026-08-13 15:57:07', '2026-08-13 15:57:07'),
(38, 72, 10, 2, 0.14, 'accepted', '2026-08-13 15:57:07', '2026-08-13 15:57:21', '2026-08-13 15:59:07', '2026-08-13 15:57:07', '2026-08-13 15:57:21'),
(39, 73, 11, 2, 0.12, 'pending', '2026-08-13 15:57:41', NULL, '2026-08-13 15:59:41', '2026-08-13 15:57:41', '2026-08-13 15:57:41'),
(40, 73, 10, 2, 0.13, 'accepted', '2026-08-13 15:57:41', '2026-08-13 15:57:48', '2026-08-13 15:59:41', '2026-08-13 15:57:41', '2026-08-13 15:57:48'),
(41, 74, 10, 2, 0.12, 'accepted', '2026-08-13 16:38:09', '2026-08-13 16:38:13', '2026-08-13 16:40:09', '2026-08-13 16:38:09', '2026-08-13 16:38:13'),
(42, 74, 11, 2, 0.12, 'pending', '2026-08-13 16:38:09', NULL, '2026-08-13 16:40:09', '2026-08-13 16:38:09', '2026-08-13 16:38:09'),
(43, 75, 10, 2, 0.12, 'accepted', '2026-08-13 16:45:49', '2026-08-13 16:45:58', '2026-08-13 16:47:49', '2026-08-13 16:45:49', '2026-08-13 16:45:58'),
(44, 75, 11, 2, 0.12, 'pending', '2026-08-13 16:45:49', NULL, '2026-08-13 16:47:49', '2026-08-13 16:45:49', '2026-08-13 16:45:49'),
(45, 76, 11, 2, 0.12, 'refused', '2026-08-13 16:48:46', '2026-08-13 16:48:49', '2026-08-13 16:50:46', '2026-08-13 16:48:46', '2026-08-13 16:48:49'),
(46, 76, 10, 2, 0.13, 'accepted', '2026-08-13 16:48:46', '2026-08-13 16:48:49', '2026-08-13 16:50:46', '2026-08-13 16:48:46', '2026-08-13 16:48:49'),
(47, 77, 11, 2, 0.12, 'refused', '2026-08-13 16:53:23', '2026-08-13 16:53:26', '2026-08-13 16:55:23', '2026-08-13 16:53:23', '2026-08-13 16:53:26'),
(48, 77, 10, 2, 0.13, 'accepted', '2026-08-13 16:53:23', '2026-08-13 16:53:26', '2026-08-13 16:55:23', '2026-08-13 16:53:23', '2026-08-13 16:53:26');

-- --------------------------------------------------------

--
-- Structure de la table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `payments`
--

DROP TABLE IF EXISTS `payments`;
CREATE TABLE IF NOT EXISTS `payments` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` bigint UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('credit_card','mobile_money','cash','bank_transfer') COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','completed','failed','refunded') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `transaction_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_details` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payments_transaction_id_unique` (`transaction_id`),
  KEY `payments_order_id_foreign` (`order_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_slug_unique` (`slug`)
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `slug`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Voir le tableau de bord admin', 'view-admin-dashboard', 'Voir le tableau de bord admin', '2026-08-06 11:03:50', '2026-08-06 11:04:49'),
(2, 'Voir toutes les commandes de la plateforme', 'view-all-orders', 'Voir toutes les commandes de la plateforme', '2026-08-06 11:03:50', '2026-08-06 11:04:49'),
(3, 'Créer une commande (Admin)', 'create-order', 'Créer manuellement une commande pour le compte d\'un client ou vendeur.', '2026-08-06 11:03:50', '2026-08-06 11:03:50'),
(4, 'Gérer les attributions manuelles de livraisons', 'manage-assignments', 'Gérer les attributions manuelles de livraisons', '2026-08-06 11:03:51', '2026-08-06 11:04:49'),
(5, 'Gérer et résoudre les litiges', 'manage-disputes', 'Gérer et résoudre les litiges', '2026-08-06 11:03:51', '2026-08-06 11:04:49'),
(6, 'Voir la liste des vendeurs', 'view-sellers', 'Voir la liste des vendeurs', '2026-08-06 11:03:51', '2026-08-06 11:04:49'),
(7, 'Valider les comptes et documents KYC des vendeurs', 'approve-sellers', 'Valider les comptes et documents KYC des vendeurs', '2026-08-06 11:03:51', '2026-08-06 11:04:49'),
(8, 'Modifier ou suspendre un compte vendeur', 'edit-sellers', 'Modifier ou suspendre un compte vendeur', '2026-08-06 11:03:51', '2026-08-06 11:04:49'),
(9, 'Voir la liste des livreurs', 'view-couriers', 'Voir la liste des livreurs', '2026-08-06 11:03:51', '2026-08-06 11:04:49'),
(10, 'Valider les comptes et documents KYC des livreurs', 'approve-couriers', 'Valider les comptes et documents KYC des livreurs', '2026-08-06 11:03:51', '2026-08-06 11:04:49'),
(11, 'Modifier ou suspendre un compte livreur', 'edit-couriers', 'Modifier ou suspendre un compte livreur', '2026-08-06 11:03:51', '2026-08-06 11:04:49'),
(12, 'Gérer les zones de livraison et les grilles tarifaires', 'manage-zones-pricing', 'Gérer les zones de livraison et les grilles tarifaires', '2026-08-06 11:03:51', '2026-08-06 11:04:49'),
(13, 'Voir les statistiques et rapports avancés', 'view-statistics', 'Voir les statistiques et rapports avancés', '2026-08-06 11:03:51', '2026-08-06 11:04:49'),
(14, 'Gérer les transactions, commissions et retraits', 'manage-finances', 'Gérer les transactions, commissions et retraits', '2026-08-06 11:03:51', '2026-08-06 11:04:49'),
(15, 'Gérer les paramètres globaux du système', 'manage-system-settings', 'Gérer les paramètres globaux du système', '2026-08-06 11:03:51', '2026-08-06 11:04:49'),
(16, 'Voir le tableau de bord vendeur', 'view-seller-dashboard', 'Voir le tableau de bord vendeur', '2026-08-06 11:03:51', '2026-08-06 11:04:49'),
(17, 'Créer une demande de livraison', 'create-seller-order', 'Créer une demande de livraison', '2026-08-06 11:03:51', '2026-08-06 11:04:49'),
(18, 'Voir l\'historique de mes commandes', 'view-my-orders', 'Voir l\'historique de mes commandes', '2026-08-06 11:03:51', '2026-08-06 11:04:49'),
(19, 'Suivre les livraisons en temps réel (GPS)', 'track-live-deliveries', 'Suivre les livraisons en temps réel (GPS)', '2026-08-06 11:03:51', '2026-08-06 11:04:49'),
(20, 'Consulter et télécharger mes factures', 'view-my-invoices', 'Consulter et télécharger mes factures', '2026-08-06 11:03:51', '2026-08-06 11:04:49'),
(21, 'Gérer mon profil vendeur', 'manage-seller-profile', 'Modifier ses informations d\'entreprise, de contact et de mot de passe.', '2026-08-06 11:03:51', '2026-08-06 11:03:51'),
(22, 'Gérer les clés d\'intégration API', 'manage-seller-api', 'Gérer les clés d\'intégration API', '2026-08-06 11:03:51', '2026-08-06 11:04:49'),
(23, 'Voir les missions de livraison disponibles', 'view-available-missions', 'Voir les missions de livraison disponibles', '2026-08-06 11:03:51', '2026-08-06 11:04:49'),
(24, 'Accepter, refuser ou mettre à jour le statut d\'une mission', 'manage-mission-status', 'Accepter, refuser ou mettre à jour le statut d\'une mission', '2026-08-06 11:03:51', '2026-08-06 11:04:49'),
(25, 'Consulter mes gains et l\'historique de mes revenus', 'view-my-earnings', 'Consulter mes gains et l\'historique de mes revenus', '2026-08-06 11:03:51', '2026-08-06 11:04:49');

-- --------------------------------------------------------

--
-- Structure de la table `permission_role`
--

DROP TABLE IF EXISTS `permission_role`;
CREATE TABLE IF NOT EXISTS `permission_role` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `permission_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `permission_role_permission_id_foreign` (`permission_id`),
  KEY `permission_role_role_id_foreign` (`role_id`)
) ENGINE=MyISAM AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `permission_role`
--

INSERT INTO `permission_role` (`id`, `permission_id`, `role_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, NULL, NULL),
(2, 2, 1, NULL, NULL),
(3, 3, 1, NULL, NULL),
(4, 4, 1, NULL, NULL),
(5, 5, 1, NULL, NULL),
(6, 6, 1, NULL, NULL),
(7, 7, 1, NULL, NULL),
(8, 8, 1, NULL, NULL),
(9, 9, 1, NULL, NULL),
(10, 10, 1, NULL, NULL),
(11, 11, 1, NULL, NULL),
(12, 12, 1, NULL, NULL),
(13, 13, 1, NULL, NULL),
(14, 14, 1, NULL, NULL),
(15, 15, 1, NULL, NULL),
(16, 16, 1, NULL, NULL),
(17, 17, 1, NULL, NULL),
(18, 18, 1, NULL, NULL),
(19, 19, 1, NULL, NULL),
(20, 20, 1, NULL, NULL),
(21, 21, 1, NULL, NULL),
(22, 22, 1, NULL, NULL),
(23, 23, 1, NULL, NULL),
(24, 24, 1, NULL, NULL),
(25, 25, 1, NULL, NULL),
(26, 17, 2, NULL, NULL),
(27, 22, 2, NULL, NULL),
(28, 21, 2, NULL, NULL),
(29, 19, 2, NULL, NULL),
(30, 20, 2, NULL, NULL),
(31, 18, 2, NULL, NULL),
(32, 16, 2, NULL, NULL),
(33, 24, 3, NULL, NULL),
(34, 23, 3, NULL, NULL),
(35, 25, 3, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=MyISAM AUTO_INCREMENT=220 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 'App\\Models\\User', 3, 'auth_token', '256a35de911498d804bc1ce3806e91dc0f362899a9a6872b129007bc56009f7a', '[\"*\"]', NULL, NULL, '2026-07-31 19:58:05', '2026-07-31 19:58:05'),
(2, 'App\\Models\\User', 4, 'auth_token', '21b4b4c4564a4fea7300413685d408ceb5019ffcaca5ebae5ff9eee86c0fd346', '[\"*\"]', NULL, NULL, '2026-07-31 20:00:51', '2026-07-31 20:00:51'),
(3, 'App\\Models\\User', 5, 'auth_token', 'b2810139177cf9fc086f85320b14179ca5f7eaba13eba80c6b4648d83dee9e4d', '[\"*\"]', NULL, NULL, '2026-07-31 20:09:14', '2026-07-31 20:09:14'),
(4, 'App\\Models\\User', 1, 'auth_token', '940a5126e7ba15ed91c46ee426e92d3bb0947c40272406c12f0d93cb12fc859d', '[\"*\"]', NULL, NULL, '2026-07-31 20:14:33', '2026-07-31 20:14:33'),
(5, 'App\\Models\\User', 1, 'auth_token', 'f95e3a2639a7116d9cb39604ac89c0db970cd19ad5c3819ffc5d172afdd051d3', '[\"*\"]', NULL, NULL, '2026-08-01 05:33:51', '2026-08-01 05:33:51'),
(6, 'App\\Models\\User', 1, 'auth_token', 'd6b8f223e56e8c3fb973eb5c2512be4a2bd2791165a2bc300588718dd5b7abc1', '[\"*\"]', NULL, NULL, '2026-08-01 05:35:35', '2026-08-01 05:35:35'),
(7, 'App\\Models\\User', 1, 'auth_token', '9ea7c4dc6b847d785ddfec500fb9df4979e8f1332a0527b60f774d3cbc745092', '[\"*\"]', '2026-08-01 10:47:40', NULL, '2026-08-01 05:47:09', '2026-08-01 10:47:40'),
(8, 'App\\Models\\User', 1, 'auth_token', '3bbf63bb81476e29797673ce573e412763132d580ea463395f9c855c0f2152a5', '[\"*\"]', NULL, NULL, '2026-08-01 06:59:45', '2026-08-01 06:59:45'),
(9, 'App\\Models\\User', 1, 'auth_token', 'afd65afeac7fcab8a9c003602a819601add94605469584572f39ecf987af57e8', '[\"*\"]', NULL, NULL, '2026-08-01 07:41:04', '2026-08-01 07:41:04'),
(10, 'App\\Models\\User', 1, 'auth_token', 'ac0a046ac55009743b3df99379a6734552e0216ff08c7c8f5716453611e3bfd3', '[\"*\"]', '2026-08-01 09:26:05', NULL, '2026-08-01 09:10:06', '2026-08-01 09:26:05'),
(11, 'App\\Models\\User', 1, 'auth_token', 'b479f1890bd7c4ad89cf378579bbc1e377863f49e1183fc2f9dc160fa6ed75ba', '[\"*\"]', NULL, NULL, '2026-08-01 10:15:26', '2026-08-01 10:15:26'),
(12, 'App\\Models\\User', 1, 'auth_token', '4deecc481b7b9941b6d25e992cb97f5894b3e1fc656ac552bab19186ec52373e', '[\"*\"]', '2026-08-02 06:34:09', NULL, '2026-08-01 10:21:02', '2026-08-02 06:34:09'),
(13, 'App\\Models\\User', 2, 'auth_token', '35329cba905437042b9f672e1e2fa0e63523c8c5a364a6612404af846b958bcd', '[\"*\"]', '2026-08-01 21:20:02', NULL, '2026-08-01 10:52:22', '2026-08-01 21:20:02'),
(14, 'App\\Models\\User', 1, 'auth_token', 'ee1593c0d37bcf8e45d1b2364fe9420e0a13e236d74de85a44086aada10bcc1f', '[\"*\"]', '2026-08-02 06:35:12', NULL, '2026-08-02 06:35:00', '2026-08-02 06:35:12'),
(15, 'App\\Models\\User', 2, 'auth_token', '20c4e43db7e3fa9c8913e8f5e859e28aaf30b5db0ea5b1bee65d58541565faa2', '[\"*\"]', '2026-08-02 06:38:35', NULL, '2026-08-02 06:35:41', '2026-08-02 06:38:35'),
(16, 'App\\Models\\User', 1, 'auth_token', '0b8e81b6f20e23c84971e0d3bc638ac623bf2f5eda42d79bc66f0fa83d16709a', '[\"*\"]', '2026-08-02 08:01:25', NULL, '2026-08-02 06:39:23', '2026-08-02 08:01:25'),
(17, 'App\\Models\\User', 1, 'auth_token', '503530e1460b383fdeb2c6a030f06a01ab94f20138c9db7f8f263fe13349503d', '[\"*\"]', '2026-08-02 07:49:37', NULL, '2026-08-02 06:39:55', '2026-08-02 07:49:37'),
(18, 'App\\Models\\User', 3, 'auth_token', '9f6221c97c2dfc64933658aff0ffcb90ef2af4e88c8e6b81d5a17e85bf64b413', '[\"*\"]', '2026-08-02 08:07:21', NULL, '2026-08-02 08:02:56', '2026-08-02 08:07:21'),
(19, 'App\\Models\\User', 3, 'auth_token', 'bcafe45b682b28747f158d37614e0f4ef5c22412d6e5fa6a58e24dc259e230fa', '[\"*\"]', '2026-08-02 08:08:01', NULL, '2026-08-02 08:07:43', '2026-08-02 08:08:01'),
(20, 'App\\Models\\User', 3, 'auth_token', 'a54da20748ff4730af91671f021cc59e7f880813046c0cd6ff4ed9eab0bc7fe0', '[\"*\"]', '2026-08-02 08:09:23', NULL, '2026-08-02 08:09:15', '2026-08-02 08:09:23'),
(21, 'App\\Models\\User', 3, 'auth_token', '915cdca9f59ed3372970d5b9fd2a6784f07404d7139f7ae1daf9ed6ade3e8bf5', '[\"*\"]', NULL, NULL, '2026-08-02 08:11:43', '2026-08-02 08:11:43'),
(22, 'App\\Models\\User', 3, 'auth_token', '934fb01e738635a380d68f056664c74e4b786c76b2bce1e401919815c930eb07', '[\"*\"]', '2026-08-02 08:15:26', NULL, '2026-08-02 08:14:30', '2026-08-02 08:15:26'),
(23, 'App\\Models\\User', 1, 'auth_token', 'caacb9591842b5822a2ebe0d2461b1f5751372aa5125dbd9c17d65e87e0611bd', '[\"*\"]', '2026-08-02 09:56:21', NULL, '2026-08-02 08:18:47', '2026-08-02 09:56:21'),
(24, 'App\\Models\\User', 1, 'auth_token', '4712827d3051ea028d58af1ae52acd3bca30171f9c00d97fdf812c9768480fbc', '[\"*\"]', '2026-08-04 07:06:25', NULL, '2026-08-02 09:39:25', '2026-08-04 07:06:25'),
(25, 'App\\Models\\User', 1, 'auth_token', '63f58738c34d3aeb414c3de20ee05a6aaca3098d05979de30b8f5a85781d9419', '[\"*\"]', '2026-08-02 15:14:55', NULL, '2026-08-02 15:10:45', '2026-08-02 15:14:55'),
(26, 'App\\Models\\User', 1, 'auth_token', '04155c033bd270161db9327374fd1a470eb323c56d9d75c06d8f9d76a35fc69b', '[\"*\"]', '2026-08-02 15:25:16', NULL, '2026-08-02 15:21:33', '2026-08-02 15:25:16'),
(27, 'App\\Models\\User', 1, 'auth_token', '1d8d4023d5251285d5067f4114e43cb43f46da71403a57ac03c6511c7d9bea32', '[\"*\"]', '2026-08-02 16:58:10', NULL, '2026-08-02 15:25:28', '2026-08-02 16:58:10'),
(28, 'App\\Models\\User', 3, 'auth_token', 'cf4ee5dfbaa4b1ec2ff00d8a3f45058b607a03f100bee7ba734a099af3d06b67', '[\"*\"]', '2026-08-02 19:35:29', NULL, '2026-08-02 15:32:30', '2026-08-02 19:35:29'),
(29, 'App\\Models\\User', 1, 'auth_token', '8c0399c14af49a5b1b72546768e7beeb5b16ee0d37b06b1872539f573a8a3b1c', '[\"*\"]', '2026-08-02 17:36:02', NULL, '2026-08-02 16:58:32', '2026-08-02 17:36:02'),
(30, 'App\\Models\\User', 7, 'auth_token', '1964cde750500996eddf3c7a378379cafd4c8c9c0d980e70f5e3ec19b43c81ea', '[\"*\"]', NULL, NULL, '2026-08-02 18:15:34', '2026-08-02 18:15:34'),
(31, 'App\\Models\\User', 7, 'auth_token', 'aa98e0a9d398c7d2bfe289ccf153668fe4ce861666db9c52e99904a919efa825', '[\"*\"]', '2026-08-02 19:35:57', NULL, '2026-08-02 19:35:57', '2026-08-02 19:35:57'),
(42, 'App\\Models\\User', 3, 'auth_token', 'f9e83e4227e8a3a096121e47bb35e4432d6b297b40953fb704341456f1db3d8e', '[\"*\"]', '2026-08-04 05:17:19', NULL, '2026-08-03 05:17:56', '2026-08-04 05:17:19'),
(33, 'App\\Models\\User', 3, 'auth_token', '749d050a8aaac24824b028a996945a7be9e5de53c412b9d5d7bafba821269181', '[\"*\"]', '2026-08-02 22:38:10', NULL, '2026-08-02 21:57:24', '2026-08-02 22:38:10'),
(34, 'App\\Models\\User', 1, 'auth_token', 'a5ca314d075f56dc34a3e6e5309ee682471b9678276ea7b7e29f8bf90230a83d', '[\"*\"]', '2026-08-03 04:29:06', NULL, '2026-08-03 00:02:54', '2026-08-03 04:29:06'),
(35, 'App\\Models\\User', 4, 'auth_token', '97588c50a608c3b23c3cbd428bc33b005cc9d6c4f353874da72c47bb8eecbbc6', '[\"*\"]', '2026-08-03 04:33:18', NULL, '2026-08-03 04:30:14', '2026-08-03 04:33:18'),
(36, 'App\\Models\\User', 1, 'auth_token', 'e29aa5c22d98db91dc0d86aae99df9ccc3fd8fb947871be4f40f7693bb312097', '[\"*\"]', '2026-08-03 04:48:20', NULL, '2026-08-03 04:34:36', '2026-08-03 04:48:20'),
(37, 'App\\Models\\User', 1, 'auth_token', '41d028362f6eaf232ce842d538547e1492d667856213de6c80e484e6abdb56dd', '[\"*\"]', '2026-08-03 04:51:04', NULL, '2026-08-03 04:51:02', '2026-08-03 04:51:04'),
(39, 'App\\Models\\User', 1, 'auth_token', 'c9829c05f4a1bca27638d612d2e8a8fc0312ef510b90c4be72a6187535409444', '[\"*\"]', '2026-08-03 07:30:55', NULL, '2026-08-03 05:01:14', '2026-08-03 07:30:55'),
(56, 'App\\Models\\User', 3, 'auth_token', '49adfd9c5d85a32c67beea23d82d51643484987b08f9e32628e035d1ce693322', '[\"*\"]', '2026-08-04 21:48:21', NULL, '2026-08-04 21:45:02', '2026-08-04 21:48:21'),
(45, 'App\\Models\\User', 9, 'auth_token', '4634dc9c486c1a6490ced03a43857b22403864d0f5680b834490d848dcd2fb1e', '[\"*\"]', NULL, NULL, '2026-08-04 05:32:11', '2026-08-04 05:32:11'),
(61, 'App\\Models\\User', 14, 'auth_token', '173256d46ab1d00b73b41b8f21a4e3e35f0db05496f878995e005c0585a59eac', '[\"*\"]', NULL, NULL, '2026-08-05 09:19:05', '2026-08-05 09:19:05'),
(120, 'App\\Models\\User', 9, 'auth_token', '6359409c3db6b5a9e14852b4e179f3246b0c22e4614c83805fdfd5cf81fc15e9', '[\"*\"]', '2026-08-09 16:58:29', NULL, '2026-08-09 09:16:20', '2026-08-09 16:58:29'),
(59, 'App\\Models\\User', 13, 'auth_token', '29982073a7449c2fd8762f80b4063bac1cd75f08ba3eadf6368f63ca991d15e3', '[\"*\"]', NULL, NULL, '2026-08-05 08:56:02', '2026-08-05 08:56:02'),
(62, 'App\\Models\\User', 15, 'auth_token', '3d4895d196948e639ca70a5e110e6d4a0b7ee9f984b3df6962b72bb6038fd5f9', '[\"*\"]', NULL, NULL, '2026-08-05 09:20:35', '2026-08-05 09:20:35'),
(63, 'App\\Models\\User', 16, 'auth_token', '74798182d037065bd973f7cecb8901bd4e15da08ae20b97b8a94767db98e3c5e', '[\"*\"]', NULL, NULL, '2026-08-05 09:27:37', '2026-08-05 09:27:37'),
(64, 'App\\Models\\User', 17, 'auth_token', '8c50591f740122b040da98d58398413a5cff51ae67477a99de6dd6d03649ef05', '[\"*\"]', NULL, NULL, '2026-08-06 07:26:22', '2026-08-06 07:26:22'),
(65, 'App\\Models\\User', 3, 'auth_token', 'd3fc5aad64c22bd85cdb9acaec6765379ad6e6218215f00a296d504dff7a9620', '[\"*\"]', '2026-08-06 10:52:41', NULL, '2026-08-06 07:28:41', '2026-08-06 10:52:41'),
(66, 'App\\Models\\User', 1, 'auth_token', 'a6aa0f12f7abee65daffa413e330ee81304357e9e5a4a87017fd5266b851ff56', '[\"*\"]', NULL, NULL, '2026-08-06 10:57:03', '2026-08-06 10:57:03'),
(69, 'App\\Models\\User', 2, 'auth_token', 'f39a3dd658171a523b33b14f7ef04e247bc6539642ffba50005c5e9d693f72ca', '[\"*\"]', '2026-08-07 17:01:08', NULL, '2026-08-06 11:14:32', '2026-08-07 17:01:08'),
(70, 'App\\Models\\User', 4, 'auth_token', '868d520c9efc0b4b4ada173c16e780df50a628850931a47914771748f0e37b04', '[\"*\"]', NULL, NULL, '2026-08-06 11:21:47', '2026-08-06 11:21:47'),
(71, 'App\\Models\\User', 5, 'auth_token', 'be3960ac620d2e941e79a1041e75c173e6457bf31b2bd2d57cf99a86c522bbb8', '[\"*\"]', NULL, NULL, '2026-08-06 13:00:25', '2026-08-06 13:00:25'),
(72, 'App\\Models\\User', 6, 'auth_token', 'efc1d0daedf4137cffb46cc84a16894d60687e954932383d194bb3ce922e9d3d', '[\"*\"]', NULL, NULL, '2026-08-06 13:21:14', '2026-08-06 13:21:14'),
(73, 'App\\Models\\User', 7, 'auth_token', '427485d17329a2f7d7e187cc1974be052888f5598084a9432b3f334dee4ee35a', '[\"*\"]', NULL, NULL, '2026-08-06 14:03:11', '2026-08-06 14:03:11'),
(74, 'App\\Models\\User', 8, 'auth_token', '86bc68e0accbee2ff7840a39e5397e4bbcfdcab19da0658d68e84d027ca62cc8', '[\"*\"]', NULL, NULL, '2026-08-06 15:17:25', '2026-08-06 15:17:25'),
(75, 'App\\Models\\User', 9, 'auth_token', '380eaf69e38bee5da24cb4de677e22f909f8654b8c158df747dcc69f0c945768', '[\"*\"]', NULL, NULL, '2026-08-06 15:57:38', '2026-08-06 15:57:38'),
(99, 'App\\Models\\User', 10, 'auth_token', '7253d613a17311ba45b98c9c5368b80f61ef3e7805e54b18ca16fd969e9417bc', '[\"*\"]', NULL, NULL, '2026-08-08 21:01:16', '2026-08-08 21:01:16'),
(77, 'App\\Models\\User', 9, 'auth_token', '401a470e440196ccb7ba637c9c5f53e193b582f2eddb0a41a96d330b89074266', '[\"*\"]', NULL, NULL, '2026-08-08 07:01:59', '2026-08-08 07:01:59'),
(79, 'App\\Models\\User', 9, 'auth_token', '7eb0b545097e68af9e1b3c816f651deff0c02c5156083ae6529e83383adb8537', '[\"*\"]', '2026-08-08 07:21:23', NULL, '2026-08-08 07:21:21', '2026-08-08 07:21:23'),
(80, 'App\\Models\\User', 9, 'auth_token', 'b2efdf8cf73e700de630b102fb632daff8fe55e3ff7b6088ad93ee83993223ea', '[\"*\"]', '2026-08-08 07:27:25', NULL, '2026-08-08 07:26:55', '2026-08-08 07:27:25'),
(81, 'App\\Models\\User', 9, 'auth_token', 'dc3a9b4ca319b2717811ca0f34518c50b0c3d07ab8cd2bcf9f4e00b46bd0cf55', '[\"*\"]', '2026-08-08 08:12:55', NULL, '2026-08-08 08:02:06', '2026-08-08 08:12:55'),
(82, 'App\\Models\\User', 9, 'auth_token', 'b8e4a327ef34a247ef19d58a8cf15a030eb25ff674519fb4137e153485cb873c', '[\"*\"]', '2026-08-08 08:16:06', NULL, '2026-08-08 08:16:00', '2026-08-08 08:16:06'),
(83, 'App\\Models\\User', 9, 'auth_token', 'd392a26ddfc3a4f2b10de7ea1ff88dba29be3a5dad95c326bf9e41321b2ce49a', '[\"*\"]', '2026-08-08 08:22:25', NULL, '2026-08-08 08:22:19', '2026-08-08 08:22:25'),
(84, 'App\\Models\\User', 9, 'auth_token', 'dcef6fc0f6fcb60cfffbdfb49fb4abb1e0888f3d5824b1d1f3afdd7cea63a293', '[\"*\"]', '2026-08-08 08:40:47', NULL, '2026-08-08 08:35:26', '2026-08-08 08:40:47'),
(85, 'App\\Models\\User', 9, 'auth_token', 'f6b2e1b94f88eee6651cbce4a91e4688951e99a789b039f76b00af55a5fe9be3', '[\"*\"]', '2026-08-08 08:48:54', NULL, '2026-08-08 08:48:46', '2026-08-08 08:48:54'),
(86, 'App\\Models\\User', 9, 'auth_token', 'ddd19d84f26bda1205cab0e8f25e7960879cc2c37f6b6b1ab3fb8268586370f8', '[\"*\"]', '2026-08-08 08:58:10', NULL, '2026-08-08 08:57:52', '2026-08-08 08:58:10'),
(87, 'App\\Models\\User', 9, 'auth_token', '3b8f16d0c9d63f8d52d41a703f3fe359c1fd1d5cd6100c5978cb54a63c517491', '[\"*\"]', '2026-08-08 09:04:38', NULL, '2026-08-08 09:03:58', '2026-08-08 09:04:38'),
(88, 'App\\Models\\User', 9, 'auth_token', '652abf8f9b74f005514637e8dfdaad507a411b516cb4b8c4539d09869664a458', '[\"*\"]', '2026-08-08 09:56:07', NULL, '2026-08-08 09:12:35', '2026-08-08 09:56:07'),
(89, 'App\\Models\\User', 9, 'auth_token', '5503058134294249d50f9330265132de29bff31d87f45ce2e447997a72fc55d0', '[\"*\"]', '2026-08-08 15:30:54', NULL, '2026-08-08 15:30:53', '2026-08-08 15:30:54'),
(90, 'App\\Models\\User', 9, 'auth_token', '686ac8c6377d8ce98137f10d555d4a1be9d9843c3609c805d131fc8cbb9cb519', '[\"*\"]', '2026-08-08 15:37:45', NULL, '2026-08-08 15:37:44', '2026-08-08 15:37:45'),
(91, 'App\\Models\\User', 9, 'auth_token', '72cf80d7ff715d1b3cda12d7d1ef66a429d1da502a4e9047976f9b3b98b50cd5', '[\"*\"]', '2026-08-08 16:26:57', NULL, '2026-08-08 15:39:03', '2026-08-08 16:26:57'),
(93, 'App\\Models\\User', 9, 'auth_token', 'e0df02bec9265a6ff364d59950b0e86f472face6144728ba16a05a197a9bced0', '[\"*\"]', '2026-08-08 17:05:37', NULL, '2026-08-08 17:05:30', '2026-08-08 17:05:37'),
(94, 'App\\Models\\User', 9, 'auth_token', '5dbaeec166a340af3af342a64e52a719f64ae616b54c9abaf69bae4be9ecab56', '[\"*\"]', '2026-08-08 19:58:12', NULL, '2026-08-08 19:57:31', '2026-08-08 19:58:12'),
(135, 'App\\Models\\User', 9, 'auth_token', '0cb9752bd48960dfbdd84ce704f4f75b6f101a195d3b9180b831d014d5dacb16', '[\"*\"]', '2026-08-11 08:47:40', NULL, '2026-08-10 19:52:00', '2026-08-11 08:47:40'),
(100, 'App\\Models\\User', 10, 'auth_token', '92500adf6e13398b9b3f1d2a58242a5c117fa9f284d02d3af1108eb896da9f4b', '[\"*\"]', NULL, NULL, '2026-08-08 21:03:57', '2026-08-08 21:03:57'),
(101, 'App\\Models\\User', 9, 'auth_token', '11bf469b39d4300203bc4bb1c349f5eda4929f820972d3aef6c540cd56e410c6', '[\"*\"]', NULL, NULL, '2026-08-08 21:10:52', '2026-08-08 21:10:52'),
(103, 'App\\Models\\User', 9, 'auth_token', 'e1c7bfe627cef6e6fd9d616d6cf43a2b30dc44e24117d264c81b5986be9fd6e5', '[\"*\"]', '2026-08-08 21:16:05', NULL, '2026-08-08 21:16:04', '2026-08-08 21:16:05'),
(104, 'App\\Models\\User', 9, 'auth_token', '810c28577d4ee16b6d2a4b021c32b52140140e757dfca63195cca375ca4632b2', '[\"*\"]', NULL, NULL, '2026-08-09 07:34:08', '2026-08-09 07:34:08'),
(105, 'App\\Models\\User', 9, 'auth_token', '56aca70e8f35418b87063abf5d1fd51cda7cc166bf68b80d8a5676ab2ba29d2b', '[\"*\"]', NULL, NULL, '2026-08-09 07:36:34', '2026-08-09 07:36:34'),
(106, 'App\\Models\\User', 9, 'auth_token', '05ba1ffd33ef54d703388ba7d7fc65b2222539894105818ca71eee96c065fbfb', '[\"*\"]', NULL, NULL, '2026-08-09 07:41:39', '2026-08-09 07:41:39'),
(107, 'App\\Models\\User', 10, 'auth_token', 'ac8de0ba319207a3410ee21dde946ee3c17332394e35f8f2552cebc9ebb73572', '[\"*\"]', NULL, NULL, '2026-08-09 07:44:30', '2026-08-09 07:44:30'),
(110, 'App\\Models\\User', 9, 'auth_token', 'f8d5af226675a871ce84bf45a752e5db8b5cd5bcdb23dba7b191dc2e9d8bdd1d', '[\"*\"]', NULL, NULL, '2026-08-09 07:47:37', '2026-08-09 07:47:37'),
(111, 'App\\Models\\User', 11, 'auth_token', '7b87aa6bce12a6c78f227f693783127bac390b6c8f82105826758b1ccfbb486f', '[\"*\"]', NULL, NULL, '2026-08-09 07:52:06', '2026-08-09 07:52:06'),
(112, 'App\\Models\\User', 11, 'auth_token', '844d4036eb80e25bf1ed4336aababf2c153385567806b1918d50eaceb9702c17', '[\"*\"]', NULL, NULL, '2026-08-09 07:53:36', '2026-08-09 07:53:36'),
(114, 'App\\Models\\User', 9, 'auth_token', '7766cc8d8e1c9a997a0d13507bac0d39e556b197574f77f0bda461126fabbe5b', '[\"*\"]', '2026-08-09 07:56:38', NULL, '2026-08-09 07:56:19', '2026-08-09 07:56:38'),
(115, 'App\\Models\\User', 9, 'auth_token', '7814646fbafb16f2478718ba5c061e63e258fa1f618c3fed885741bc1b3a2b07', '[\"*\"]', NULL, NULL, '2026-08-09 07:57:17', '2026-08-09 07:57:17'),
(116, 'App\\Models\\User', 9, 'auth_token', '0ed597503a762f53540e1d106d40e9e4cafb11cb9c5f0206b75538dc9758acd0', '[\"*\"]', '2026-08-09 08:46:09', NULL, '2026-08-09 07:59:21', '2026-08-09 08:46:09'),
(121, 'App\\Models\\User', 9, 'auth_token', '922a72dd25004fd07911bb3a1d87ef983af3ff5567707d4de2ebb340e79678ce', '[\"*\"]', '2026-08-09 09:20:19', NULL, '2026-08-09 09:20:17', '2026-08-09 09:20:19'),
(122, 'App\\Models\\User', 9, 'auth_token', '5bcd2a58d02328b573d98a9fd6a00dd94d8ae8ae70b571e9bd7309be10bbba5e', '[\"*\"]', '2026-08-09 09:32:53', NULL, '2026-08-09 09:32:51', '2026-08-09 09:32:53'),
(123, 'App\\Models\\User', 9, 'auth_token', '1814045d7654fe28b7fc53a589136877d9ba892585f282f024e691a292cdaa0e', '[\"*\"]', '2026-08-09 10:01:14', NULL, '2026-08-09 09:48:41', '2026-08-09 10:01:14'),
(126, 'App\\Models\\User', 10, 'auth_token', 'f3221d2ae8e211b770992a63b7f1c396fc44ad0fb7998eecc5a2d4d3dc9455c2', '[\"*\"]', NULL, NULL, '2026-08-09 16:52:03', '2026-08-09 16:52:03'),
(149, 'App\\Models\\User', 2, 'auth_token', 'f9f081128e0efa91d42b9709f04673b3bc8f74c2824e8f98310bc0e133b6f87f', '[\"*\"]', '2026-08-13 10:27:14', NULL, '2026-08-11 08:33:19', '2026-08-13 10:27:14'),
(129, 'App\\Models\\User', 10, 'auth_token', '6c6beb764e91da73d17f78a43e9d74b29053fb524c283679d5dce2e7969693ce', '[\"*\"]', NULL, NULL, '2026-08-09 21:15:34', '2026-08-09 21:15:34'),
(130, 'App\\Models\\User', 9, 'auth_token', 'b27ad69366dfb8e6736ac2515fb3935c21b348efe0fbec41d3484cc022737a0c', '[\"*\"]', '2026-08-09 21:41:10', NULL, '2026-08-09 21:41:07', '2026-08-09 21:41:10'),
(131, 'App\\Models\\User', 9, 'auth_token', 'dc907c1082325f75f56059b997b6eaa924624979b8d9352529fb45bb23306e33', '[\"*\"]', '2026-08-10 07:57:28', NULL, '2026-08-10 07:57:26', '2026-08-10 07:57:28'),
(133, 'App\\Models\\User', 9, 'auth_token', 'be1a2446886499e5022a9e61ddbb7fd54192968f7c845791e93865b92ba028a5', '[\"*\"]', '2026-08-10 10:05:11', NULL, '2026-08-10 09:54:32', '2026-08-10 10:05:11'),
(134, 'App\\Models\\User', 9, 'auth_token', 'db2a877f8eeea2fe307244571b4dd7e76490133f5dba18c8b383325e8e914a1b', '[\"*\"]', '2026-08-10 17:06:39', NULL, '2026-08-10 15:37:42', '2026-08-10 17:06:39'),
(137, 'App\\Models\\User', 10, 'auth_token', 'efbf83d5e947959502251bff8d783734086850ee191b01952c29c4988f7f1e2f', '[\"*\"]', NULL, NULL, '2026-08-10 20:40:46', '2026-08-10 20:40:46'),
(138, 'App\\Models\\User', 10, 'auth_token', '54e44886d57a28eaa1d2eabadcfe62917802e460684bfb83511a930d0f65c08c', '[\"*\"]', NULL, NULL, '2026-08-10 20:49:15', '2026-08-10 20:49:15'),
(141, 'App\\Models\\User', 10, 'auth_token', '8a5c457bc2d48107ed14693e0b30152a9c37b5563a4c447ce5784c8e5c8327c8', '[\"*\"]', NULL, NULL, '2026-08-10 20:55:19', '2026-08-10 20:55:19'),
(148, 'App\\Models\\User', 10, 'auth_token', '753924fa766ca55ae53a090f2cbc893acd20274121e8cd93e84746bd6a8520b1', '[\"*\"]', '2026-08-11 07:38:48', NULL, '2026-08-11 07:28:22', '2026-08-11 07:38:48'),
(150, 'App\\Models\\User', 11, 'auth_token', 'db1ba32b2a1af903834f4c44add9ae7c9bbca1847c5d3a6a332109883b364acc', '[\"*\"]', '2026-08-11 08:33:38', NULL, '2026-08-11 08:33:36', '2026-08-11 08:33:38'),
(151, 'App\\Models\\User', 11, 'auth_token', '6d5901d19c6fed02269b59d0e70e16a697ccc70ab62c978bf5258be42d4ad24b', '[\"*\"]', '2026-08-11 09:00:38', NULL, '2026-08-11 08:36:17', '2026-08-11 09:00:38'),
(154, 'App\\Models\\User', 10, 'auth_token', 'dba81b80bd47c95a16157845260cb3162b7f4732f237adce94eb91da53425d38', '[\"*\"]', '2026-08-11 10:16:16', NULL, '2026-08-11 10:16:15', '2026-08-11 10:16:16'),
(156, 'App\\Models\\User', 11, 'auth_token', '2304551dbcb5c6af70d5fb145f381f335dd51a58b3bb8ea53fbcdec02ab4fd49', '[\"*\"]', '2026-08-11 10:35:49', NULL, '2026-08-11 10:28:08', '2026-08-11 10:35:49'),
(184, 'App\\Models\\User', 11, 'auth_token', 'a6017e7d39acd114fabacdcdd46a5578b532b37554492b12a49684aa7b5a2e24', '[\"*\"]', '2026-08-12 15:37:29', NULL, '2026-08-12 15:30:46', '2026-08-12 15:37:29'),
(162, 'App\\Models\\User', 11, 'auth_token', 'fe2d4b2e25e89aa545e7dea003ddf107034f527cd939c32fb352435307d93f69', '[\"*\"]', '2026-08-11 15:49:37', NULL, '2026-08-11 15:27:03', '2026-08-11 15:49:37'),
(169, 'App\\Models\\User', 10, 'auth_token', 'b1184b247cde91562cfe83598bb4694453a2ac42638fbe49ffa70115c69fe81a', '[\"*\"]', '2026-08-11 17:04:47', NULL, '2026-08-11 16:36:59', '2026-08-11 17:04:47'),
(164, 'App\\Models\\User', 11, 'auth_token', '764b44c2cadeeb996d1f339db4ec21fb404f3396072265c600c28347db919e5f', '[\"*\"]', '2026-08-11 16:07:27', NULL, '2026-08-11 15:52:41', '2026-08-11 16:07:27'),
(165, 'App\\Models\\User', 11, 'auth_token', '612f0a60a08c7c984aabb8ec8fd7f9d8b4f2530a0adc2fbe2999e1eabef46290', '[\"*\"]', '2026-08-11 16:13:20', NULL, '2026-08-11 16:13:18', '2026-08-11 16:13:20'),
(166, 'App\\Models\\User', 11, 'auth_token', 'cb4e6edf663da11e18c139755c02e8deafd24738315590028e3c6cbcb0a4a957', '[\"*\"]', '2026-08-11 16:23:51', NULL, '2026-08-11 16:23:49', '2026-08-11 16:23:51'),
(167, 'App\\Models\\User', 11, 'auth_token', 'a13c91942dbe3e4fcaabea2185de14764b0b7c442042bc68f31094704cfaa582', '[\"*\"]', '2026-08-11 16:38:26', NULL, '2026-08-11 16:30:27', '2026-08-11 16:38:26'),
(170, 'App\\Models\\User', 11, 'auth_token', '3519b21e362eb2b82d4947b546c2adc6500535e01185e360158b8eae9f05d293', '[\"*\"]', '2026-08-11 17:04:22', NULL, '2026-08-11 16:42:56', '2026-08-11 17:04:22'),
(179, 'App\\Models\\User', 12, 'auth_token', '4bfd0d71578e9a09464d90384521622b3d60a4e8d22cfc9a393f8a201046cb4b', '[\"*\"]', NULL, NULL, '2026-08-12 09:54:53', '2026-08-12 09:54:53'),
(172, 'App\\Models\\User', 11, 'auth_token', '2b157c4b64b51507bd129468f4842a2c489b458081cadce200e9f83aea55b7af', '[\"*\"]', '2026-08-12 06:27:48', NULL, '2026-08-12 05:07:13', '2026-08-12 06:27:48'),
(173, 'App\\Models\\User', 11, 'auth_token', '2f3395861d94bfdae01a52eab10191c3a91ad13f775c2439641e036ac930a31e', '[\"*\"]', '2026-08-12 06:35:14', NULL, '2026-08-12 06:32:18', '2026-08-12 06:35:14'),
(174, 'App\\Models\\User', 11, 'auth_token', '91625d345ceb847ff3fcf66bb3994767612af6a743b32c7ef99c69cd2afe04b2', '[\"*\"]', '2026-08-12 06:46:40', NULL, '2026-08-12 06:43:58', '2026-08-12 06:46:40'),
(175, 'App\\Models\\User', 11, 'auth_token', 'af98a48530320beef554181fe6bfdda26f8f662c286fd3e8786598776a619934', '[\"*\"]', '2026-08-12 06:56:52', NULL, '2026-08-12 06:53:38', '2026-08-12 06:56:52'),
(176, 'App\\Models\\User', 11, 'auth_token', '2626a0be1c239657251095494bcf7d8eceefd4398110cb6b47e0b25c9b8616e4', '[\"*\"]', '2026-08-12 07:14:04', NULL, '2026-08-12 07:12:08', '2026-08-12 07:14:04'),
(178, 'App\\Models\\User', 10, 'auth_token', 'c779a111f7300811d7afcf264f5553610f384aa6814ddb4828dc4e39ab1288d3', '[\"*\"]', '2026-08-12 11:19:21', NULL, '2026-08-12 09:19:56', '2026-08-12 11:19:21'),
(183, 'App\\Models\\User', 11, 'auth_token', '5bcff44fbb1fe4464d6d088c9b3f6bfc0518061bc6f06dcf655d23374c182434', '[\"*\"]', '2026-08-12 11:16:28', NULL, '2026-08-12 11:12:39', '2026-08-12 11:16:28'),
(186, 'App\\Models\\User', 10, 'auth_token', '5297283684046d74689131f18de099fc8f8e2872d69fd49c3dbb8d4730b82ed2', '[\"*\"]', '2026-08-12 16:42:16', NULL, '2026-08-12 15:34:59', '2026-08-12 16:42:16'),
(187, 'App\\Models\\User', 11, 'auth_token', 'c780870ba57f12be5f3871deb93c7129e09651453f4d842f3ead78ef7fbdab23', '[\"*\"]', '2026-08-12 15:43:27', NULL, '2026-08-12 15:40:51', '2026-08-12 15:43:27'),
(188, 'App\\Models\\User', 11, 'auth_token', '42d433c40d656ae7eb1f63c3267440d1019503a714bf6847103451111d45f294', '[\"*\"]', '2026-08-12 16:14:26', NULL, '2026-08-12 15:45:51', '2026-08-12 16:14:26'),
(189, 'App\\Models\\User', 11, 'auth_token', 'a6dfdae8914d9ca9bcab508ea350be22ea579cc25188cfade6cbd196be8daeef', '[\"*\"]', '2026-08-12 16:30:37', NULL, '2026-08-12 16:26:32', '2026-08-12 16:30:37'),
(190, 'App\\Models\\User', 11, 'auth_token', 'c59014e6029170724553c3e14c0c5525ee6b33230ba25b065e5463d2df081661', '[\"*\"]', '2026-08-12 16:41:27', NULL, '2026-08-12 16:38:46', '2026-08-12 16:41:27'),
(193, 'App\\Models\\User', 10, 'auth_token', '8eba3a868c7eab12241f180985e6a59d2d91bd4e47ee0603d1311e0e8344a1d0', '[\"*\"]', '2026-08-12 21:59:00', NULL, '2026-08-12 21:43:40', '2026-08-12 21:59:00'),
(192, 'App\\Models\\User', 11, 'auth_token', 'f1a323e7ec12e7a55375bceb3dc0c32f75ac6f7c4ebef032ace234371a56fa92', '[\"*\"]', '2026-08-12 21:59:15', NULL, '2026-08-12 21:43:05', '2026-08-12 21:59:15'),
(194, 'App\\Models\\User', 11, 'auth_token', 'cd4d9f6fa9699891d46c5fdc8411067b06d617a690c5673f65768f756b38851b', '[\"*\"]', '2026-08-13 10:03:23', NULL, '2026-08-13 06:26:51', '2026-08-13 10:03:23'),
(195, 'App\\Models\\User', 10, 'auth_token', '3e3df674599934fdd3bb74d6165bfb65c9fa9d800213d87c6f5176559ba6a0bc', '[\"*\"]', '2026-08-13 06:36:17', NULL, '2026-08-13 06:28:18', '2026-08-13 06:36:17'),
(196, 'App\\Models\\User', 10, 'auth_token', '595b0a4f6baf2359cbac99e57b1bc18f72d155a908a0e85fc666cd570bc47911', '[\"*\"]', '2026-08-13 07:41:52', NULL, '2026-08-13 06:37:56', '2026-08-13 07:41:52'),
(197, 'App\\Models\\User', 10, 'auth_token', '7bff881dc75ed58a7a7dcd803109d80b8c97e1a6445b348955993df122ed0aca', '[\"*\"]', '2026-08-13 09:20:21', NULL, '2026-08-13 08:59:19', '2026-08-13 09:20:21'),
(198, 'App\\Models\\User', 10, 'auth_token', 'b6e8f8b7b3e976473347d3ff98b1744647ac92ee6fee46ab91b9c975ec4d4da9', '[\"*\"]', '2026-08-13 09:35:57', NULL, '2026-08-13 09:35:34', '2026-08-13 09:35:57'),
(199, 'App\\Models\\User', 10, 'auth_token', '554356a26be0c09ebddb886e05bd93db65757d1596f45001843873b5a3271f87', '[\"*\"]', '2026-08-13 10:07:58', NULL, '2026-08-13 10:02:36', '2026-08-13 10:07:58'),
(200, 'App\\Models\\User', 11, 'auth_token', 'e6415d9e73a098d0da5219555dad71e3a2af3045b12d941b98fee47ef49c75e3', '[\"*\"]', '2026-08-13 10:15:10', NULL, '2026-08-13 10:12:02', '2026-08-13 10:15:10'),
(201, 'App\\Models\\User', 10, 'auth_token', 'ad76471b5f66af71e0f088fb2d348245efaff3cae8f4cc6442799bcb6bada177', '[\"*\"]', '2026-08-13 10:25:30', NULL, '2026-08-13 10:12:29', '2026-08-13 10:25:30'),
(202, 'App\\Models\\User', 10, 'auth_token', '19e9eafd4a6481329404a39a171d2add8850972bef6e9d69a45658514d6f54eb', '[\"*\"]', '2026-08-13 10:40:10', NULL, '2026-08-13 10:35:46', '2026-08-13 10:40:10'),
(204, 'App\\Models\\User', 11, 'auth_token', '40f581d7ef61bf977033e44258087bbdb711467a2476043d1b895fd22dc86bf1', '[\"*\"]', '2026-08-13 11:59:43', NULL, '2026-08-13 10:42:13', '2026-08-13 11:59:43'),
(205, 'App\\Models\\User', 10, 'auth_token', 'cc6172d9c5b31c85077441b3504de19e427139b4fb552d1a1f5df86411a345b0', '[\"*\"]', '2026-08-13 10:44:54', NULL, '2026-08-13 10:42:37', '2026-08-13 10:44:54'),
(206, 'App\\Models\\User', 10, 'auth_token', 'ea907821b923a936f73cb7ea127a8d61a01efca1915d56c8ea0a7679e7b443cc', '[\"*\"]', '2026-08-13 10:50:20', NULL, '2026-08-13 10:45:49', '2026-08-13 10:50:20'),
(207, 'App\\Models\\User', 10, 'auth_token', 'a6dbb67e93237e7e35ad7a34528eec4c768f7b2c9ac7ac50b635bc04ea3acb10', '[\"*\"]', '2026-08-13 11:01:49', NULL, '2026-08-13 10:51:06', '2026-08-13 11:01:49'),
(208, 'App\\Models\\User', 10, 'auth_token', '515e4e753a4efd474dd572ce287eab14e11107292a23e5fcecc738b5418efb93', '[\"*\"]', '2026-08-13 11:05:50', NULL, '2026-08-13 11:03:32', '2026-08-13 11:05:50'),
(209, 'App\\Models\\User', 10, 'auth_token', '867db11478f7d825193b4813dfa14be2719d8fa1d3df5938524b133e156f4427', '[\"*\"]', '2026-08-13 11:19:03', NULL, '2026-08-13 11:16:57', '2026-08-13 11:19:03'),
(210, 'App\\Models\\User', 10, 'auth_token', 'f0b600783e7ea093efb637d32b658de06dbfccf802b7b7f03b678a9f2b948a0b', '[\"*\"]', '2026-08-13 12:03:58', NULL, '2026-08-13 11:59:14', '2026-08-13 12:03:58'),
(211, 'App\\Models\\User', 10, 'auth_token', 'c29eacada689cc68d914a661495bfcb452aacedde155a280a09360969571311a', '[\"*\"]', '2026-08-13 12:31:02', NULL, '2026-08-13 12:20:55', '2026-08-13 12:31:02'),
(212, 'App\\Models\\User', 10, 'auth_token', '6d3a64bff2f7ec3ca3c9c527034ae40c099a706f949bc81dd76a3132592bcd05', '[\"*\"]', '2026-08-13 12:24:59', NULL, '2026-08-13 12:24:56', '2026-08-13 12:24:59'),
(219, 'App\\Models\\User', 11, 'auth_token', 'c50b9386cf9f8356042bfff68cea55be3baa1e288e70e736f9a5254277e814a1', '[\"*\"]', '2026-08-13 16:54:04', NULL, '2026-08-13 15:52:08', '2026-08-13 16:54:04'),
(214, 'App\\Models\\User', 10, 'auth_token', '7d73ae5f759c7883718a575b1abbc9bdaac368036d8779808f1123224da4cd9e', '[\"*\"]', '2026-08-13 13:00:20', NULL, '2026-08-13 12:37:13', '2026-08-13 13:00:20'),
(215, 'App\\Models\\User', 10, 'auth_token', 'e0c5a6cd75e5a73186c615dbe1e21cab9e1850a7ef508f0f183a9af1fb347f92', '[\"*\"]', '2026-08-13 13:08:10', NULL, '2026-08-13 13:04:24', '2026-08-13 13:08:10'),
(217, 'App\\Models\\User', 11, 'auth_token', '049e94029354582099baa901cae2bcdc224f609b525948e95602a32db9ff6e0e', '[\"*\"]', '2026-08-13 14:58:34', NULL, '2026-08-13 14:51:18', '2026-08-13 14:58:34'),
(218, 'App\\Models\\User', 10, 'auth_token', '7e005e42db71638b729ce4eb7d3fa828ff39bb64d8e3e3b359b1f849e87597d2', '[\"*\"]', '2026-08-13 17:01:56', NULL, '2026-08-13 15:51:04', '2026-08-13 17:01:56');

-- --------------------------------------------------------

--
-- Structure de la table `ratings`
--

DROP TABLE IF EXISTS `ratings`;
CREATE TABLE IF NOT EXISTS `ratings` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `rater_id` bigint UNSIGNED NOT NULL,
  `rated_id` bigint UNSIGNED NOT NULL,
  `delivery_id` bigint UNSIGNED DEFAULT NULL,
  `rating` tinyint UNSIGNED NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ratings_rater_id_delivery_id_unique` (`rater_id`,`delivery_id`),
  KEY `ratings_rated_id_foreign` (`rated_id`),
  KEY `ratings_delivery_id_foreign` (`delivery_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_slug_unique` (`slug`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `roles`
--

INSERT INTO `roles` (`id`, `name`, `slug`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Administrateur', 'admin', 'Accès complet à la plateforme (Back-office)', '2026-08-06 11:04:49', '2026-08-06 11:04:49'),
(2, 'Vendeur', 'vendeur', 'Commerçant ou vendeur en ligne gérant ses livraisons', '2026-08-06 11:04:49', '2026-08-06 11:04:49'),
(3, 'Livreur', 'livreur', 'Livreur indépendant acceptant et réalisant les missions', '2026-08-06 11:04:49', '2026-08-06 11:04:49');

-- --------------------------------------------------------

--
-- Structure de la table `role_user`
--

DROP TABLE IF EXISTS `role_user`;
CREATE TABLE IF NOT EXISTS `role_user` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `role_user_user_id_foreign` (`user_id`),
  KEY `role_user_role_id_foreign` (`role_id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `role_user`
--

INSERT INTO `role_user` (`id`, `user_id`, `role_id`, `created_at`, `updated_at`) VALUES
(1, 2, 1, NULL, NULL),
(2, 3, 2, NULL, NULL),
(3, 4, 2, NULL, NULL),
(4, 5, 2, NULL, NULL),
(5, 6, 2, NULL, NULL),
(6, 7, 2, NULL, NULL),
(7, 8, 2, NULL, NULL),
(8, 9, 2, NULL, NULL),
(9, 10, 3, NULL, NULL),
(10, 11, 2, NULL, NULL),
(13, 12, 3, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `sellers`
--

DROP TABLE IF EXISTS `sellers`;
CREATE TABLE IF NOT EXISTS `sellers` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `company_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `password` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_front_path` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_back_path` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `selfie_path` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('en_attente','valide','rejete','suspendu') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en_attente',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sellers_email_unique` (`email`),
  KEY `fk_sellers_users` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `sellers`
--

INSERT INTO `sellers` (`id`, `user_id`, `company_name`, `email`, `phone`, `country`, `city`, `address`, `password`, `id_front_path`, `id_back_path`, `selfie_path`, `status`, `remember_token`, `created_at`, `updated_at`, `email_verified_at`) VALUES
(6, 9, 'boutique', 'tchaboueassana@gmail.com', '01553366', 'GA', 'Franceville', '12 rue', NULL, 'sellers/kyc/6a74bd008db63.webp', 'sellers/kyc/6a74bd0109b69.webp', 'sellers/kyc/6a74bd0199f04.webp', 'valide', NULL, '2026-08-06 15:57:38', '2026-08-08 16:14:54', NULL),
(7, 11, 'stephane sarl', 'stephane@gmail.com', '0154320947', 'GA', 'Port-Gentil', '3 Rue de Verdun', NULL, 'sellers/kyc/6a783fb52d6d9.webp', 'sellers/kyc/6a783fb59b9ab.webp', 'sellers/kyc/6a783fb628aea.webp', 'suspendu', NULL, '2026-08-09 07:52:06', '2026-08-10 07:49:07', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('WSCIU4jSWXIc9kXZ4K3Ir9pFyB3LLezhS7AX0QF8', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', 'eyJfdG9rZW4iOiJZaGlLcDRCUWdpdXdheE4ydDQ1Z1BUT2ZzUUtVZGZUNlBOYkI5dE5xIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1785417854),
('g9UpXj5gHhYvqdFH6BfXclgpjqWRoCmt0E8ti1Uw', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiIxa3B6SlE3QllsSGNDNjV3aEVoQ2dQT1VjOUllT3NGTDBHUFhWTW81IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1785686691),
('uiz6THNB0pC5pzdUj1PlDMVnFx7LN85Sk7JVdfSg', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 Edg/151.0.0.0', 'eyJfdG9rZW4iOiJyYnBrQk5qamxJT3p3M2NsNUdsazJKUXk2ckRDSlRNU2FWUWdvNEZxIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL3VubWFuaXB1bGFibGUtY3JlZWRsZXNzLXBlZHJvLm5ncm9rLWZyZWUuZGV2Iiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1786626814),
('7kk5KePcXnS8DlPjT24pGoNE0M3XwBHiGbFsGxAh', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 Edg/151.0.0.0', 'eyJfdG9rZW4iOiJIZHpVUXhweUtkUTlaOXA1ZnNPeVo4OE5ESTVvMDBSMGwwQ3BncjVpIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL3VubWFuaXB1bGFibGUtY3JlZWRsZXNzLXBlZHJvLm5ncm9rLWZyZWUuZGV2Iiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1786626815);

-- --------------------------------------------------------

--
-- Structure de la table `subscriptions`
--

DROP TABLE IF EXISTS `subscriptions`;
CREATE TABLE IF NOT EXISTS `subscriptions` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `seller_id` bigint UNSIGNED NOT NULL,
  `plan_type` enum('monthly','yearly') COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('active','expired','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `amount` decimal(10,2) NOT NULL,
  `transaction_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subscriptions_seller_id_foreign` (`seller_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
CREATE TABLE IF NOT EXISTS `transactions` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `type` enum('commission','subscription','service_fee','payout') COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('pending','completed','failed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `description` text COLLATE utf8mb4_unicode_ci,
  `reference` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `transactions_reference_unique` (`reference`),
  KEY `transactions_user_id_foreign` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `is_verified` tinyint(1) NOT NULL DEFAULT '0',
  `status` enum('active','inactive','suspended') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `avatar` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `api_token` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_api_token_unique` (`api_token`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `name`, `last_name`, `phone`, `address`, `is_verified`, `status`, `avatar`, `api_token`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(9, 'tchaboueassana@gmail.com', '$2y$12$gyUQiIIkrG1RNj9AXiM/dOLf10JgIVy9/ioOJIAWxp3AcMTpdicdK', 'boutique', NULL, '01553366', NULL, 0, 'active', NULL, NULL, NULL, '2026-08-06 15:57:38', '2026-08-08 16:49:55', NULL),
(2, 'admin@gabonlivraison.com', '$2y$12$QxKRr3e7/9as.PX7x85Ouu7U.oT2wcwmaBSNeF7lMP40VQ3PD6kYy', 'Administrateur Principal', NULL, NULL, NULL, 0, 'active', NULL, NULL, NULL, '2026-08-06 11:04:50', '2026-08-06 11:04:50', NULL),
(3, 'vendeur@test.com', '$2y$12$DP.gGLoawl9U2no54JnUPuslWcWwmdN6dlpM9dpbQE0jEL6.3eIdS', 'Super Market Plus', NULL, NULL, NULL, 0, 'active', NULL, NULL, NULL, '2026-08-06 11:04:50', '2026-08-06 11:04:50', NULL),
(10, 'rahmanedev02@gmail.com', '$2y$12$U87bj2tyQkMQ3L.7UsC3geyC/opYWuSC7Oa7/B5rKnb8c/feij6SO', 'al-oussein Tchaboue', NULL, '+2290154320947', NULL, 0, 'active', NULL, NULL, NULL, '2026-08-08 21:01:16', '2026-08-11 07:33:28', NULL),
(11, 'stephane@gmail.com', '$2y$12$d93DnqI39FSsIhM5FvNFju3ZL4J/tlVTsbOUlK.CPoZTIiPpX9dEK', 'stephane sarl', NULL, '0154320947', NULL, 0, 'active', NULL, NULL, NULL, '2026-08-09 07:52:06', '2026-08-09 07:55:32', NULL),
(12, 'tanko02@gmail.com', '$2y$12$9QXI3gL0j7ai353XoqwRfu/ne2s2PiVigFwJiqUFtcnLqkJBZr4/a', 'Amadou Tanko', NULL, '+22954320947', NULL, 0, 'active', NULL, NULL, NULL, '2026-08-12 09:54:53', '2026-08-12 09:54:53', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `zones`
--

DROP TABLE IF EXISTS `zones`;
CREATE TABLE IF NOT EXISTS `zones` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `nom` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tarif_km` decimal(10,2) NOT NULL DEFAULT '0.00',
  `statut` enum('actif','inactif') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'actif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `zones_code_unique` (`code`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `zones`
--

INSERT INTO `zones` (`id`, `nom`, `code`, `tarif_km`, `statut`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Haut-Ogooué (Franceville)', 'HAU', 200.00, 'actif', '2026-08-09 19:58:35', '2026-08-09 19:58:35', NULL),
(2, 'Nyanga (Tchibanga)', 'NYA', 200.00, 'actif', '2026-08-09 19:58:35', '2026-08-09 19:58:35', NULL);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
