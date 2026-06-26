-- MySQL dump 10.13  Distrib 8.0.46, for Linux (x86_64)
--
-- Host: localhost    Database: lakeview_sweets
-- ------------------------------------------------------
-- Server version	8.0.46-0ubuntu0.24.04.3

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `branches`
--

DROP TABLE IF EXISTS `branches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `branches` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `phones` json DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `branches_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `branches`
--

LOCK TABLES `branches` WRITE;
/*!40000 ALTER TABLE `branches` DISABLE KEYS */;
INSERT INTO `branches` VALUES (1,'Nalta (Opposite Ahsania Petrol Pump)','nalta-opposite-ahsania-petrol-pump-Dk11F',NULL,'[\"+8801968101984\"]',NULL,1,1,'2026-06-26 06:29:40','2026-06-26 06:29:40'),(2,'AB Bank, Adjacent to Satkhira','ab-bank-adjacent-to-satkhira-i3F1t',NULL,'[\"+8801958216722\"]',NULL,1,2,'2026-06-26 06:29:40','2026-06-26 06:29:40'),(3,'Adjacent to Sadar Hospital','adjacent-to-sadar-hospital-bgydP',NULL,'[\"+8801958611949\"]',NULL,1,3,'2026-06-26 06:29:40','2026-06-26 06:29:40'),(4,'Adjacent to Land Office','adjacent-to-land-office-b7EOY',NULL,'[\"01968-101984\"]',NULL,1,4,'2026-06-26 06:29:40','2026-06-26 06:29:40'),(5,'Adjacent to Govt Girls School','adjacent-to-govt-girls-school-VU1SK',NULL,'[\"01958611946\"]',NULL,1,5,'2026-06-26 06:29:40','2026-06-26 06:29:40'),(6,'Old Satkhira Hatkhola Mor','old-satkhira-hatkhola-mor-6Nb6v',NULL,'[\"01958216723\"]',NULL,1,6,'2026-06-26 06:29:40','2026-06-26 06:29:40'),(7,'Lake View Cafe & Restaurant (Main)','lake-view-cafe-restaurant-main-Ya3pt',NULL,'[\"+8801722554400\"]',NULL,1,7,'2026-06-26 06:29:40','2026-06-26 06:29:40');
/*!40000 ALTER TABLE `branches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Fast Food','fast-food-9zBNi',NULL,NULL,1,1,'2026-06-26 06:29:39','2026-06-26 06:29:39'),(2,'Bread','bread-mEfDa',NULL,NULL,1,2,'2026-06-26 06:29:39','2026-06-26 06:29:39'),(3,'Cookies','cookies-4xkrZ',NULL,NULL,1,3,'2026-06-26 06:29:39','2026-06-26 06:29:39'),(4,'Toast','toast-MqNTg',NULL,NULL,1,4,'2026-06-26 06:29:39','2026-06-26 06:29:39'),(5,'Dessert','dessert-vTd8H',NULL,NULL,1,5,'2026-06-26 06:29:39','2026-06-26 06:29:39'),(6,'Cake','cake-TqTfV',NULL,NULL,1,6,'2026-06-26 06:29:39','2026-06-26 06:29:39'),(7,'Order Cake','order-cake-dBP2x',NULL,NULL,1,7,'2026-06-26 06:29:39','2026-06-26 06:29:39'),(8,'Sweets','sweets-YBrfW',NULL,NULL,1,8,'2026-06-26 06:29:39','2026-06-26 06:29:39');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coupons`
--

DROP TABLE IF EXISTS `coupons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `coupons` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('percentage','fixed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'percentage',
  `value` decimal(10,2) NOT NULL,
  `min_order_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `max_discount_amount` decimal(10,2) DEFAULT NULL,
  `usage_limit` int DEFAULT NULL,
  `used_count` int NOT NULL DEFAULT '0',
  `starts_at` datetime DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `coupons_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coupons`
--

LOCK TABLES `coupons` WRITE;
/*!40000 ALTER TABLE `coupons` DISABLE KEYS */;
INSERT INTO `coupons` VALUES (1,'WELCOME10','percentage',10.00,200.00,NULL,NULL,1,NULL,NULL,1,'2026-06-26 06:29:40','2026-06-26 08:22:19'),(2,'FLAT50','fixed',50.00,500.00,NULL,NULL,0,NULL,NULL,1,'2026-06-26 06:29:40','2026-06-26 06:29:40');
/*!40000 ALTER TABLE `coupons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `custom_cake_orders`
--

DROP TABLE IF EXISTS `custom_cake_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `custom_cake_orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `order_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `delivery_man_id` bigint unsigned DEFAULT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_address` text COLLATE utf8mb4_unicode_ci,
  `delivery_type` enum('pickup','home_delivery') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pickup',
  `delivery_area_id` bigint unsigned DEFAULT NULL,
  `cake_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cake_size` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cake_flavor` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message_on_cake` text COLLATE utf8mb4_unicode_ci,
  `delivery_date` date NOT NULL,
  `delivery_time` time DEFAULT NULL,
  `design_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estimated_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `delivery_charge` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `advance_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `transaction_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_verified` tinyint(1) NOT NULL DEFAULT '0',
  `payment_method` enum('cash_on_delivery') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cash_on_delivery',
  `status` enum('pending','confirmed','preparing','ready','delivered','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `payment_status` enum('unpaid','paid') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unpaid',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `admin_notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `custom_cake_orders_order_number_unique` (`order_number`),
  KEY `custom_cake_orders_branch_id_foreign` (`branch_id`),
  KEY `custom_cake_orders_delivery_area_id_foreign` (`delivery_area_id`),
  KEY `custom_cake_orders_user_id_foreign` (`user_id`),
  KEY `custom_cake_orders_delivery_man_id_foreign` (`delivery_man_id`),
  CONSTRAINT `custom_cake_orders_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `custom_cake_orders_delivery_area_id_foreign` FOREIGN KEY (`delivery_area_id`) REFERENCES `delivery_areas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `custom_cake_orders_delivery_man_id_foreign` FOREIGN KEY (`delivery_man_id`) REFERENCES `delivery_men` (`id`) ON DELETE SET NULL,
  CONSTRAINT `custom_cake_orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `custom_cake_orders`
--

LOCK TABLES `custom_cake_orders` WRITE;
/*!40000 ALTER TABLE `custom_cake_orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `custom_cake_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `delivery_areas`
--

DROP TABLE IF EXISTS `delivery_areas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `delivery_areas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `zone_type` enum('sadar','outside_sadar') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'sadar',
  `delivery_charge` decimal(10,2) NOT NULL DEFAULT '0.00',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `delivery_areas_branch_id_foreign` (`branch_id`),
  CONSTRAINT `delivery_areas_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=162 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `delivery_areas`
--

LOCK TABLES `delivery_areas` WRITE;
/*!40000 ALTER TABLE `delivery_areas` DISABLE KEYS */;
INSERT INTO `delivery_areas` VALUES (15,NULL,'Satkhira Sadar','sadar',100.00,1,'2026-06-26 07:50:13','2026-06-26 08:27:43'),(16,NULL,'Binerpota','sadar',100.00,1,'2026-06-26 07:50:13','2026-06-26 08:27:43'),(17,NULL,'Bhatsa','sadar',100.00,1,'2026-06-26 07:50:13','2026-06-26 08:27:43'),(18,NULL,'Khalilnagar','sadar',100.00,1,'2026-06-26 07:50:13','2026-06-26 08:27:43'),(19,NULL,'Sultanpur','sadar',100.00,1,'2026-06-26 07:50:13','2026-06-26 08:27:43'),(20,NULL,'Kulia','sadar',100.00,1,'2026-06-26 07:50:13','2026-06-26 08:27:43'),(21,NULL,'Munshigonj','sadar',100.00,1,'2026-06-26 07:50:13','2026-06-26 08:27:43'),(22,NULL,'Brahmarajpur','sadar',100.00,1,'2026-06-26 07:50:13','2026-06-26 08:27:43'),(23,NULL,'Nimtala','sadar',100.00,1,'2026-06-26 07:50:13','2026-06-26 08:27:43'),(24,NULL,'Palashpol','sadar',100.00,1,'2026-06-26 07:50:13','2026-06-26 08:27:43'),(25,NULL,'Labsha','sadar',100.00,1,'2026-06-26 07:50:13','2026-06-26 08:27:43'),(26,NULL,'Parulia','sadar',100.00,1,'2026-06-26 07:50:13','2026-06-26 08:27:43'),(27,NULL,'Bhadra','sadar',100.00,1,'2026-06-26 07:50:13','2026-06-26 08:27:43'),(28,NULL,'Asashuni Sadar','sadar',100.00,1,'2026-06-26 07:50:13','2026-06-26 08:27:43'),(29,NULL,'Debhata','outside_sadar',200.00,1,'2026-06-26 07:50:13','2026-06-26 08:27:43'),(30,NULL,'Kaliganj','outside_sadar',200.00,1,'2026-06-26 07:50:13','2026-06-26 08:27:43'),(31,NULL,'Assasuni','outside_sadar',200.00,1,'2026-06-26 07:50:13','2026-06-26 08:27:43'),(32,NULL,'Shyamnagar','outside_sadar',200.00,1,'2026-06-26 07:50:13','2026-06-26 08:27:43'),(33,NULL,'Kalaroa','outside_sadar',200.00,1,'2026-06-26 07:50:13','2026-06-26 08:27:43'),(34,NULL,'Tala','outside_sadar',200.00,1,'2026-06-26 07:50:13','2026-06-26 08:27:43'),(35,NULL,'Satkhira Sadar (Rural)','outside_sadar',200.00,1,'2026-06-26 07:50:13','2026-06-26 08:27:43');
/*!40000 ALTER TABLE `delivery_areas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `delivery_men`
--

DROP TABLE IF EXISTS `delivery_men`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `delivery_men` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` bigint unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `delivery_men_phone_unique` (`phone`),
  KEY `delivery_men_branch_id_foreign` (`branch_id`),
  CONSTRAINT `delivery_men_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `delivery_men`
--

LOCK TABLES `delivery_men` WRITE;
/*!40000 ALTER TABLE `delivery_men` DISABLE KEYS */;
/*!40000 ALTER TABLE `delivery_men` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2024_01_01_000001_create_categories_table',1),(5,'2024_01_01_000002_create_branches_table',1),(6,'2024_01_01_000003_create_delivery_areas_table',1),(7,'2024_01_01_000004_create_products_table',1),(8,'2024_01_01_000005_create_coupons_table',1),(9,'2024_01_01_000006_create_orders_table',1),(10,'2024_01_01_000007_create_order_items_table',1),(11,'2024_01_01_000008_create_custom_cake_orders_table',1),(12,'2024_01_01_000009_create_settings_table',1),(13,'2024_01_01_000010_add_user_id_to_orders',2),(14,'2024_01_01_000011_create_delivery_men_table',3),(15,'2024_01_01_000012_add_delivery_man_and_payment_status_to_orders',3),(16,'2024_01_01_000013_make_delivery_area_branch_nullable',3),(17,'2024_01_01_000014_add_advance_payment_fields_to_orders',4);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned DEFAULT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_items_order_id_foreign` (`order_id`),
  KEY `order_items_product_id_foreign` (`product_id`),
  CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES (1,1,59,'1kg Cake',1200.00,1,1200.00,'2026-06-26 08:13:30','2026-06-26 08:13:30'),(2,1,49,'Black Forest 1/2kg Cake',600.00,1,600.00,'2026-06-26 08:13:30','2026-06-26 08:13:30'),(3,2,59,'1kg Cake',1200.00,2,2400.00,'2026-06-26 08:22:20','2026-06-26 08:22:20'),(4,2,49,'Black Forest 1/2kg Cake',600.00,1,600.00,'2026-06-26 08:22:20','2026-06-26 08:22:20'),(5,2,92,'Rasmalai 1kg',320.00,1,320.00,'2026-06-26 08:22:20','2026-06-26 08:22:20');
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `order_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `delivery_man_id` bigint unsigned DEFAULT NULL,
  `delivery_area_id` bigint unsigned DEFAULT NULL,
  `coupon_id` bigint unsigned DEFAULT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_address` text COLLATE utf8mb4_unicode_ci,
  `delivery_type` enum('pickup','home_delivery') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pickup',
  `subtotal` decimal(10,2) NOT NULL,
  `delivery_charge` decimal(10,2) NOT NULL DEFAULT '0.00',
  `discount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total` decimal(10,2) NOT NULL,
  `advance_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `transaction_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_verified` tinyint(1) NOT NULL DEFAULT '0',
  `payment_method` enum('cash_on_delivery') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cash_on_delivery',
  `status` enum('pending','confirmed','preparing','ready','out_for_delivery','delivered','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `payment_status` enum('unpaid','paid') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unpaid',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_order_number_unique` (`order_number`),
  KEY `orders_branch_id_foreign` (`branch_id`),
  KEY `orders_delivery_area_id_foreign` (`delivery_area_id`),
  KEY `orders_coupon_id_foreign` (`coupon_id`),
  KEY `orders_user_id_foreign` (`user_id`),
  KEY `orders_delivery_man_id_foreign` (`delivery_man_id`),
  CONSTRAINT `orders_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `orders_coupon_id_foreign` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) ON DELETE SET NULL,
  CONSTRAINT `orders_delivery_area_id_foreign` FOREIGN KEY (`delivery_area_id`) REFERENCES `delivery_areas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `orders_delivery_man_id_foreign` FOREIGN KEY (`delivery_man_id`) REFERENCES `delivery_men` (`id`) ON DELETE SET NULL,
  CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,2,'ORD-20260626-3G8NI5',2,NULL,NULL,NULL,'Mir Javed','01772788676',NULL,'pickup',1800.00,0.00,0.00,1800.00,0.00,NULL,0,'cash_on_delivery','pending','unpaid',NULL,'2026-06-26 08:13:30','2026-06-26 08:13:30'),(2,2,'ORD-20260626-Y3OAQW',7,NULL,NULL,1,'Mir Javed','01772788676','Satkhira, Satkhira','home_delivery',3320.00,100.00,332.00,3088.00,0.00,NULL,0,'cash_on_delivery','preparing','paid',NULL,'2026-06-26 08:22:20','2026-06-26 08:46:57');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `price` decimal(10,2) NOT NULL,
  `discount_price` decimal(10,2) DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gallery` json DEFAULT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT '1',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_slug_unique` (`slug`),
  KEY `products_category_id_foreign` (`category_id`),
  CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=103 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,1,'Burger','burger-StQVG',NULL,70.00,NULL,NULL,NULL,1,0,1,'2026-06-26 06:29:39','2026-06-26 06:29:39'),(2,1,'Cheese Burger','cheese-burger-XCRv0',NULL,100.00,NULL,NULL,NULL,1,0,2,'2026-06-26 06:29:39','2026-06-26 06:29:39'),(3,1,'Hot Spicy','hot-spicy-q99JY',NULL,60.00,NULL,NULL,NULL,1,0,3,'2026-06-26 06:29:39','2026-06-26 06:29:39'),(4,1,'Mini Pizza','mini-pizza-3DxPG',NULL,70.00,NULL,NULL,NULL,1,0,4,'2026-06-26 06:29:39','2026-06-26 06:29:39'),(5,1,'Pizza','pizza-Wrbtn',NULL,80.00,NULL,NULL,NULL,1,0,5,'2026-06-26 06:29:39','2026-06-26 06:29:39'),(6,1,'French Pizza','french-pizza-fly6x',NULL,80.00,NULL,NULL,NULL,1,0,6,'2026-06-26 06:29:39','2026-06-26 06:29:39'),(7,1,'Patties 40','patties-40-xG0Lw',NULL,40.00,NULL,NULL,NULL,1,0,7,'2026-06-26 06:29:39','2026-06-26 06:29:39'),(8,1,'Meatloaf','meatloaf-uHp2k',NULL,90.00,NULL,NULL,NULL,1,0,8,'2026-06-26 06:29:39','2026-06-26 06:29:39'),(9,1,'Pop Roll','pop-roll-4g3mO',NULL,70.00,NULL,NULL,NULL,1,0,9,'2026-06-26 06:29:39','2026-06-26 06:29:39'),(10,1,'Sandwich','sandwich-zBfO8',NULL,70.00,NULL,NULL,NULL,1,0,10,'2026-06-26 06:29:39','2026-06-26 06:29:39'),(11,1,'Cream Roll','cream-roll-uEjmH',NULL,50.00,NULL,NULL,NULL,1,0,11,'2026-06-26 06:29:39','2026-06-26 06:29:39'),(12,1,'Pastry Cake','pastry-cake-BXIaI',NULL,150.00,NULL,NULL,NULL,1,0,12,'2026-06-26 06:29:39','2026-06-26 06:29:39'),(13,1,'Mini Pastry Cake','mini-pastry-cake-N8MCZ',NULL,70.00,NULL,NULL,NULL,1,0,13,'2026-06-26 06:29:39','2026-06-26 06:29:39'),(14,1,'Fruit Cake','fruit-cake-cdRwu',NULL,180.00,NULL,NULL,NULL,1,0,14,'2026-06-26 06:29:39','2026-06-26 06:29:39'),(15,1,'Dry Cake','dry-cake-OHPxL',NULL,130.00,NULL,NULL,NULL,1,0,15,'2026-06-26 06:29:39','2026-06-26 06:29:39'),(16,2,'Honeycomb','honeycomb-zcxLp',NULL,20.00,NULL,NULL,NULL,1,0,16,'2026-06-26 06:29:39','2026-06-26 06:29:39'),(17,2,'Butterbun','butterbun-87oq2',NULL,20.00,NULL,NULL,NULL,1,0,17,'2026-06-26 06:29:39','2026-06-26 06:29:39'),(18,2,'Bread 300g','bread-300g-fMSeE',NULL,35.00,NULL,NULL,NULL,1,0,18,'2026-06-26 06:29:39','2026-06-26 06:29:39'),(19,2,'Bread 420g','bread-420g-7l0Xn',NULL,50.00,NULL,NULL,NULL,1,0,19,'2026-06-26 06:29:39','2026-06-26 06:29:39'),(20,2,'Fruit Bread Small','fruit-bread-small-ZPN7C',NULL,50.00,NULL,NULL,NULL,1,0,20,'2026-06-26 06:29:39','2026-06-26 06:29:39'),(21,2,'Fruit Bread Large','fruit-bread-large-sVDtH',NULL,70.00,NULL,NULL,NULL,1,0,21,'2026-06-26 06:29:39','2026-06-26 06:29:39'),(22,3,'Pop Stick','pop-stick-UxaBs',NULL,130.00,NULL,NULL,NULL,1,0,22,'2026-06-26 06:29:39','2026-06-26 06:29:39'),(23,3,'Sugar Free Cookies','sugar-free-cookies-7JJLw',NULL,130.00,NULL,NULL,NULL,1,0,23,'2026-06-26 06:29:39','2026-06-26 06:29:39'),(24,3,'Almond Cookies','almond-cookies-E8OGu',NULL,160.00,NULL,NULL,NULL,1,0,24,'2026-06-26 06:29:39','2026-06-26 06:29:39'),(25,3,'Fruit Cookies','fruit-cookies-kt65u',NULL,150.00,NULL,NULL,NULL,1,0,25,'2026-06-26 06:29:39','2026-06-26 06:29:39'),(26,3,'Cashew Cookies','cashew-cookies-Bx0i3',NULL,150.00,NULL,NULL,NULL,1,0,26,'2026-06-26 06:29:39','2026-06-26 06:29:39'),(27,3,'Vermicelli Cookies','vermicelli-cookies-QVq2y',NULL,160.00,NULL,NULL,NULL,1,0,27,'2026-06-26 06:29:39','2026-06-26 06:29:39'),(28,3,'Nut Cookies','nut-cookies-cHeaA',NULL,150.00,NULL,NULL,NULL,1,0,28,'2026-06-26 06:29:39','2026-06-26 06:29:39'),(29,3,'Salt Cookies','salt-cookies-OnpVb',NULL,140.00,NULL,NULL,NULL,1,0,29,'2026-06-26 06:29:39','2026-06-26 06:29:39'),(30,3,'Butter Cookies','butter-cookies-EXDKN',NULL,130.00,NULL,NULL,NULL,1,0,30,'2026-06-26 06:29:39','2026-06-26 06:29:39'),(31,3,'Sponge Cookies','sponge-cookies-PBxTT',NULL,130.00,NULL,NULL,NULL,1,0,31,'2026-06-26 06:29:39','2026-06-26 06:29:39'),(32,4,'Garlic Toast','garlic-toast-cdCbn',NULL,60.00,NULL,NULL,NULL,1,0,32,'2026-06-26 06:29:39','2026-06-26 06:29:39'),(33,4,'Chili Toast','chili-toast-0hJM8',NULL,60.00,NULL,NULL,NULL,1,0,33,'2026-06-26 06:29:39','2026-06-26 06:29:39'),(34,4,'Butter Toast','butter-toast-QGmoF',NULL,50.00,NULL,NULL,NULL,1,0,34,'2026-06-26 06:29:39','2026-06-26 06:29:39'),(35,4,'Bombay Toast','bombay-toast-aET7g',NULL,40.00,NULL,NULL,NULL,1,0,35,'2026-06-26 06:29:39','2026-06-26 06:29:39'),(36,5,'Donut','donut-IERgf',NULL,60.00,NULL,NULL,NULL,1,0,36,'2026-06-26 06:29:39','2026-06-26 06:29:39'),(37,5,'Muffin','muffin-lclj6',NULL,60.00,NULL,NULL,NULL,1,0,37,'2026-06-26 06:29:39','2026-06-26 06:29:39'),(38,5,'Chocolate Ball','chocolate-ball-WBNZW',NULL,40.00,NULL,NULL,NULL,1,0,38,'2026-06-26 06:29:39','2026-06-26 06:29:39'),(39,5,'Swiss Roll','swiss-roll-LlA2E',NULL,70.00,NULL,NULL,NULL,1,0,39,'2026-06-26 06:29:39','2026-06-26 06:29:39'),(40,5,'Pudding','pudding-vZqAf',NULL,50.00,NULL,NULL,NULL,1,0,40,'2026-06-26 06:29:39','2026-06-26 06:29:39'),(41,5,'Chocolate Mousse','chocolate-mousse-aNz7t',NULL,70.00,NULL,NULL,NULL,1,0,41,'2026-06-26 06:29:39','2026-06-26 06:29:39'),(42,5,'White Mousse','white-mousse-yAH98',NULL,70.00,NULL,NULL,NULL,1,0,42,'2026-06-26 06:29:39','2026-06-26 06:29:39'),(43,5,'White Forest Pastry','white-forest-pastry-eVGZ2',NULL,70.00,NULL,NULL,NULL,1,0,43,'2026-06-26 06:29:39','2026-06-26 06:29:39'),(44,5,'Black Forest Pastry','black-forest-pastry-up2t6',NULL,70.00,NULL,NULL,NULL,1,0,44,'2026-06-26 06:29:40','2026-06-26 06:29:40'),(45,5,'Chocolate Pastry','chocolate-pastry-8f4GS',NULL,70.00,NULL,NULL,NULL,1,1,45,'2026-06-26 06:29:40','2026-06-26 06:29:40'),(46,5,'Orange Pastry','orange-pastry-jNcbx',NULL,90.00,NULL,NULL,NULL,1,0,46,'2026-06-26 06:29:40','2026-06-26 06:29:40'),(47,5,'Red Velvet Pastry','red-velvet-pastry-E8Q3Z',NULL,90.00,NULL,NULL,NULL,1,0,47,'2026-06-26 06:29:40','2026-06-26 06:29:40'),(48,6,'White Forest 1/2kg Cake','white-forest-12kg-cake-nzEVI',NULL,600.00,NULL,NULL,NULL,1,0,48,'2026-06-26 06:29:40','2026-06-26 06:29:40'),(49,6,'Black Forest 1/2kg Cake','black-forest-12kg-cake-mncRo',NULL,600.00,NULL,NULL,NULL,1,1,49,'2026-06-26 06:29:40','2026-06-26 06:29:40'),(50,6,'Chocolate 1/2kg Cake','chocolate-12kg-cake-vSCPA',NULL,600.00,NULL,NULL,NULL,1,0,50,'2026-06-26 06:29:40','2026-06-26 06:29:40'),(51,6,'1/2kg Vanilla Cake','12kg-vanilla-cake-iLRr9',NULL,600.00,NULL,NULL,NULL,1,0,51,'2026-06-26 06:29:40','2026-06-26 06:29:40'),(52,6,'Orange 1/2kg Cake','orange-12kg-cake-ryPxO',NULL,600.00,NULL,NULL,NULL,1,0,52,'2026-06-26 06:29:40','2026-06-26 06:29:40'),(53,6,'Strawberry 1/2kg Cake','strawberry-12kg-cake-PmlpD',NULL,600.00,NULL,NULL,NULL,1,0,53,'2026-06-26 06:29:40','2026-06-26 06:29:40'),(54,6,'Lemon 1/2kg Cake','lemon-12kg-cake-UG4rW',NULL,600.00,NULL,NULL,NULL,1,0,54,'2026-06-26 06:29:40','2026-06-26 06:29:40'),(55,6,'Blueberry Cake 1/2kg','blueberry-cake-12kg-n7pHo',NULL,600.00,NULL,NULL,NULL,1,0,55,'2026-06-26 06:29:40','2026-06-26 06:29:40'),(56,6,'Red Velvet Cake 1/2kg','red-velvet-cake-12kg-F5Bvd',NULL,700.00,NULL,NULL,NULL,1,0,56,'2026-06-26 06:29:40','2026-06-26 06:29:40'),(57,6,'Vanilla Mini Cake 300g','vanilla-mini-cake-300g-arZHk',NULL,400.00,NULL,NULL,NULL,1,0,57,'2026-06-26 06:29:40','2026-06-26 06:29:40'),(58,7,'1/2kg Cake','12kg-cake-JC83E',NULL,600.00,NULL,NULL,NULL,1,0,58,'2026-06-26 06:29:40','2026-06-26 06:29:40'),(59,7,'1kg Cake','1kg-cake-k8R8o',NULL,1200.00,NULL,NULL,NULL,1,1,59,'2026-06-26 06:29:40','2026-06-26 06:29:40'),(60,7,'1kg Red Velvet Cake','1kg-red-velvet-cake-Y2BqQ',NULL,1400.00,NULL,NULL,NULL,1,0,60,'2026-06-26 06:29:40','2026-06-26 06:29:40'),(61,7,'1.5kg Cake','15kg-cake-HIH0D',NULL,1800.00,NULL,NULL,NULL,1,0,61,'2026-06-26 06:29:40','2026-06-26 06:29:40'),(62,6,'1.5kg Red Velvet Cake','15kg-red-velvet-cake-GDTpv',NULL,2100.00,NULL,NULL,NULL,1,0,62,'2026-06-26 06:29:40','2026-06-26 06:29:40'),(63,7,'2kg Cake','2kg-cake-IZgyL',NULL,2400.00,NULL,NULL,NULL,1,0,63,'2026-06-26 06:29:40','2026-06-26 06:29:40'),(64,8,'Dry Rasgolla','dry-rasgolla-L0Qg5',NULL,280.00,NULL,NULL,NULL,1,1,64,'2026-06-26 06:29:40','2026-06-26 06:29:40'),(65,8,'Kalojam','kalojam-pZHry',NULL,280.00,NULL,NULL,NULL,1,1,65,'2026-06-26 06:29:40','2026-06-26 06:29:40'),(66,8,'Red Chamcham','red-chamcham-Jums9',NULL,280.00,NULL,NULL,NULL,1,0,66,'2026-06-26 06:29:40','2026-06-26 06:29:40'),(67,8,'Mini Chamcham','mini-chamcham-YQvzB',NULL,350.00,NULL,NULL,NULL,1,0,67,'2026-06-26 06:29:40','2026-06-26 06:29:40'),(68,8,'Baby Sweets','baby-sweets-v8C1U',NULL,300.00,NULL,NULL,NULL,1,0,68,'2026-06-26 06:29:40','2026-06-26 06:29:40'),(69,8,'Malaikari','malaikari-rxnMC',NULL,350.00,NULL,NULL,NULL,1,0,69,'2026-06-26 06:29:40','2026-06-26 06:29:40'),(70,8,'Shahi Toast','shahi-toast-25t52',NULL,350.00,NULL,NULL,NULL,1,0,70,'2026-06-26 06:29:40','2026-06-26 06:29:40'),(71,8,'Langcha','langcha-gFEse',NULL,380.00,NULL,NULL,NULL,1,0,71,'2026-06-26 06:29:40','2026-06-26 06:29:40'),(72,8,'Creamjam','creamjam-iY91p',NULL,360.00,NULL,NULL,NULL,1,0,72,'2026-06-26 06:29:40','2026-06-26 06:29:40'),(73,8,'Golap Jam','golap-jam-PObyu',NULL,350.00,NULL,NULL,NULL,1,0,73,'2026-06-26 06:29:40','2026-06-26 06:29:40'),(74,8,'Pora Sandesh','pora-sandesh-qbdNS',NULL,420.00,NULL,NULL,NULL,1,0,74,'2026-06-26 06:29:40','2026-06-26 06:29:40'),(75,8,'Maya Sandesh','maya-sandesh-87VRW',NULL,400.00,NULL,NULL,NULL,1,0,75,'2026-06-26 06:29:40','2026-06-26 06:29:40'),(76,8,'Hafsi Sandesh','hafsi-sandesh-8Byox',NULL,500.00,NULL,NULL,NULL,1,0,76,'2026-06-26 06:29:40','2026-06-26 06:29:40'),(77,8,'White Sandesh','white-sandesh-37K7p',NULL,400.00,NULL,NULL,NULL,1,0,77,'2026-06-26 06:29:40','2026-06-26 06:29:40'),(78,8,'Sandwich','sandwich-EOQ21',NULL,350.00,NULL,NULL,NULL,1,0,78,'2026-06-26 06:29:40','2026-06-26 06:29:40'),(79,8,'S.P Rasgolla','sp-rasgolla-QUQNr',NULL,320.00,NULL,NULL,NULL,1,0,79,'2026-06-26 06:29:40','2026-06-26 06:29:40'),(80,8,'Kacha Chana','kacha-chana-Ei0pt',NULL,450.00,NULL,NULL,NULL,1,0,80,'2026-06-26 06:29:40','2026-06-26 06:29:40'),(81,8,'Mihi Dana Laddu','mihi-dana-laddu-KXyDr',NULL,280.00,NULL,NULL,NULL,1,0,81,'2026-06-26 06:29:40','2026-06-26 06:29:40'),(82,8,'Orange Laddu','orange-laddu-as9MF',NULL,280.00,NULL,NULL,NULL,1,0,82,'2026-06-26 06:29:40','2026-06-26 06:29:40'),(83,8,'Strawberry Laddu','strawberry-laddu-aZRoD',NULL,280.00,NULL,NULL,NULL,1,0,83,'2026-06-26 06:29:40','2026-06-26 06:29:40'),(84,8,'Sar Doi 1kg','sar-doi-1kg-mZnpR',NULL,220.00,NULL,NULL,NULL,1,0,84,'2026-06-26 06:29:40','2026-06-26 06:29:40'),(85,8,'Tok-Mishti Doi 1kg','tok-mishti-doi-1kg-S3tuO',NULL,200.00,NULL,NULL,NULL,1,0,85,'2026-06-26 06:29:40','2026-06-26 06:29:40'),(86,8,'Tok-Mishti Doi 500g','tok-mishti-doi-500g-IP5Pp',NULL,110.00,NULL,NULL,NULL,1,0,86,'2026-06-26 06:29:40','2026-06-26 06:29:40'),(87,8,'Kaf Doi','kaf-doi-acbx0',NULL,25.00,NULL,NULL,NULL,1,0,87,'2026-06-26 06:29:40','2026-06-26 06:29:40'),(88,8,'Toffee Chocolate','toffee-chocolate-ZXtYn',NULL,10.00,NULL,NULL,NULL,1,0,88,'2026-06-26 06:29:40','2026-06-26 06:29:40'),(89,8,'Guji Guava 100g','guji-guava-100g-IBZMA',NULL,60.00,NULL,NULL,NULL,1,0,89,'2026-06-26 06:29:40','2026-06-26 06:29:40'),(90,8,'Potato Chanachur 300g','potato-chanachur-300g-K79ID',NULL,120.00,NULL,NULL,NULL,1,0,90,'2026-06-26 06:29:40','2026-06-26 06:29:40'),(91,8,'Chira','chira-9U835',NULL,120.00,NULL,NULL,NULL,1,0,91,'2026-06-26 06:29:40','2026-06-26 06:29:40'),(92,8,'Rasmalai 1kg','rasmalai-1kg-xQDeP',NULL,320.00,NULL,NULL,NULL,1,1,92,'2026-06-26 06:29:40','2026-06-26 06:29:40'),(93,8,'Rasmalai 500g','rasmalai-500g-WTFF9',NULL,160.00,NULL,NULL,NULL,1,0,93,'2026-06-26 06:29:40','2026-06-26 06:29:40'),(94,8,'Dudh Sandesh','dudh-sandesh-abMyY',NULL,500.00,NULL,NULL,NULL,1,0,94,'2026-06-26 06:29:40','2026-06-26 06:29:40'),(95,8,'Dodiya Sandesh','dodiya-sandesh-MTbSk',NULL,550.00,NULL,NULL,NULL,1,0,95,'2026-06-26 06:29:40','2026-06-26 06:29:40'),(96,8,'Sar Malai','sar-malai-iGuN3',NULL,40.00,NULL,NULL,NULL,1,0,96,'2026-06-26 06:29:40','2026-06-26 06:29:40'),(97,8,'Malai Pastry','malai-pastry-j5vgp',NULL,550.00,NULL,NULL,NULL,1,0,97,'2026-06-26 06:29:40','2026-06-26 06:29:40'),(98,8,'Malai Chop','malai-chop-NhDd8',NULL,500.00,NULL,NULL,NULL,1,0,98,'2026-06-26 06:29:40','2026-06-26 06:29:40'),(99,8,'Cutlet','cutlet-S8MJp',NULL,450.00,NULL,NULL,NULL,1,0,99,'2026-06-26 06:29:40','2026-06-26 06:29:40'),(100,8,'Muskat Halwa','muskat-halwa-35dAE',NULL,400.00,NULL,NULL,NULL,1,0,100,'2026-06-26 06:29:40','2026-06-26 06:29:40'),(101,8,'Carrot Barfi','carrot-barfi-wI3gS',NULL,460.00,NULL,NULL,NULL,1,0,101,'2026-06-26 06:29:40','2026-06-26 06:29:40'),(102,8,'Balushai','balushai-ncyqU',NULL,300.00,NULL,NULL,NULL,1,0,102,'2026-06-26 06:29:40','2026-06-26 06:29:40');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('1TFUOhP2onDkBcePoJqxJf0E8GSKY2IjqVNuRxBx',NULL,'127.0.0.1','curl/8.5.0','eyJfdG9rZW4iOiI5N0tWN3BDMldEZXdJSDZzazB3MmNDWkgxbEVWMzZyUmtIOXVWVjdrIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDgwXC9wcm9kdWN0cyIsInJvdXRlIjoicHJvZHVjdHMuaW5kZXgifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1782481612),('5ReqUqoDZ0BiIOAWZri56oeo0LCB1Yy0t98lYIHd',NULL,'127.0.0.1','curl/8.5.0','eyJfdG9rZW4iOiJBcHNNTGxEYzJSdU1ORHNCaTU1QUt6R25ON2J4alN2TGJUSnNkQkdFIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDgwXC9sb2dpbiIsInJvdXRlIjoibG9naW4ifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1782482425),('5xP30TFduidfEPoCxXvDjLQtwJ70Z1GEEqrMlepP',NULL,'127.0.0.1','curl/8.5.0','eyJfdG9rZW4iOiJqS0dlRkplSmxPMkU0Q2dFV29pZmJJTUJLRnRseDFoOVozYlB5ZkRnIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDgwXC9jdXN0b20tY2FrZSIsInJvdXRlIjoiY3VzdG9tLWNha2UuaW5kZXgifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1782482425),('BSGCQcupfTnbVbxlCI9w642OIZL9fJlykcPYrP48',NULL,'127.0.0.1','curl/8.5.0','eyJfdG9rZW4iOiJKTzljS292bnAxN2VTSUxBNXNyN0t4TExacWdYRG9CN0ZKRnlvWmluIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDgwXC9sb2dpbiIsInJvdXRlIjoibG9naW4ifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1782483041),('E1GPWe8CGpriDxXoqncOoGiBOK6jOqAYUR7boP47',NULL,'127.0.0.1','curl/8.5.0','eyJfdG9rZW4iOiIyb25VdWwzRUo4WmhhNWxaWEFaeUg2b0RpZDE2dmJuQXBNS0JWOXcwIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDgwIiwicm91dGUiOiJob21lIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1782481609),('EjUhprL0217k0hIsikusDXdy8oXdZ4YfVAcBzati',NULL,'127.0.0.1','curl/8.5.0','eyJfdG9rZW4iOiJ2NFgzUjV1U2E1c1JCZXRxVEkzcWRpa09BNzNJVVVFemFuSWtxMktsIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDgwXC9jaGVja291dCIsInJvdXRlIjoiY2hlY2tvdXQuaW5kZXgifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1782481962),('iiZQxzwQURsoZtE2zFqyiJAjTfopofRbywQVMhZ4',1,'127.0.0.1','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJHd2VrQkNDZ1dJMVhxQ1JqTldUQTRFS2JNR3pDUmZoZUVxc3k2dWgyIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJob21lIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjF9',1782486782),('klJ60rdrrYBphNmIM0ZfcXEZBJQG6XSa88QFKec3',NULL,'127.0.0.1','curl/8.5.0','eyJfdG9rZW4iOiJmUkpONTI5VUVpQXczbG1HdTlKZ1dHb2t2UHpDZExrdThQOTFqdkt4IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDgwXC9jaGVja291dCIsInJvdXRlIjoiY2hlY2tvdXQuaW5kZXgifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1782481953),('ksNH2sJA0XhrxuRGgyrt21JRQaD6AX9iLX8dUmq1',NULL,'127.0.0.1','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJVUVRDQlhtNFdHZDl4ZE1xUXQyV080SjRkZGRlaTBRVDIyMFZBczNCIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAiLCJyb3V0ZSI6ImhvbWUifX0=',1782484816),('NhCpi7qJ79TGqkp1sIoAvIwiV78F65mtWKtj9q2w',NULL,'127.0.0.1','curl/8.5.0','eyJfdG9rZW4iOiJqcEJGdnUwNkhYU1NSV1VISWdSWHNHaHpJb2loWFBSdVpXcEF1M3JPIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDgwXC9yZWdpc3RlciIsInJvdXRlIjoicmVnaXN0ZXIifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1782483042),('QWJijLgPSpCMA4gA6LeA6EFtJY5SsouXfeXiZyEr',NULL,'127.0.0.1','curl/8.5.0','eyJfdG9rZW4iOiJhMjZ6aTVXdGgwbnNXYldrSnYySDlIdGJPaWgxWTlndUFvSkNzY2dQIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDgwXC9jaGVja291dCIsInJvdXRlIjoiY2hlY2tvdXQuaW5kZXgifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1782481982),('uu8oemq3urfoumdzXv8AEhvKD8o0um9TxaUmr1p5',NULL,'127.0.0.1','curl/8.5.0','eyJfdG9rZW4iOiJVUFBNVEw4bW16UEJ5dHB0UmJnZk9DZlpKc2VrVmtocEJieXE2aFBEIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDgwXC9jaGVja291dCIsInJvdXRlIjoiY2hlY2tvdXQuaW5kZXgifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1782481957),('vKuimZJVU11YJTlansjYzySkyjATKHiwWLDpZ8lN',NULL,'127.0.0.1','curl/8.5.0','eyJfdG9rZW4iOiJvZHVzZDc1S1JJeXJTS0E2OFNWWE5XQVh6dVhvdmEzRGRMa2tBVHdQIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDgwXC9jaGVja291dCIsInJvdXRlIjoiY2hlY2tvdXQuaW5kZXgifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1782481966),('vu82rkzsqOEWZVqBWP65Rbg3cHioLZVbqXlNFBWW',NULL,'127.0.0.1','curl/8.5.0','eyJfdG9rZW4iOiJtTE9rRlBsOGlWY0gwaURWM0ZqUjlYSUUwUWlwTUJMMWk0WVNxZjRSIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDgwXC9yZWdpc3RlciIsInJvdXRlIjoicmVnaXN0ZXIifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1782482425),('YIJJSdE5i4lpHgcL03qj9RchO1k89EDsV7IiA7PC',NULL,'127.0.0.1','curl/8.5.0','eyJfdG9rZW4iOiJSUWljYVUxeEVsVmh2dG9EQXFqZmZKV0dTSTNOdno2WXZGQ3ljTkNpIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDgwXC9jaGVja291dCIsInJvdXRlIjoiY2hlY2tvdXQuaW5kZXgifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1782481974),('zpP1QZK5pQziWC4DH2qq9iNT2YrQMjv8sN6wCyTb',NULL,'127.0.0.1','curl/8.5.0','eyJfdG9rZW4iOiJGZ1lzQmt5TW9yalh0TU9JVGlyUTk2cGZUQThFd0RTQmpvQzVuakc2IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDgwXC9jaGVja291dCIsInJvdXRlIjoiY2hlY2tvdXQuaW5kZXgifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1782483115);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `group` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (1,'site_name','Lake View Sweets & Bakery','general','2026-06-26 06:29:39','2026-06-26 06:29:39'),(2,'site_tagline','The Finest Sweets & Bakery','general','2026-06-26 06:29:39','2026-06-26 06:29:39'),(3,'site_description','Lake View Sweets & Bakery - The finest sweets and bakery in Satkhira. We are committed to delivering the highest quality sweets, cakes, biscuits and bakery products.','general','2026-06-26 06:29:39','2026-06-26 06:29:39'),(4,'site_logo','','general','2026-06-26 06:29:39','2026-06-26 06:29:39'),(5,'hero_title','Lake View Sweets & Bakery','homepage','2026-06-26 06:29:39','2026-06-26 06:29:39'),(6,'hero_subtitle','The finest sweets and bakery in Satkhira - delivering the best quality products to your doorstep','homepage','2026-06-26 06:29:39','2026-06-26 06:29:39'),(7,'hero_image','','homepage','2026-06-26 06:29:39','2026-06-26 06:29:39'),(8,'about_text','Lake View Sweets & Bakery was established in 2023. We produce the highest quality sweets, cakes, biscuits and bakery products in Satkhira district. We have 7 branches across Satkhira Sadar and surrounding areas.','general','2026-06-26 06:29:39','2026-06-26 06:29:39'),(9,'facebook_url','https://facebook.com/lakeviewsweets','social','2026-06-26 06:29:39','2026-06-26 06:29:39'),(10,'instagram_url','','social','2026-06-26 06:29:39','2026-06-26 06:29:39'),(11,'whatsapp_number','+8801722554400','social','2026-06-26 06:29:39','2026-06-26 06:29:39'),(12,'opening_hours','Open Daily 9:00 AM - 11:00 PM','general','2026-06-26 06:29:39','2026-06-26 08:32:06'),(13,'min_order_amount','0','delivery','2026-06-26 06:29:39','2026-06-26 06:29:39'),(14,'custom_cake_info','Order your custom cake. Upload your design and we will create the cake exactly as you want it.','general','2026-06-26 06:29:39','2026-06-26 06:29:39'),(15,'min_order_pickup','0','delivery','2026-06-26 08:39:00','2026-06-26 08:39:00'),(16,'min_order_sadar','500','delivery','2026-06-26 08:39:00','2026-06-26 08:39:00'),(17,'min_order_outside','1000','delivery','2026-06-26 08:39:00','2026-06-26 08:39:00'),(18,'hero_images','[]','homepage','2026-06-26 08:49:24','2026-06-26 08:49:24'),(19,'featured_section_title','Our Popular Products','homepage','2026-06-26 08:49:24','2026-06-26 08:49:24'),(20,'featured_section_subtitle','Try our most loved sweets and bakery items','homepage','2026-06-26 08:49:24','2026-06-26 08:49:24'),(21,'promo_banner_text','Free delivery on orders over ৳1000 within Satkhira Sadar','homepage','2026-06-26 08:49:24','2026-06-26 08:49:24'),(22,'promo_banner_active','1','homepage','2026-06-26 08:49:24','2026-06-26 08:49:24'),(23,'merchant_number','01722554400','delivery','2026-06-26 08:58:28','2026-06-26 08:58:28'),(24,'merchant_name','Lake View Sweets & Bakery','delivery','2026-06-26 08:58:28','2026-06-26 08:58:28'),(25,'payment_instructions','Send money to our merchant number and enter the transaction ID below.','delivery','2026-06-26 08:58:28','2026-06-26 08:58:28');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'customer',
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Super Admin','admin@lakeview.com','2026-06-26 06:29:39','$2y$12$DIe1bulN3WmjQuNHIBpMLuUNVC2zXw2Vi3wvfoLct8NtHGmQEngAC','admin','+8801722554400',NULL,'2026-06-26 06:29:39','2026-06-26 06:29:39'),(2,'Mir Javed','customer_lYS7JAyt@lakeview.local',NULL,'$2y$12$q3svGdOt3KWnsdKOVK73xeq1oA1w0CkW7pj..h2lzoDxJzPCoSOmK','customer','01772788676',NULL,'2026-06-26 08:13:30','2026-06-26 08:13:30');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-26 21:14:02
