-- MySQL dump 10.13  Distrib 8.0.41, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: lucky_spin
-- ------------------------------------------------------
-- Server version	8.0.42

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
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
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
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
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
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
  `attempts` tinyint unsigned NOT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2024_01_01_000000_add_is_admin_to_users_table',1),(5,'2024_01_01_000001_create_prizes_table',1),(6,'2024_01_01_000002_create_spin_codes_table',1),(7,'2024_01_01_000003_create_spin_sessions_table',1),(8,'2024_01_01_000004_create_spin_results_table',1),(9,'2025_12_01_104146_create_personal_access_tokens_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
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
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
INSERT INTO `personal_access_tokens` VALUES (1,'App\\Models\\User',2,'admin-token','b67e8d6123cdf45edd5f445b1011256c8d5daddf9d6410619650fb0c7422c18d','[\"*\"]','2025-12-01 04:11:15',NULL,'2025-12-01 03:49:09','2025-12-01 04:11:15');
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `prizes`
--

DROP TABLE IF EXISTS `prizes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `prizes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` int NOT NULL DEFAULT '0',
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#ffffff',
  `probability` decimal(5,4) NOT NULL DEFAULT '0.0500',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `stock` int DEFAULT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `prizes`
--

LOCK TABLES `prizes` WRITE;
/*!40000 ALTER TABLE `prizes` DISABLE KEYS */;
INSERT INTO `prizes` VALUES (1,'Cao băng bạo',120,'images/cao-bang-bao.jpg','#FF6B6B',0.1200,1,NULL,0,'2025-12-01 03:48:07','2025-12-01 03:48:07'),(2,'Cao bí hựu',200,'images/cao-bi-huu.jpg','#4ECDC4',0.0900,1,NULL,1,'2025-12-01 03:48:07','2025-12-01 03:48:07'),(3,'Cao di hồn',100,'images/cao-di-hon.jpg','#45B7D1',0.1200,1,NULL,2,'2025-12-01 03:48:07','2025-12-01 03:48:07'),(4,'Cao huyết bạo',750,'images/cao-huyet-bao.jpg','#F7DC6F',0.0200,1,NULL,3,'2025-12-01 03:48:07','2025-12-01 03:48:07'),(5,'Cao linh đông',500,'images/cao-linh-dong.jpg','#BB8FCE',0.0300,1,NULL,4,'2025-12-01 03:48:07','2025-12-01 03:48:07'),(6,'Cao mãnh kích',750,'images/cao-manh-kich.jpg','#85C1E2',0.0200,1,NULL,5,'2025-12-01 03:48:07','2025-12-01 03:48:07'),(7,'Cao ngưng thần',450,'images/cao-ngung-than.jpg','#F8B739',0.0400,1,NULL,6,'2025-12-01 03:48:07','2025-12-01 03:48:07'),(8,'Cao cường thân',450,'images/cao-cuong-than.jpg','#52C469',0.0400,1,NULL,7,'2025-12-01 03:48:07','2025-12-01 03:48:07'),(9,'Cao phản chấn',300,'images/cao-phan-chan.jpg','#FF8C94',0.0600,1,NULL,8,'2025-12-01 03:48:07','2025-12-01 03:48:07'),(10,'Cao phản kích',300,'images/cao-phan-kich.jpg','#A8E6CF',0.0600,1,NULL,9,'2025-12-01 03:48:07','2025-12-01 03:48:07'),(11,'Cao phụ thân',150,'images/cao-phu-than.jpg','#FFD3B6',0.1000,1,NULL,10,'2025-12-01 03:48:07','2025-12-01 03:48:07'),(12,'Cao tá lực',150,'images/cao-ta-luc.jpg','#FFAAA5',0.1000,1,NULL,11,'2025-12-01 03:48:07','2025-12-01 03:48:07'),(13,'Cao thế sát',600,'images/cao-the-sat.jpg','#FF8B94',0.0250,1,NULL,12,'2025-12-01 03:48:07','2025-12-01 03:48:07'),(14,'Cao thị huyết',100,'images/cao-thi-huyet.jpg','#A8D8EA',0.1200,1,NULL,13,'2025-12-01 03:48:07','2025-12-01 03:48:07'),(15,'Cao thuấn ảnh',400,'images/cao-thuan-anh.jpg','#AA96DA',0.0500,1,NULL,14,'2025-12-01 03:48:07','2025-12-01 03:48:07'),(16,'Cao nội lực',600,'images/cao-noi-luc.jpg','#FCBAD3',0.0250,1,NULL,15,'2025-12-01 03:48:07','2025-12-01 03:48:07'),(17,'Cao cộng sinh',300,'images/cao-cong-sinh.jpg','#FFFFD2',0.0600,1,NULL,16,'2025-12-01 03:48:07','2025-12-01 03:48:07'),(18,'Cao nhục tường',100,'images/cao-nhuc-tuong.jpg','#A0CED9',0.1200,1,NULL,17,'2025-12-01 03:48:07','2025-12-01 03:48:07'),(19,'Cao Huyết tế',4500,'images/cao-huyet-te.jpg','#FFC09F',0.0010,1,NULL,18,'2025-12-01 03:48:07','2025-12-01 03:48:07');
/*!40000 ALTER TABLE `prizes` ENABLE KEYS */;
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
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `spin_codes`
--

DROP TABLE IF EXISTS `spin_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `spin_codes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `spins_amount` int NOT NULL DEFAULT '1',
  `max_uses` int DEFAULT NULL,
  `used_count` int NOT NULL DEFAULT '0',
  `note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `spin_codes_code_unique` (`code`),
  KEY `spin_codes_code_is_active_index` (`code`,`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `spin_codes`
--

LOCK TABLES `spin_codes` WRITE;
/*!40000 ALTER TABLE `spin_codes` DISABLE KEYS */;
INSERT INTO `spin_codes` VALUES (1,'SOTPI7YJXN',5,1,1,NULL,NULL,1,'2025-12-01 04:02:54','2025-12-01 04:06:19');
/*!40000 ALTER TABLE `spin_codes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `spin_results`
--

DROP TABLE IF EXISTS `spin_results`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `spin_results` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `session_id` bigint unsigned NOT NULL,
  `prize_id` bigint unsigned NOT NULL,
  `spin_token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_angle` decimal(8,2) NOT NULL,
  `status` enum('pending','claimed','expired') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `claimed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `spin_results_spin_token_unique` (`spin_token`),
  KEY `spin_results_prize_id_foreign` (`prize_id`),
  KEY `spin_results_spin_token_index` (`spin_token`),
  KEY `spin_results_session_id_status_index` (`session_id`,`status`),
  CONSTRAINT `spin_results_prize_id_foreign` FOREIGN KEY (`prize_id`) REFERENCES `prizes` (`id`),
  CONSTRAINT `spin_results_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `spin_sessions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `spin_results`
--

LOCK TABLES `spin_results` WRITE;
/*!40000 ALTER TABLE `spin_results` DISABLE KEYS */;
INSERT INTO `spin_results` VALUES (1,1,1,'EzXGsDZ46rO7mPf58SEJmJcf69lpF93rLAwyUpFfGQVIAWuzpiVSIvcuJOkAvjcr',2534.02,'claimed','2025-12-01 04:10:59','2025-12-01 04:10:50','2025-12-01 04:10:59'),(2,1,9,'IWD3TryrWeQeljqym06yReNvVH3TgauJcxp9SpZsXExNPO9noUsJ2IRHvS5dzopg',2677.45,'claimed','2025-12-01 04:15:58','2025-12-01 04:15:50','2025-12-01 04:15:58'),(3,1,16,'2Mpq1SVDQoVBmP1OM2BZqXDz98xKp0GSHmdQivTTf3N1L2nGtseH60bcV7oab7aQ',2818.04,'claimed','2025-12-01 04:16:17','2025-12-01 04:16:09','2025-12-01 04:16:17'),(4,1,17,'feYsbGuc7ehybaHnFjvxKc3kPdGyzw36a9Un5c7PeXQBsCbsPtlNfA1DEBCJi36g',2565.66,'claimed','2025-12-01 04:21:10','2025-12-01 04:21:01','2025-12-01 04:21:10'),(5,1,12,'wEo4QP8l2u8W8Vx8ZyxkdKRr77hC73RrVNzIFiZIYE5yCVkXgAUGbPd8RrgpCiMh',2665.71,'claimed','2025-12-01 04:21:31','2025-12-01 04:21:22','2025-12-01 04:21:31');
/*!40000 ALTER TABLE `spin_results` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `spin_sessions`
--

DROP TABLE IF EXISTS `spin_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `spin_sessions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `session_token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code_id` bigint unsigned DEFAULT NULL,
  `spin_balance` int NOT NULL DEFAULT '0',
  `total_spins` int NOT NULL DEFAULT '0',
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expires_at` timestamp NOT NULL,
  `last_spin_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `spin_sessions_session_token_unique` (`session_token`),
  KEY `spin_sessions_code_id_foreign` (`code_id`),
  KEY `spin_sessions_session_token_index` (`session_token`),
  KEY `spin_sessions_expires_at_index` (`expires_at`),
  CONSTRAINT `spin_sessions_code_id_foreign` FOREIGN KEY (`code_id`) REFERENCES `spin_codes` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `spin_sessions`
--

LOCK TABLES `spin_sessions` WRITE;
/*!40000 ALTER TABLE `spin_sessions` DISABLE KEYS */;
INSERT INTO `spin_sessions` VALUES (1,'HIIT9Vv1PpVoyJ8GOHl4RMc575MfWMYRgktvVlb7RtdrIprhgp1HmqJGYBZsIpQl',1,0,5,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0','2025-12-08 04:06:19','2025-12-01 04:21:22','2025-12-01 04:06:19','2025-12-01 04:21:22');
/*!40000 ALTER TABLE `spin_sessions` ENABLE KEYS */;
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
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Test User','test@example.com','2025-12-01 03:47:47','$2y$12$EFwdkPQk8lYUpMQSiA6pLuMQtMRiHV4AB.eNtQwce4Gj1UQMSz5BW','BcY6Xfi8tA',0,'2025-12-01 03:47:47','2025-12-01 03:47:47'),(2,'Admin','admin@example.com',NULL,'$2y$12$BqwJB2uciwO1GXdi/68PrOURwoJ8VdZkRoiuIBeZxsJtP/keevnn2',NULL,1,'2025-12-01 03:48:28','2025-12-01 03:48:28');
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

-- Dump completed on 2025-12-01 18:22:19
