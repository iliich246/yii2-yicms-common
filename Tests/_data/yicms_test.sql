-- MySQL dump 10.13  Distrib 5.7.12, for Win64 (x86_64)
--
-- Host: localhost    Database: yicms_test
-- ------------------------------------------------------
-- Server version	5.7.20-0ubuntu0.16.04.1

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
-- Table structure for table `common_condition_validators`
--

DROP TABLE IF EXISTS `common_condition_validators`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `common_condition_validators` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `common_condition_template_id` int(11) DEFAULT NULL,
  `validator` varchar(255) DEFAULT NULL,
  `params` text,
  PRIMARY KEY (`id`),
  KEY `common_condition_validators-to-common_conditions_templates` (`common_condition_template_id`) USING BTREE,
  CONSTRAINT `common_condition_validators_ibfk_1` FOREIGN KEY (`common_condition_template_id`) REFERENCES `common_conditions_templates` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `common_condition_validators`
--

LOCK TABLES `common_condition_validators` WRITE;
/*!40000 ALTER TABLE `common_condition_validators` DISABLE KEYS */;
/*!40000 ALTER TABLE `common_condition_validators` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `common_conditions`
--

DROP TABLE IF EXISTS `common_conditions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `common_conditions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `common_condition_template_id` int(11) DEFAULT NULL,
  `condition_reference` int(11) DEFAULT NULL,
  `common_value_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `common_conditions-to-common_conditions_templates` (`common_condition_template_id`) USING BTREE,
  KEY `common_conditions-to-common_conditions_values` (`common_value_id`) USING BTREE,
  CONSTRAINT `common_conditions_ibfk_1` FOREIGN KEY (`common_condition_template_id`) REFERENCES `common_conditions_templates` (`id`),
  CONSTRAINT `common_conditions_ibfk_2` FOREIGN KEY (`common_value_id`) REFERENCES `common_conditions_values` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `common_conditions`
--

LOCK TABLES `common_conditions` WRITE;
/*!40000 ALTER TABLE `common_conditions` DISABLE KEYS */;
/*!40000 ALTER TABLE `common_conditions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `common_conditions_names`
--

DROP TABLE IF EXISTS `common_conditions_names`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `common_conditions_names` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `common_condition_template_id` int(11) DEFAULT NULL,
  `common_language_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `common_conditions_names-to-common_conditions_templates` (`common_condition_template_id`) USING BTREE,
  KEY `common_conditions_names-to-common_languages` (`common_language_id`) USING BTREE,
  CONSTRAINT `common_conditions_names_ibfk_1` FOREIGN KEY (`common_condition_template_id`) REFERENCES `common_conditions_templates` (`id`),
  CONSTRAINT `common_conditions_names_ibfk_2` FOREIGN KEY (`common_language_id`) REFERENCES `common_languages` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `common_conditions_names`
--

LOCK TABLES `common_conditions_names` WRITE;
/*!40000 ALTER TABLE `common_conditions_names` DISABLE KEYS */;
/*!40000 ALTER TABLE `common_conditions_names` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `common_conditions_templates`
--

DROP TABLE IF EXISTS `common_conditions_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `common_conditions_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `condition_template_reference` int(11) DEFAULT NULL,
  `type` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `common_conditions_templates`
--

LOCK TABLES `common_conditions_templates` WRITE;
/*!40000 ALTER TABLE `common_conditions_templates` DISABLE KEYS */;
/*!40000 ALTER TABLE `common_conditions_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `common_conditions_value_names`
--

DROP TABLE IF EXISTS `common_conditions_value_names`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `common_conditions_value_names` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `common_condition_value_id` int(11) DEFAULT NULL,
  `common_language_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `common_conditions_value_names-to-common_conditions_values` (`common_condition_value_id`) USING BTREE,
  KEY `common_conditions_value_names-to-common_languages` (`common_language_id`) USING BTREE,
  CONSTRAINT `common_conditions_value_names_ibfk_1` FOREIGN KEY (`common_condition_value_id`) REFERENCES `common_conditions_values` (`id`),
  CONSTRAINT `common_conditions_value_names_ibfk_2` FOREIGN KEY (`common_language_id`) REFERENCES `common_languages` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `common_conditions_value_names`
--

LOCK TABLES `common_conditions_value_names` WRITE;
/*!40000 ALTER TABLE `common_conditions_value_names` DISABLE KEYS */;
/*!40000 ALTER TABLE `common_conditions_value_names` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `common_conditions_values`
--

DROP TABLE IF EXISTS `common_conditions_values`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `common_conditions_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `common_condition_template_id` int(11) DEFAULT NULL,
  `value_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `common_conditions_values-to-common_conditions_templates` (`common_condition_template_id`) USING BTREE,
  CONSTRAINT `common_conditions_values_ibfk_1` FOREIGN KEY (`common_condition_template_id`) REFERENCES `common_conditions_templates` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `common_conditions_values`
--

LOCK TABLES `common_conditions_values` WRITE;
/*!40000 ALTER TABLE `common_conditions_values` DISABLE KEYS */;
/*!40000 ALTER TABLE `common_conditions_values` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `common_config`
--

DROP TABLE IF EXISTS `common_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `common_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `defaultLanguage` varchar(255) DEFAULT NULL,
  `languageMethod` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `common_config`
--

LOCK TABLES `common_config` WRITE;
/*!40000 ALTER TABLE `common_config` DISABLE KEYS */;
INSERT INTO `common_config` VALUES (1,'en-EU','0');
/*!40000 ALTER TABLE `common_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `common_field_names`
--

DROP TABLE IF EXISTS `common_field_names`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `common_field_names` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `common_fields_template_id` int(11) DEFAULT NULL,
  `common_language_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `common_field_names-to-common_fields_templates` (`common_fields_template_id`) USING BTREE,
  KEY `common_field_names-to-common_languages` (`common_language_id`) USING BTREE,
  CONSTRAINT `common_field_names_ibfk_1` FOREIGN KEY (`common_fields_template_id`) REFERENCES `common_fields_templates` (`id`),
  CONSTRAINT `common_field_names_ibfk_2` FOREIGN KEY (`common_language_id`) REFERENCES `common_languages` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `common_field_names`
--

LOCK TABLES `common_field_names` WRITE;
/*!40000 ALTER TABLE `common_field_names` DISABLE KEYS */;
/*!40000 ALTER TABLE `common_field_names` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `common_field_translates`
--

DROP TABLE IF EXISTS `common_field_translates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `common_field_translates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `common_fields_represent_id` int(11) DEFAULT NULL,
  `common_language_id` int(11) DEFAULT NULL,
  `value` text,
  PRIMARY KEY (`id`),
  KEY `common_field_translates-to-common_fields_represents` (`common_fields_represent_id`) USING BTREE,
  KEY `common_field_translates-to-common_languages` (`common_language_id`) USING BTREE,
  CONSTRAINT `common_field_translates_ibfk_1` FOREIGN KEY (`common_fields_represent_id`) REFERENCES `common_fields_represents` (`id`),
  CONSTRAINT `common_field_translates_ibfk_2` FOREIGN KEY (`common_language_id`) REFERENCES `common_languages` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `common_field_translates`
--

LOCK TABLES `common_field_translates` WRITE;
/*!40000 ALTER TABLE `common_field_translates` DISABLE KEYS */;
/*!40000 ALTER TABLE `common_field_translates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `common_field_validators`
--

DROP TABLE IF EXISTS `common_field_validators`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `common_field_validators` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `common_fields_template_id` int(11) DEFAULT NULL,
  `validator` varchar(255) DEFAULT NULL,
  `params` text,
  PRIMARY KEY (`id`),
  KEY `common_field_validators-to-common_fields_templates` (`common_fields_template_id`) USING BTREE,
  CONSTRAINT `common_field_validators_ibfk_1` FOREIGN KEY (`common_fields_template_id`) REFERENCES `common_fields_templates` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `common_field_validators`
--

LOCK TABLES `common_field_validators` WRITE;
/*!40000 ALTER TABLE `common_field_validators` DISABLE KEYS */;
/*!40000 ALTER TABLE `common_field_validators` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `common_fields_represents`
--

DROP TABLE IF EXISTS `common_fields_represents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `common_fields_represents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `common_fields_template_id` int(11) DEFAULT NULL,
  `field_reference` int(11) DEFAULT NULL,
  `editable` tinyint(1) DEFAULT NULL,
  `visible` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `common_fields_represents-to-common_fields_templates` (`common_fields_template_id`) USING BTREE,
  CONSTRAINT `common_fields_represents_ibfk_1` FOREIGN KEY (`common_fields_template_id`) REFERENCES `common_fields_templates` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `common_fields_represents`
--

LOCK TABLES `common_fields_represents` WRITE;
/*!40000 ALTER TABLE `common_fields_represents` DISABLE KEYS */;
/*!40000 ALTER TABLE `common_fields_represents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `common_fields_templates`
--

DROP TABLE IF EXISTS `common_fields_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `common_fields_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `field_template_reference` int(11) DEFAULT NULL,
  `program_name` varchar(50) DEFAULT NULL,
  `type` smallint(6) DEFAULT NULL,
  `editable` tinyint(1) DEFAULT NULL,
  `visible` tinyint(1) DEFAULT NULL,
  `is_main` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `common_fields_templates`
--

LOCK TABLES `common_fields_templates` WRITE;
/*!40000 ALTER TABLE `common_fields_templates` DISABLE KEYS */;
/*!40000 ALTER TABLE `common_fields_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `common_file_names`
--

DROP TABLE IF EXISTS `common_file_names`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `common_file_names` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `common_files_template_id` int(11) DEFAULT NULL,
  `common_language_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`id`),
  KEY `common_file_names-to-common_files_templates` (`common_files_template_id`) USING BTREE,
  KEY `common_file_names-to-common_languages` (`common_language_id`) USING BTREE,
  CONSTRAINT `common_file_names_ibfk_1` FOREIGN KEY (`common_files_template_id`) REFERENCES `common_file_translates` (`id`),
  CONSTRAINT `common_file_names_ibfk_2` FOREIGN KEY (`common_language_id`) REFERENCES `common_languages` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `common_file_names`
--

LOCK TABLES `common_file_names` WRITE;
/*!40000 ALTER TABLE `common_file_names` DISABLE KEYS */;
/*!40000 ALTER TABLE `common_file_names` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `common_file_translates`
--

DROP TABLE IF EXISTS `common_file_translates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `common_file_translates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `common_file_id` int(11) DEFAULT NULL,
  `common_language_id` int(11) DEFAULT NULL,
  `system_name` varchar(255) DEFAULT NULL,
  `original_name` varchar(255) DEFAULT NULL,
  `size` int(11) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `common_file_translates-to-common_files` (`common_file_id`) USING BTREE,
  KEY `common_file_translates-to-common_languages` (`common_language_id`) USING BTREE,
  CONSTRAINT `common_file_translates_ibfk_1` FOREIGN KEY (`common_file_id`) REFERENCES `common_files` (`id`),
  CONSTRAINT `common_file_translates_ibfk_2` FOREIGN KEY (`common_language_id`) REFERENCES `common_languages` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `common_file_translates`
--

LOCK TABLES `common_file_translates` WRITE;
/*!40000 ALTER TABLE `common_file_translates` DISABLE KEYS */;
/*!40000 ALTER TABLE `common_file_translates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `common_files`
--

DROP TABLE IF EXISTS `common_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `common_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `common_files_template_id` int(11) DEFAULT NULL,
  `file_reference` int(11) DEFAULT NULL,
  `field_reference` int(11) DEFAULT NULL,
  `system_name` varchar(255) DEFAULT NULL,
  `original_name` varchar(255) DEFAULT NULL,
  `size` int(11) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `editable` tinyint(1) DEFAULT NULL,
  `visible` tinyint(1) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `common_files-to-common_files_templates` (`common_files_template_id`) USING BTREE,
  CONSTRAINT `common_files_ibfk_1` FOREIGN KEY (`common_files_template_id`) REFERENCES `common_files_templates` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `common_files`
--

LOCK TABLES `common_files` WRITE;
/*!40000 ALTER TABLE `common_files` DISABLE KEYS */;
/*!40000 ALTER TABLE `common_files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `common_files_templates`
--

DROP TABLE IF EXISTS `common_files_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `common_files_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file_template_reference` int(11) DEFAULT NULL,
  `field_template_reference` int(11) DEFAULT NULL,
  `program_name` varchar(50) DEFAULT NULL,
  `type` smallint(6) DEFAULT NULL,
  `language_type` smallint(6) DEFAULT NULL,
  `max_files` int(11) DEFAULT NULL,
  `max_size` int(11) DEFAULT NULL,
  `allow_files` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `common_files_templates`
--

LOCK TABLES `common_files_templates` WRITE;
/*!40000 ALTER TABLE `common_files_templates` DISABLE KEYS */;
/*!40000 ALTER TABLE `common_files_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `common_files_validators`
--

DROP TABLE IF EXISTS `common_files_validators`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `common_files_validators` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `common_files_template_id` int(11) DEFAULT NULL,
  `validator` varchar(255) DEFAULT NULL,
  `params` text,
  PRIMARY KEY (`id`),
  KEY `common_files_validators-to-common_files_templates` (`common_files_template_id`) USING BTREE,
  CONSTRAINT `common_files_validators_ibfk_1` FOREIGN KEY (`common_files_template_id`) REFERENCES `common_files_templates` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `common_files_validators`
--

LOCK TABLES `common_files_validators` WRITE;
/*!40000 ALTER TABLE `common_files_validators` DISABLE KEYS */;
/*!40000 ALTER TABLE `common_files_validators` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `common_images`
--

DROP TABLE IF EXISTS `common_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `common_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `common_images_templates_id` int(11) DEFAULT NULL,
  `image_reference` int(11) DEFAULT NULL,
  `field_reference` int(11) DEFAULT NULL,
  `system_name` varchar(255) DEFAULT NULL,
  `original_name` varchar(255) DEFAULT NULL,
  `size` int(11) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `editable` tinyint(1) DEFAULT NULL,
  `visible` tinyint(1) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `common_images-to-common_images_templates` (`common_images_templates_id`) USING BTREE,
  CONSTRAINT `common_images_ibfk_1` FOREIGN KEY (`common_images_templates_id`) REFERENCES `common_images_templates` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `common_images`
--

LOCK TABLES `common_images` WRITE;
/*!40000 ALTER TABLE `common_images` DISABLE KEYS */;
/*!40000 ALTER TABLE `common_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `common_images_names`
--

DROP TABLE IF EXISTS `common_images_names`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `common_images_names` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `common_images_templates_id` int(11) DEFAULT NULL,
  `common_language_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`id`),
  KEY `common_images_names-to-common_images_templates` (`common_images_templates_id`) USING BTREE,
  KEY `common_images_names-to-common_languages` (`common_language_id`) USING BTREE,
  CONSTRAINT `common_images_names_ibfk_1` FOREIGN KEY (`common_images_templates_id`) REFERENCES `common_images_templates` (`id`),
  CONSTRAINT `common_images_names_ibfk_2` FOREIGN KEY (`common_language_id`) REFERENCES `common_languages` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `common_images_names`
--

LOCK TABLES `common_images_names` WRITE;
/*!40000 ALTER TABLE `common_images_names` DISABLE KEYS */;
/*!40000 ALTER TABLE `common_images_names` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `common_images_templates`
--

DROP TABLE IF EXISTS `common_images_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `common_images_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `image_template_reference` int(11) DEFAULT NULL,
  `field_template_reference` int(11) DEFAULT NULL,
  `program_name` varchar(50) DEFAULT NULL,
  `type` smallint(6) DEFAULT NULL,
  `language_type` smallint(6) DEFAULT NULL,
  `crop_type` smallint(6) DEFAULT NULL,
  `max_images` int(11) DEFAULT NULL,
  `max_size` int(11) DEFAULT NULL,
  `allow_files` varchar(255) DEFAULT NULL,
  `crop_height` int(11) DEFAULT NULL,
  `crop_width` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `common_images_templates`
--

LOCK TABLES `common_images_templates` WRITE;
/*!40000 ALTER TABLE `common_images_templates` DISABLE KEYS */;
/*!40000 ALTER TABLE `common_images_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `common_images_thumbnails`
--

DROP TABLE IF EXISTS `common_images_thumbnails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `common_images_thumbnails` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `common_images_templates_id` int(11) DEFAULT NULL,
  `program_mane` varchar(50) DEFAULT NULL,
  `divider` smallint(6) DEFAULT NULL,
  `quality` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `common_images_thumbnails-to-common_images_templates` (`common_images_templates_id`) USING BTREE,
  CONSTRAINT `common_images_thumbnails_ibfk_1` FOREIGN KEY (`common_images_templates_id`) REFERENCES `common_images_templates` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `common_images_thumbnails`
--

LOCK TABLES `common_images_thumbnails` WRITE;
/*!40000 ALTER TABLE `common_images_thumbnails` DISABLE KEYS */;
/*!40000 ALTER TABLE `common_images_thumbnails` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `common_images_translates`
--

DROP TABLE IF EXISTS `common_images_translates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `common_images_translates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `common_image_id` int(11) DEFAULT NULL,
  `common_language_id` int(11) DEFAULT NULL,
  `system_name` varchar(255) DEFAULT NULL,
  `original_name` varchar(255) DEFAULT NULL,
  `size` int(11) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `editable` tinyint(1) DEFAULT NULL,
  `visible` tinyint(1) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `common_images_translates-to-common_images` (`common_image_id`) USING BTREE,
  KEY `common_images_translates-to-common_languages` (`common_language_id`) USING BTREE,
  CONSTRAINT `common_images_translates_ibfk_1` FOREIGN KEY (`common_image_id`) REFERENCES `common_images` (`id`),
  CONSTRAINT `common_images_translates_ibfk_2` FOREIGN KEY (`common_language_id`) REFERENCES `common_languages` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `common_images_translates`
--

LOCK TABLES `common_images_translates` WRITE;
/*!40000 ALTER TABLE `common_images_translates` DISABLE KEYS */;
/*!40000 ALTER TABLE `common_images_translates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `common_images_validators`
--

DROP TABLE IF EXISTS `common_images_validators`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `common_images_validators` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `common_images_templates_id` int(11) DEFAULT NULL,
  `validator` varchar(255) DEFAULT NULL,
  `params` text,
  PRIMARY KEY (`id`),
  KEY `common_images_validators-to-common_images_templates` (`common_images_templates_id`) USING BTREE,
  CONSTRAINT `common_images_validators_ibfk_1` FOREIGN KEY (`common_images_templates_id`) REFERENCES `common_images_templates` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `common_images_validators`
--

LOCK TABLES `common_images_validators` WRITE;
/*!40000 ALTER TABLE `common_images_validators` DISABLE KEYS */;
/*!40000 ALTER TABLE `common_images_validators` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `common_languages`
--

DROP TABLE IF EXISTS `common_languages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `common_languages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(5) NOT NULL,
  `name` varchar(255) NOT NULL,
  `used` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `common_languages`
--

LOCK TABLES `common_languages` WRITE;
/*!40000 ALTER TABLE `common_languages` DISABLE KEYS */;
/*!40000 ALTER TABLE `common_languages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migration`
--

DROP TABLE IF EXISTS `migration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migration`
--

LOCK TABLES `migration` WRITE;
/*!40000 ALTER TABLE `migration` DISABLE KEYS */;
INSERT INTO `migration` VALUES ('m000000_000000_base',1510573199),('m171110_213106_common_init',1510573212);
/*!40000 ALTER TABLE `migration` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `test_config_module1`
--

DROP TABLE IF EXISTS `test_config_module1`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_config_module1` (
  `id` int(11) NOT NULL,
  `field1` varchar(255) DEFAULT NULL,
  `field2` varchar(255) DEFAULT NULL,
  `field3` varchar(255) DEFAULT NULL,
  `field4` varchar(255) DEFAULT NULL,
  `field5` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_config_module1`
--

LOCK TABLES `test_config_module1` WRITE;
/*!40000 ALTER TABLE `test_config_module1` DISABLE KEYS */;
INSERT INTO `test_config_module1` VALUES (1,'field1_value','field2_value','field3_value','field4_value','5');
/*!40000 ALTER TABLE `test_config_module1` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `test_config_module2`
--

DROP TABLE IF EXISTS `test_config_module2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_config_module2` (
  `id` int(11) NOT NULL,
  `field1` varchar(255) DEFAULT NULL,
  `field2` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_config_module2`
--

LOCK TABLES `test_config_module2` WRITE;
/*!40000 ALTER TABLE `test_config_module2` DISABLE KEYS */;
INSERT INTO `test_config_module2` VALUES (1,'field1_value','2');
/*!40000 ALTER TABLE `test_config_module2` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `test_templates`
--

DROP TABLE IF EXISTS `test_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_templates` (
  `id` int(11) NOT NULL,
  `test_template_reference` varchar(45) DEFAULT NULL,
  `program_name` varchar(45) DEFAULT NULL,
  `type` varchar(45) DEFAULT NULL,
  `editable` varchar(45) DEFAULT NULL,
  `visible` varchar(45) DEFAULT NULL,
  `is_main` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_templates`
--

LOCK TABLES `test_templates` WRITE;
/*!40000 ALTER TABLE `test_templates` DISABLE KEYS */;
INSERT INTO `test_templates` VALUES (1,'100100','name1',NULL,NULL,NULL,NULL),(2,'100100','name2',NULL,NULL,NULL,NULL),(3,'100100','name3',NULL,NULL,NULL,NULL),(4,'230101','name1',NULL,NULL,NULL,NULL),(5,'230101','name2',NULL,NULL,NULL,NULL),(6,'230101','name3',NULL,NULL,NULL,NULL),(7,'230101','name4',NULL,NULL,NULL,NULL),(8,'912233','name1',NULL,NULL,NULL,NULL),(9,'912233','name2',NULL,NULL,NULL,NULL),(10,'912233','name3',NULL,NULL,NULL,NULL),(11,'912233','name99',NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `test_templates` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-11-15 22:15:32
