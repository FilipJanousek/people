-- Adminer 4.8.1 MySQL 8.4.0 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

CREATE DATABASE `people` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `people`;

DROP TABLE IF EXISTS `humans`;
CREATE TABLE `humans` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `firstname` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `lastname` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `fullname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `alias` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `gender` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `birth_date` datetime NOT NULL,
  `death_date` datetime DEFAULT NULL,
  `country_code` char(2) COLLATE utf8mb4_bin DEFAULT NULL,
  `sex_orientation` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `religion` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `category` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `category` (`category`),
  CONSTRAINT `humans_ibfk_1` FOREIGN KEY (`category`) REFERENCES `humans_categories_types` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;


DROP TABLE IF EXISTS `humans_categories_types`;
CREATE TABLE `humans_categories_types` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

INSERT INTO `humans_categories_types` (`id`, `title`, `code`) VALUES
(1,	'actor',	'ACTOR'),
(2,	'musician',	'MUSICIAN'),
(3,	'family',	'FAMILY');

DROP TABLE IF EXISTS `humans_contacts`;
CREATE TABLE `humans_contacts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `human_id` int unsigned NOT NULL,
  `type` int unsigned DEFAULT NULL,
  `value` varchar(100) COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `human_id` (`human_id`),
  CONSTRAINT `humans_contacts_ibfk_1` FOREIGN KEY (`type`) REFERENCES `humans_contacts_types` (`id`) ON DELETE SET NULL,
  CONSTRAINT `humans_contacts_ibfk_2` FOREIGN KEY (`human_id`) REFERENCES `humans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;


DROP TABLE IF EXISTS `humans_contacts_types`;
CREATE TABLE `humans_contacts_types` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(25) COLLATE utf8mb4_bin NOT NULL,
  `code` varchar(25) COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

INSERT INTO `humans_contacts_types` (`id`, `title`, `code`) VALUES
(1,	'phone',	'PHONE'),
(2,	'email',	'EMAIL');

DROP TABLE IF EXISTS `humans_physical_attributes`;
CREATE TABLE `humans_physical_attributes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `human_id` int unsigned NOT NULL,
  `height` int unsigned DEFAULT NULL,
  `weight` int unsigned DEFAULT NULL,
  `eyes_color` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `hair_color` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `human_id` (`human_id`),
  CONSTRAINT `humans_physical_attributes_ibfk_1` FOREIGN KEY (`human_id`) REFERENCES `humans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;


DROP TABLE IF EXISTS `humans_social_attributes`;
CREATE TABLE `humans_social_attributes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `human_id` int unsigned NOT NULL,
  `siblings` int unsigned DEFAULT NULL,
  `marriages` int unsigned DEFAULT NULL,
  `children` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `human_id` (`human_id`),
  CONSTRAINT `humans_social_attributes_ibfk_1` FOREIGN KEY (`human_id`) REFERENCES `humans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;


-- 2024-07-07 17:15:50
