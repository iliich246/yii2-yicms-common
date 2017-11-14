/*
Navicat MySQL Data Transfer

Source Server         : yicms
Source Server Version : 50720
Source Host           : localhost:3306
Source Database       : yicms_test

Target Server Type    : MYSQL
Target Server Version : 50720
File Encoding         : 65001

Date: 2017-11-14 15:32:31
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for common_condition_validators
-- ----------------------------
DROP TABLE IF EXISTS `common_condition_validators`;
CREATE TABLE `common_condition_validators` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `common_condition_template_id` int(11) DEFAULT NULL,
  `validator` varchar(255) DEFAULT NULL,
  `params` text,
  PRIMARY KEY (`id`),
  KEY `common_condition_validators-to-common_conditions_templates` (`common_condition_template_id`) USING BTREE,
  CONSTRAINT `common_condition_validators_ibfk_1` FOREIGN KEY (`common_condition_template_id`) REFERENCES `common_conditions_templates` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of common_condition_validators
-- ----------------------------

-- ----------------------------
-- Table structure for common_conditions
-- ----------------------------
DROP TABLE IF EXISTS `common_conditions`;
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

-- ----------------------------
-- Records of common_conditions
-- ----------------------------

-- ----------------------------
-- Table structure for common_conditions_names
-- ----------------------------
DROP TABLE IF EXISTS `common_conditions_names`;
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

-- ----------------------------
-- Records of common_conditions_names
-- ----------------------------

-- ----------------------------
-- Table structure for common_conditions_templates
-- ----------------------------
DROP TABLE IF EXISTS `common_conditions_templates`;
CREATE TABLE `common_conditions_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `condition_template_reference` int(11) DEFAULT NULL,
  `type` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of common_conditions_templates
-- ----------------------------

-- ----------------------------
-- Table structure for common_conditions_value_names
-- ----------------------------
DROP TABLE IF EXISTS `common_conditions_value_names`;
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

-- ----------------------------
-- Records of common_conditions_value_names
-- ----------------------------

-- ----------------------------
-- Table structure for common_conditions_values
-- ----------------------------
DROP TABLE IF EXISTS `common_conditions_values`;
CREATE TABLE `common_conditions_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `common_condition_template_id` int(11) DEFAULT NULL,
  `value_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `common_conditions_values-to-common_conditions_templates` (`common_condition_template_id`) USING BTREE,
  CONSTRAINT `common_conditions_values_ibfk_1` FOREIGN KEY (`common_condition_template_id`) REFERENCES `common_conditions_templates` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of common_conditions_values
-- ----------------------------

-- ----------------------------
-- Table structure for common_config
-- ----------------------------
DROP TABLE IF EXISTS `common_config`;
CREATE TABLE `common_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `defaultLanguage` varchar(255) DEFAULT NULL,
  `languageMethod` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of common_config
-- ----------------------------
INSERT INTO `common_config` VALUES ('1', 'en-EU', '0');

-- ----------------------------
-- Table structure for common_field_names
-- ----------------------------
DROP TABLE IF EXISTS `common_field_names`;
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

-- ----------------------------
-- Records of common_field_names
-- ----------------------------

-- ----------------------------
-- Table structure for common_field_translates
-- ----------------------------
DROP TABLE IF EXISTS `common_field_translates`;
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

-- ----------------------------
-- Records of common_field_translates
-- ----------------------------

-- ----------------------------
-- Table structure for common_field_validators
-- ----------------------------
DROP TABLE IF EXISTS `common_field_validators`;
CREATE TABLE `common_field_validators` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `common_fields_template_id` int(11) DEFAULT NULL,
  `validator` varchar(255) DEFAULT NULL,
  `params` text,
  PRIMARY KEY (`id`),
  KEY `common_field_validators-to-common_fields_templates` (`common_fields_template_id`) USING BTREE,
  CONSTRAINT `common_field_validators_ibfk_1` FOREIGN KEY (`common_fields_template_id`) REFERENCES `common_fields_templates` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of common_field_validators
-- ----------------------------

-- ----------------------------
-- Table structure for common_fields_represents
-- ----------------------------
DROP TABLE IF EXISTS `common_fields_represents`;
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

-- ----------------------------
-- Records of common_fields_represents
-- ----------------------------

-- ----------------------------
-- Table structure for common_fields_templates
-- ----------------------------
DROP TABLE IF EXISTS `common_fields_templates`;
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

-- ----------------------------
-- Records of common_fields_templates
-- ----------------------------

-- ----------------------------
-- Table structure for common_file_names
-- ----------------------------
DROP TABLE IF EXISTS `common_file_names`;
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

-- ----------------------------
-- Records of common_file_names
-- ----------------------------

-- ----------------------------
-- Table structure for common_file_translates
-- ----------------------------
DROP TABLE IF EXISTS `common_file_translates`;
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

-- ----------------------------
-- Records of common_file_translates
-- ----------------------------

-- ----------------------------
-- Table structure for common_files
-- ----------------------------
DROP TABLE IF EXISTS `common_files`;
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

-- ----------------------------
-- Records of common_files
-- ----------------------------

-- ----------------------------
-- Table structure for common_files_templates
-- ----------------------------
DROP TABLE IF EXISTS `common_files_templates`;
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

-- ----------------------------
-- Records of common_files_templates
-- ----------------------------

-- ----------------------------
-- Table structure for common_files_validators
-- ----------------------------
DROP TABLE IF EXISTS `common_files_validators`;
CREATE TABLE `common_files_validators` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `common_files_template_id` int(11) DEFAULT NULL,
  `validator` varchar(255) DEFAULT NULL,
  `params` text,
  PRIMARY KEY (`id`),
  KEY `common_files_validators-to-common_files_templates` (`common_files_template_id`) USING BTREE,
  CONSTRAINT `common_files_validators_ibfk_1` FOREIGN KEY (`common_files_template_id`) REFERENCES `common_files_templates` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of common_files_validators
-- ----------------------------

-- ----------------------------
-- Table structure for common_images
-- ----------------------------
DROP TABLE IF EXISTS `common_images`;
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

-- ----------------------------
-- Records of common_images
-- ----------------------------

-- ----------------------------
-- Table structure for common_images_names
-- ----------------------------
DROP TABLE IF EXISTS `common_images_names`;
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

-- ----------------------------
-- Records of common_images_names
-- ----------------------------

-- ----------------------------
-- Table structure for common_images_templates
-- ----------------------------
DROP TABLE IF EXISTS `common_images_templates`;
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

-- ----------------------------
-- Records of common_images_templates
-- ----------------------------

-- ----------------------------
-- Table structure for common_images_thumbnails
-- ----------------------------
DROP TABLE IF EXISTS `common_images_thumbnails`;
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

-- ----------------------------
-- Records of common_images_thumbnails
-- ----------------------------

-- ----------------------------
-- Table structure for common_images_translates
-- ----------------------------
DROP TABLE IF EXISTS `common_images_translates`;
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

-- ----------------------------
-- Records of common_images_translates
-- ----------------------------

-- ----------------------------
-- Table structure for common_images_validators
-- ----------------------------
DROP TABLE IF EXISTS `common_images_validators`;
CREATE TABLE `common_images_validators` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `common_images_templates_id` int(11) DEFAULT NULL,
  `validator` varchar(255) DEFAULT NULL,
  `params` text,
  PRIMARY KEY (`id`),
  KEY `common_images_validators-to-common_images_templates` (`common_images_templates_id`) USING BTREE,
  CONSTRAINT `common_images_validators_ibfk_1` FOREIGN KEY (`common_images_templates_id`) REFERENCES `common_images_templates` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of common_images_validators
-- ----------------------------

-- ----------------------------
-- Table structure for common_languages
-- ----------------------------
DROP TABLE IF EXISTS `common_languages`;
CREATE TABLE `common_languages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(5) NOT NULL,
  `name` varchar(255) NOT NULL,
  `used` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of common_languages
-- ----------------------------

-- ----------------------------
-- Table structure for migration
-- ----------------------------
DROP TABLE IF EXISTS `migration`;
CREATE TABLE `migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of migration
-- ----------------------------
INSERT INTO `migration` VALUES ('m000000_000000_base', '1510573199');
INSERT INTO `migration` VALUES ('m171110_213106_common_init', '1510573212');

-- ----------------------------
-- Table structure for test_config_module1
-- ----------------------------
DROP TABLE IF EXISTS `test_config_module1`;
CREATE TABLE `test_config_module1` (
  `id` int(11) NOT NULL,
  `field1` varchar(255) DEFAULT NULL,
  `field2` varchar(255) DEFAULT NULL,
  `field3` varchar(255) DEFAULT NULL,
  `field4` varchar(255) DEFAULT NULL,
  `field5` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of test_config_module1
-- ----------------------------
INSERT INTO `test_config_module1` VALUES ('1', 'field1_value', 'field2_value', 'field3_value', 'field4_value', '5');

-- ----------------------------
-- Table structure for test_config_module2
-- ----------------------------
DROP TABLE IF EXISTS `test_config_module2`;
CREATE TABLE `test_config_module2` (
  `id` int(11) NOT NULL,
  `field1` varchar(255) DEFAULT NULL,
  `field2` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of test_config_module2
-- ----------------------------
INSERT INTO `test_config_module2` VALUES ('1', 'field1_value', '2');
SET FOREIGN_KEY_CHECKS=1;
