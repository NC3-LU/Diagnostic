-- MySQL dump 10.13  Distrib 5.7.23, for Linux (x86_64)
--
-- Host: localhost    Database: diagnostic
-- ------------------------------------------------------
-- Server version	5.7.23-0ubuntu0.18.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `translation_key` varchar(255) NOT NULL,
  `uid` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'__category1','19a40862f00e73efa5ca88afccec50d8'),(2,'__category2','4fe10edc1b0bb7c10dbe3be5862574fd'),(3,'__category3','4a0e4b9ff67d5a764b121bb881526046'),(4,'__category4','ecf70c725ee4814daf71eb608aed655f'),(5,'__category5','9db9b393da67331909e76415afee2d12'),(6,'__category6','722c6c9b89ad042ee0b9a8dae34330f3'),(7,'__category7','e05c6e5d4ecf2783e49ba3595915e4a1'),(8,'__category8','f6132c50ffa2f550d4f6187fd0773178');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `questions`
--

DROP TABLE IF EXISTS `questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `questions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(10) unsigned NOT NULL,
  `translation_key` varchar(255) NOT NULL,
  `threat` tinyint(3) unsigned NOT NULL,
  `weight` tinyint(3) unsigned NOT NULL,
  `blocking` varchar(255) NOT NULL,
  `uid` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `questions`
--

LOCK TABLES `questions` WRITE;
/*!40000 ALTER TABLE `questions` DISABLE KEYS */;
<<<<<<< HEAD
INSERT INTO `questions` VALUES (1,1,'__question1',20),(2,1,'__question2',5),(3,1,'__question3',5),(4,1,'__question4',5),(5,2,'__question5',5),(6,2,'__question6',10),(7,2,'__question7',10),(8,2,'__question8',5),(9,2,'__question9',15),(10,3,'__question10',10),(11,3,'__question11',15),(12,3,'__question12',10),(13,3,'__question13',5),(14,4,'__question14',20),(15,4,'__question15',10),(16,4,'__question16',30),(17,4,'__question17',5),(18,5,'__question18',10),(19,5,'__question19',25),(20,6,'__question20',15),(21,6,'__question21',15),(22,6,'__question22',20),(23,7,'__question23',5),(24,7,'__question24',5),(25,7,'__question25',30),(26,7,'__question26',15),(27,7,'__question27',10),(28,7,'__question28',20),(29,7,'__question29',10),(30,7,'__question30',5),(31,8,'__question31',10),(32,8,'__question32',20);
=======
INSERT INTO `questions` VALUES (1,1,'__question1',5,1,'✕','36ef6654ff763cbbfaedd29e82382282'),(2,1,'__question2',2,1,'✕','575c911e3cc48dffa78803b1110c2203'),(3,1,'__question3',3,1,'✕','b1ea98907d3d0e098974fc93389132c3'),(4,1,'__question4',4,1,'✕','468beed43fd841757e442ac0a21c5c1b'),(5,2,'__question5',5,1,'✕','03abc7d252410acacfdb1c67c6116b05'),(6,2,'__question6',2,1,'✕','09826ad2aac4dc6585c63023ba51924f'),(7,2,'__question7',3,1,'✕','103ebac19562b3e238a7c93b71b7d245'),(8,2,'__question8',2,1,'✕','0dcdd2516f5c09c28a2ca0d2ad1c2a39'),(9,2,'__question9',3,1,'✕','862db1e978d7514da961931ec0b881e9'),(10,3,'__question10',3,1,'✕','dc581740e0f7b1bbd09742c57ceb20eb'),(11,3,'__question11',4,1,'✕','9cbfbef11150c36dd52c935b000e2cd9'),(12,3,'__question12',4,1,'✕','c3deca21b3b901335d200d2c542f905c'),(13,3,'__question13',5,1,'✕','cd0f4b61bf49762ce45217ea274f4504'),(14,4,'__question14',3,1,'✕','4a5d043cf5519f07e74653b4ce3772ff'),(15,4,'__question15',2,1,'✕','c64fa977fc7f52d52bd4aa631f03ea5c'),(16,4,'__question16',5,1,'✕','663bfc5bf9e67ec052a89a2b38012a5c'),(17,4,'__question17',3,1,'✕','e639a039fdd0344ad8bf5eef06c093c6'),(18,5,'__question18',4,1,'✕','7bed2fcda5fd7a58cdbe80dc4bc7a6c1'),(19,5,'__question19',5,1,'✕','470254c65614619b55cd0b820234569d'),(20,6,'__question20',4,1,'✕','d5963462690631673fe62eccff317144'),(21,6,'__question21',5,1,'✕','393f9322a4565e651a1237c554c688af'),(22,6,'__question22',3,1,'✕','ea72c1972ffdf04c41b1514a023d6f11'),(23,7,'__question23',4,1,'✕','3e8fbb5345f7c371afef96023903d630'),(24,7,'__question24',4,1,'✕','a589e859362fa6a0fbf3a2f0f993f52d'),(25,7,'__question25',3,1,'✕','963d674d2032ac61737101ce2888a81c'),(26,7,'__question26',5,1,'✕','76b5bd21f3538c0f3f1648765d7aa3d2'),(27,7,'__question27',3,1,'✕','ab6fe65dd9882fd8940086f39061a6a7'),(28,7,'__question28',3,1,'✕','5f40af50a154f3109c3baf976b18b796'),(29,7,'__question29',4,1,'✕','3f39467d383f768b824819418fe06cae'),(30,7,'__question30',3,1,'✕','aa7540aa24f123a10566ec38057202b3'),(31,8,'__question31',2,1,'✕','25fb51bf4ed274dc3e8e90f8e598492d'),(32,8,'__question32',3,1,'✕','b5240931f137fb9fa8cdd43d851fa727');
>>>>>>> test
/*!40000 ALTER TABLE `questions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(128) NOT NULL,
  `password` varchar(62) NOT NULL,
  `admin` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (2,'diagnostic@cases.lu','$2y$10$Z2D2ZmdSwmqzqF5Ge6G5/OQ1WvnagAkONVcgjqGWe8/3cUY3o9MjS',1);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users_token`
--

DROP TABLE IF EXISTS `users_token`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_token` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_email` varchar(128) NOT NULL,
  `token` varchar(32) NOT NULL,
  `limit_timestamp` varchar(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_email` (`user_email`),
  CONSTRAINT `users_token_ibfk_1` FOREIGN KEY (`user_email`) REFERENCES `users` (`email`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users_token`
--

LOCK TABLES `users_token` WRITE;
/*!40000 ALTER TABLE `users_token` DISABLE KEYS */;
/*!40000 ALTER TABLE `users_token` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-11-07  2:41:31
