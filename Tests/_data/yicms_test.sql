/*
Navicat MySQL Data Transfer

Source Server         : yicms
Source Server Version : 50720
Source Host           : localhost:3306
Source Database       : yicms

Target Server Type    : MYSQL
Target Server Version : 50720
File Encoding         : 65001

Date: 2017-12-19 14:20:37
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for common_condition_validators
-- ----------------------------
DROP TABLE IF EXISTS `common_condition_validators`;
CREATE TABLE `common_condition_validators` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `common_condition_template_id` int(11) DEFAULT NULL,
  `validator` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `params` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `common_condition_validators-to-common_conditions_templates` (`common_condition_template_id`),
  CONSTRAINT `common_condition_validators-to-common_conditions_templates` FOREIGN KEY (`common_condition_template_id`) REFERENCES `common_conditions_templates` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
  `condition_reference` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `common_value_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `condition_reference-index` (`condition_reference`),
  KEY `common_conditions-to-common_conditions_templates` (`common_condition_template_id`),
  KEY `common_conditions-to-common_conditions_values` (`common_value_id`),
  CONSTRAINT `common_conditions-to-common_conditions_templates` FOREIGN KEY (`common_condition_template_id`) REFERENCES `common_conditions_templates` (`id`),
  CONSTRAINT `common_conditions-to-common_conditions_values` FOREIGN KEY (`common_value_id`) REFERENCES `common_conditions_values` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `common_conditions_names-to-common_conditions_templates` (`common_condition_template_id`),
  KEY `common_conditions_names-to-common_languages` (`common_language_id`),
  CONSTRAINT `common_conditions_names-to-common_conditions_templates` FOREIGN KEY (`common_condition_template_id`) REFERENCES `common_conditions_templates` (`id`),
  CONSTRAINT `common_conditions_names-to-common_languages` FOREIGN KEY (`common_language_id`) REFERENCES `common_languages` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of common_conditions_names
-- ----------------------------

-- ----------------------------
-- Table structure for common_conditions_templates
-- ----------------------------
DROP TABLE IF EXISTS `common_conditions_templates`;
CREATE TABLE `common_conditions_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `condition_template_reference` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` smallint(6) DEFAULT NULL,
  `condition_order` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `condition_template_reference-index` (`condition_template_reference`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `common_conditions_value_names-to-common_conditions_values` (`common_condition_value_id`),
  KEY `common_conditions_value_names-to-common_languages` (`common_language_id`),
  CONSTRAINT `common_conditions_value_names-to-common_conditions_values` FOREIGN KEY (`common_condition_value_id`) REFERENCES `common_conditions_values` (`id`),
  CONSTRAINT `common_conditions_value_names-to-common_languages` FOREIGN KEY (`common_language_id`) REFERENCES `common_languages` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
  `value_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `common_conditions_values-to-common_conditions_templates` (`common_condition_template_id`),
  CONSTRAINT `common_conditions_values-to-common_conditions_templates` FOREIGN KEY (`common_condition_template_id`) REFERENCES `common_conditions_templates` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of common_conditions_values
-- ----------------------------

-- ----------------------------
-- Table structure for common_config
-- ----------------------------
DROP TABLE IF EXISTS `common_config`;
CREATE TABLE `common_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `defaultLanguage` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `languageMethod` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of common_config
-- ----------------------------
INSERT INTO `common_config` VALUES ('1', 'en-EU', '1');

-- ----------------------------
-- Table structure for common_field_names
-- ----------------------------
DROP TABLE IF EXISTS `common_field_names`;
CREATE TABLE `common_field_names` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `common_fields_template_id` int(11) DEFAULT NULL,
  `common_language_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `common_field_names-to-common_fields_templates` (`common_fields_template_id`),
  KEY `common_field_names-to-common_languages` (`common_language_id`),
  CONSTRAINT `common_field_names-to-common_fields_templates` FOREIGN KEY (`common_fields_template_id`) REFERENCES `common_fields_templates` (`id`),
  CONSTRAINT `common_field_names-to-common_languages` FOREIGN KEY (`common_language_id`) REFERENCES `common_languages` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
  `value` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `common_field_translates-to-common_fields_represents` (`common_fields_represent_id`),
  KEY `common_field_translates-to-common_languages` (`common_language_id`),
  CONSTRAINT `common_field_translates-to-common_fields_represents` FOREIGN KEY (`common_fields_represent_id`) REFERENCES `common_fields_represents` (`id`),
  CONSTRAINT `common_field_translates-to-common_languages` FOREIGN KEY (`common_language_id`) REFERENCES `common_languages` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
  `validator` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `params` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `common_field_validators-to-common_fields_templates` (`common_fields_template_id`),
  CONSTRAINT `common_field_validators-to-common_fields_templates` FOREIGN KEY (`common_fields_template_id`) REFERENCES `common_fields_templates` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
  `field_reference` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `value` text COLLATE utf8_unicode_ci,
  `editable` tinyint(1) DEFAULT NULL,
  `visible` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `field_reference-index` (`field_reference`),
  KEY `common_fields_represents-to-common_fields_templates` (`common_fields_template_id`),
  CONSTRAINT `common_fields_represents-to-common_fields_templates` FOREIGN KEY (`common_fields_template_id`) REFERENCES `common_fields_templates` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of common_fields_represents
-- ----------------------------
-- ----------------------------
-- Table structure for common_fields_templates
-- ----------------------------
DROP TABLE IF EXISTS `common_fields_templates`;
CREATE TABLE `common_fields_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `field_template_reference` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `program_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` smallint(6) DEFAULT NULL,
  `language_type` smallint(6) DEFAULT NULL,
  `field_order` int(11) DEFAULT NULL,
  `editable` tinyint(1) DEFAULT NULL,
  `visible` tinyint(1) DEFAULT NULL,
  `is_main` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `field_template_reference-index` (`field_template_reference`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `common_file_names-to-common_files_templates` (`common_files_template_id`),
  KEY `common_file_names-to-common_languages` (`common_language_id`),
  CONSTRAINT `common_file_names-to-common_files_templates` FOREIGN KEY (`common_files_template_id`) REFERENCES `common_file_translates` (`id`),
  CONSTRAINT `common_file_names-to-common_languages` FOREIGN KEY (`common_language_id`) REFERENCES `common_languages` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
  `system_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `original_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `size` int(11) DEFAULT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `common_file_translates-to-common_files` (`common_file_id`),
  KEY `common_file_translates-to-common_languages` (`common_language_id`),
  CONSTRAINT `common_file_translates-to-common_files` FOREIGN KEY (`common_file_id`) REFERENCES `common_files` (`id`),
  CONSTRAINT `common_file_translates-to-common_languages` FOREIGN KEY (`common_language_id`) REFERENCES `common_languages` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
  `file_reference` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `field_reference` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `system_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `original_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `size` int(11) DEFAULT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `editable` tinyint(1) DEFAULT NULL,
  `visible` tinyint(1) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `file_reference-index` (`file_reference`),
  KEY `common_files-to-common_files_templates` (`common_files_template_id`),
  CONSTRAINT `common_files-to-common_files_templates` FOREIGN KEY (`common_files_template_id`) REFERENCES `common_files_templates` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of common_files
-- ----------------------------

-- ----------------------------
-- Table structure for common_files_templates
-- ----------------------------
DROP TABLE IF EXISTS `common_files_templates`;
CREATE TABLE `common_files_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file_template_reference` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `field_template_reference` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `program_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` smallint(6) DEFAULT NULL,
  `file_order` int(11) DEFAULT NULL,
  `language_type` smallint(6) DEFAULT NULL,
  `editable` tinyint(1) DEFAULT NULL,
  `visible` tinyint(1) DEFAULT NULL,
  `max_files` int(11) DEFAULT NULL,
  `max_size` int(11) DEFAULT NULL,
  `allow_files` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `file_template_reference-index` (`file_template_reference`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
  `validator` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `params` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `common_files_validators-to-common_files_templates` (`common_files_template_id`),
  CONSTRAINT `common_files_validators-to-common_files_templates` FOREIGN KEY (`common_files_template_id`) REFERENCES `common_files_templates` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of common_files_validators
-- ----------------------------

-- ----------------------------
-- Table structure for common_free_essence_name_translates
-- ----------------------------
DROP TABLE IF EXISTS `common_free_essence_name_translates`;
CREATE TABLE `common_free_essence_name_translates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `common_free_essence_id` int(11) DEFAULT NULL,
  `common_language_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `common_free_essence_name_translates-to-common_free_essences` (`common_free_essence_id`),
  KEY `common_free_essence_name_translates-to-common_languages` (`common_language_id`),
  CONSTRAINT `common_free_essence_name_translates-to-common_free_essences` FOREIGN KEY (`common_free_essence_id`) REFERENCES `common_free_essences` (`id`),
  CONSTRAINT `common_free_essence_name_translates-to-common_languages` FOREIGN KEY (`common_language_id`) REFERENCES `common_languages` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of common_free_essence_name_translates
-- ----------------------------

-- ----------------------------
-- Table structure for common_free_essences
-- ----------------------------
DROP TABLE IF EXISTS `common_free_essences`;
CREATE TABLE `common_free_essences` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `program_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `editable` tinyint(1) DEFAULT NULL,
  `visible` tinyint(1) DEFAULT NULL,
  `free_essences_order` int(11) DEFAULT NULL,
  `field_template_reference` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `field_reference` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `file_template_reference` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `file_reference` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_template_reference` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_reference` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `condition_template_reference` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `condition_reference` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of common_free_essences
-- ----------------------------
-- ----------------------------
-- Table structure for common_images
-- ----------------------------
DROP TABLE IF EXISTS `common_images`;
CREATE TABLE `common_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `common_images_templates_id` int(11) DEFAULT NULL,
  `image_reference` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `field_reference` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `system_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `original_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `size` int(11) DEFAULT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `editable` tinyint(1) DEFAULT NULL,
  `visible` tinyint(1) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `image_reference-index` (`image_reference`),
  KEY `common_images-to-common_images_templates` (`common_images_templates_id`),
  CONSTRAINT `common_images-to-common_images_templates` FOREIGN KEY (`common_images_templates_id`) REFERENCES `common_images_templates` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `common_images_names-to-common_images_templates` (`common_images_templates_id`),
  KEY `common_images_names-to-common_languages` (`common_language_id`),
  CONSTRAINT `common_images_names-to-common_images_templates` FOREIGN KEY (`common_images_templates_id`) REFERENCES `common_images_templates` (`id`),
  CONSTRAINT `common_images_names-to-common_languages` FOREIGN KEY (`common_language_id`) REFERENCES `common_languages` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of common_images_names
-- ----------------------------

-- ----------------------------
-- Table structure for common_images_templates
-- ----------------------------
DROP TABLE IF EXISTS `common_images_templates`;
CREATE TABLE `common_images_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `image_template_reference` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `field_template_reference` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `program_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` smallint(6) DEFAULT NULL,
  `image_order` int(11) DEFAULT NULL,
  `language_type` smallint(6) DEFAULT NULL,
  `editable` tinyint(1) DEFAULT NULL,
  `visible` tinyint(1) DEFAULT NULL,
  `crop_type` smallint(6) DEFAULT NULL,
  `max_images` int(11) DEFAULT NULL,
  `max_size` int(11) DEFAULT NULL,
  `allow_files` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `crop_height` int(11) DEFAULT NULL,
  `crop_width` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `image_template_reference-index` (`image_template_reference`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
  `program_mane` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `divider` smallint(6) DEFAULT NULL,
  `quality` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `common_images_thumbnails-to-common_images_templates` (`common_images_templates_id`),
  CONSTRAINT `common_images_thumbnails-to-common_images_templates` FOREIGN KEY (`common_images_templates_id`) REFERENCES `common_images_templates` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
  `system_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `original_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `size` int(11) DEFAULT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `editable` tinyint(1) DEFAULT NULL,
  `visible` tinyint(1) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `common_images_translates-to-common_images` (`common_image_id`),
  KEY `common_images_translates-to-common_languages` (`common_language_id`),
  CONSTRAINT `common_images_translates-to-common_images` FOREIGN KEY (`common_image_id`) REFERENCES `common_images` (`id`),
  CONSTRAINT `common_images_translates-to-common_languages` FOREIGN KEY (`common_language_id`) REFERENCES `common_languages` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
  `validator` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `params` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `common_images_validators-to-common_images_templates` (`common_images_templates_id`),
  CONSTRAINT `common_images_validators-to-common_images_templates` FOREIGN KEY (`common_images_templates_id`) REFERENCES `common_images_templates` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of common_images_validators
-- ----------------------------

-- ----------------------------
-- Table structure for common_languages
-- ----------------------------
DROP TABLE IF EXISTS `common_languages`;
CREATE TABLE `common_languages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `used` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of common_languages
-- ----------------------------
INSERT INTO `common_languages` VALUES ('1', 'en-EU', 'English', '1');
INSERT INTO `common_languages` VALUES ('2', 'ru-RU', 'Русский', '0');

-- ----------------------------
-- Table structure for migration
-- ----------------------------
DROP TABLE IF EXISTS `migration`;
CREATE TABLE `migration` (
  `version` varchar(180) COLLATE utf8_unicode_ci NOT NULL,
  `apply_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of migration
-- ----------------------------
INSERT INTO `migration` VALUES ('m000000_000000_base', '1511168330');
INSERT INTO `migration` VALUES ('m171110_213106_common_init', '1512734626');
INSERT INTO `migration` VALUES ('m171114_174807_pages_init', '1512734626');

-- ----------------------------
-- Table structure for pages
-- ----------------------------
DROP TABLE IF EXISTS `pages`;
CREATE TABLE `pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `program_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `editable` tinyint(1) DEFAULT NULL,
  `visible` tinyint(1) DEFAULT NULL,
  `system_route` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ruled_route` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pages_order` int(11) DEFAULT NULL,
  `field_template_reference` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `field_reference` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `file_template_reference` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `file_reference` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_template_reference` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_reference` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `condition_template_reference` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `condition_reference` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of pages
-- ----------------------------
-- ----------------------------
-- Table structure for pages_config
-- ----------------------------
DROP TABLE IF EXISTS `pages_config`;
CREATE TABLE `pages_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `imagesPatch` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `filesPatch` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `thumbNailsDirectoryName` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of pages_config
-- ----------------------------
-- ----------------------------
-- Table structure for pages_names_translates
-- ----------------------------
DROP TABLE IF EXISTS `pages_names_translates`;
CREATE TABLE `pages_names_translates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_id` int(11) DEFAULT NULL,
  `common_language_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pages_names_translates-to-pages` (`page_id`),
  KEY `pages_names_translates-to-common_languages` (`common_language_id`),
  CONSTRAINT `pages_names_translates-to-common_languages` FOREIGN KEY (`common_language_id`) REFERENCES `common_languages` (`id`),
  CONSTRAINT `pages_names_translates-to-pages` FOREIGN KEY (`page_id`) REFERENCES `pages` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of pages_names_translates
-- ----------------------------
-- ----------------------------
-- Records of pages_names_translates
-- ----------------------------
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of test_config_module2
-- ----------------------------
INSERT INTO `test_config_module2` VALUES ('1', 'field1_value', '2');

-- ----------------------------
-- Table structure for test_templates
-- ----------------------------
DROP TABLE IF EXISTS `test_templates`;
CREATE TABLE `test_templates` (
  `id` int(11) NOT NULL,
  `test_template_reference` varchar(45) DEFAULT NULL,
  `program_name` varchar(45) DEFAULT NULL,
  `type` varchar(45) DEFAULT NULL,
  `editable` varchar(45) DEFAULT NULL,
  `visible` varchar(45) DEFAULT NULL,
  `is_main` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of test_templates
-- ----------------------------
INSERT INTO `test_templates` VALUES ('1', '100100', 'name1', null, null, null, null);
INSERT INTO `test_templates` VALUES ('2', '100100', 'name2', null, null, null, null);
INSERT INTO `test_templates` VALUES ('3', '100100', 'name3', null, null, null, null);
INSERT INTO `test_templates` VALUES ('4', '230101', 'name1', null, null, null, null);
INSERT INTO `test_templates` VALUES ('5', '230101', 'name2', null, null, null, null);
INSERT INTO `test_templates` VALUES ('6', '230101', 'name3', null, null, null, null);
INSERT INTO `test_templates` VALUES ('7', '230101', 'name4', null, null, null, null);
INSERT INTO `test_templates` VALUES ('8', '912233', 'name1', null, null, null, null);
INSERT INTO `test_templates` VALUES ('9', '912233', 'name2', null, null, null, null);
INSERT INTO `test_templates` VALUES ('10', '912233', 'name3', null, null, null, null);
INSERT INTO `test_templates` VALUES ('11', '912233', 'name99', null, null, null, null);

-- ----------------------------
-- Table structure for test_templates2
-- ----------------------------
DROP TABLE IF EXISTS `test_templates2`;
CREATE TABLE `test_templates2` (
  `id` int(255) NOT NULL,
  `test_template_reference` varchar(255) NOT NULL,
  `program_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of test_templates2
-- ----------------------------
INSERT INTO `test_templates2` VALUES ('1', '100100', 'name1');
INSERT INTO `test_templates2` VALUES ('2', '100100', 'name2');
INSERT INTO `test_templates2` VALUES ('3', '100100', 'name3');
INSERT INTO `test_templates2` VALUES ('4', '230101', 'name1');
INSERT INTO `test_templates2` VALUES ('5', '230101', 'name2');
INSERT INTO `test_templates2` VALUES ('6', '230101', 'name3');
INSERT INTO `test_templates2` VALUES ('7', '230101', 'name4');
INSERT INTO `test_templates2` VALUES ('8', '912233', 'name1');
INSERT INTO `test_templates2` VALUES ('9', '912233', 'name2');
INSERT INTO `test_templates2` VALUES ('10', '912233', 'name3');
INSERT INTO `test_templates2` VALUES ('11', '912233', 'name99');
SET FOREIGN_KEY_CHECKS=1;
