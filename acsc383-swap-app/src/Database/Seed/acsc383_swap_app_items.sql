-- MySQL dump 10.13  Distrib 8.0.36, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: acsc383_swap_app
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

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
-- Table structure for table `items`
--

DROP TABLE IF EXISTS `items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `items` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `category` varchar(80) NOT NULL,
  `condition` varchar(50) NOT NULL,
  `weight_kg` decimal(10,2) NOT NULL DEFAULT 0.00,
  `transaction_type` enum('SWAP','SALE','DONATION') NOT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `status` enum('AVAILABLE','RESERVED','COMPLETED') NOT NULL DEFAULT 'AVAILABLE',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`item_id`),
  KEY `idx_items_owner` (`owner_id`),
  KEY `idx_items_status` (`status`),
  KEY `idx_items_discovery` (`status`,`transaction_type`,`category`),
  CONSTRAINT `fk_items_owner` FOREIGN KEY (`owner_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `items`
--

LOCK TABLES `items` WRITE;
/*!40000 ALTER TABLE `items` DISABLE KEYS */;
INSERT INTO `items` VALUES (1,1,'Calculus II Textbook (Anton 11e)','Some highlights, otherwise clean. Great for Calculus II revision.','Books','Good',1.20,'SALE',18.00,'AVAILABLE','2026-01-10 10:15:00'),(2,1,'Discrete Mathematics Notes Binder','Printed notes + exercises, organized by topic.','Books','Like New',0.80,'DONATION',NULL,'AVAILABLE','2026-01-10 11:10:00'),(3,1,'Wireless Mouse (Logitech)','Smooth tracking, includes USB receiver.','Electronics','Good',0.20,'SALE',8.00,'COMPLETED','2026-01-10 12:05:00'),(4,1,'Desk Lamp (LED)','Bright LED lamp with adjustable arm.','Home','Like New',1.50,'SALE',12.00,'AVAILABLE','2026-01-10 13:30:00'),(5,1,'Hoodie (Medium, Black)','Warm hoodie, no tears, washed.','Clothing','Good',0.70,'SWAP',NULL,'RESERVED','2026-01-10 14:20:00'),(6,1,'USB-C Charger 20W','Works well, compatible with most phones.','Electronics','Like New',0.15,'SALE',6.00,'AVAILABLE','2026-01-10 15:45:00'),(7,1,'Reusable Water Bottle 750ml','Stainless steel bottle, keeps drinks cold.','Accessories','Good',0.40,'DONATION',NULL,'COMPLETED','2026-01-10 16:25:00'),(8,1,'Bluetooth Speaker (Small)','Portable speaker, decent bass.','Electronics','Good',0.60,'SALE',14.00,'AVAILABLE','2026-01-10 17:40:00'),(9,1,'Winter Scarf (Grey)','Soft scarf, barely used.','Clothing','Like New',0.25,'DONATION',NULL,'AVAILABLE','2026-01-10 18:10:00'),(10,1,'Laptop Stand (Aluminum)','Raises laptop, improves posture.','Accessories','Like New',0.90,'SALE',10.00,'AVAILABLE','2026-01-10 19:05:00'),(11,2,'Java Programming Book','Beginner-friendly Java book, good examples.','Books','Good',1.10,'SALE',12.00,'COMPLETED','2026-01-11 09:30:00'),(12,2,'T-Shirt (Large, White)','Plain white tee, minimal wear.','Clothing','Like New',0.25,'DONATION',NULL,'AVAILABLE','2026-01-11 10:05:00'),(13,2,'Backpack (Laptop 15\")','Padded laptop sleeve, comfortable straps.','Accessories','Good',0.95,'SALE',16.00,'AVAILABLE','2026-01-11 11:20:00'),(14,2,'Headphones (Wired)','Over-ear headphones, good sound.','Electronics','Good',0.45,'SALE',9.00,'AVAILABLE','2026-01-11 12:00:00'),(15,2,'Graphing Calculator','Works perfectly, includes cover.','Electronics','Good',0.35,'SALE',20.00,'AVAILABLE','2026-01-11 12:45:00'),(16,2,'Small Fan (Desktop)','USB-powered fan for desk.','Home','Like New',0.60,'DONATION',NULL,'AVAILABLE','2026-01-11 13:15:00'),(17,2,'Notebook Set (5 pack)','Unused notebooks, lined pages.','Books','New',0.90,'DONATION',NULL,'COMPLETED','2026-01-11 14:10:00'),(18,2,'Gym Gloves','For weightlifting, good grip.','Accessories','Good',0.20,'SWAP',NULL,'AVAILABLE','2026-01-11 15:00:00'),(19,2,'Jacket (Medium, Navy)','Light jacket, zipper ok.','Clothing','Good',0.85,'SALE',15.00,'AVAILABLE','2026-01-11 16:10:00'),(20,2,'Desk Organizer','Holds pens/cables, tidy desk.','Home','Like New',0.50,'SALE',7.00,'RESERVED','2026-01-11 17:05:00');
/*!40000 ALTER TABLE `items` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-01-13  1:04:23
