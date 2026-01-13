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
-- Table structure for table `item_images`
--

DROP TABLE IF EXISTS `item_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `item_images` (
  `image_id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `uploaded_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`image_id`),
  KEY `idx_item_images_item` (`item_id`),
  CONSTRAINT `fk_item_images_item` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `item_images`
--

LOCK TABLES `item_images` WRITE;
/*!40000 ALTER TABLE `item_images` DISABLE KEYS */;
INSERT INTO `item_images` VALUES (1,1,'uploads/items/1/calculus_textbook_1.jpg','2026-01-10 10:16:00'),(2,1,'uploads/items/1/calculus_textbook_2.jpg','2026-01-10 10:16:10'),(3,2,'uploads/items/2/discrete_binder_1.jpg','2026-01-10 11:11:00'),(4,3,'uploads/items/3/mouse_1.jpg','2026-01-10 12:06:00'),(5,4,'uploads/items/4/desk_lamp_1.jpg','2026-01-10 13:31:00'),(6,5,'uploads/items/5/hoodie_1.jpg','2026-01-10 14:21:00'),(7,6,'uploads/items/6/usb_c_charger_1.jpg','2026-01-10 15:46:00'),(8,7,'uploads/items/7/bottle_1.jpg','2026-01-10 16:26:00'),(9,8,'uploads/items/8/speaker_1.jpg','2026-01-10 17:41:00'),(10,9,'uploads/items/9/scarf_1.jpg','2026-01-10 18:11:00'),(11,10,'uploads/items/10/laptop_stand_1.jpg','2026-01-10 19:06:00'),(12,11,'uploads/items/11/java_book_1.jpg','2026-01-11 09:31:00'),(13,12,'uploads/items/12/tshirt_white_1.jpg','2026-01-11 10:06:00'),(14,13,'uploads/items/13/backpack_1.jpg','2026-01-11 11:21:00'),(15,13,'uploads/items/13/backpack_2.jpg','2026-01-11 11:21:10'),(16,14,'uploads/items/14/headphones_1.jpg','2026-01-11 12:01:00'),(17,15,'uploads/items/15/calculator_1.jpg','2026-01-11 12:46:00'),(18,16,'uploads/items/16/desk_fan_1.jpg','2026-01-11 13:16:00'),(19,17,'uploads/items/17/notebooks_1.jpg','2026-01-11 14:11:00'),(20,18,'uploads/items/18/gym_gloves_1.jpg','2026-01-11 15:01:00'),(21,19,'uploads/items/19/jacket_1.jpg','2026-01-11 16:11:00'),(22,20,'uploads/items/20/desk_organizer_1.jpg','2026-01-11 17:06:00');
/*!40000 ALTER TABLE `item_images` ENABLE KEYS */;
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
