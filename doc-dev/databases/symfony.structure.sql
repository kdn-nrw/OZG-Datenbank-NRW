-- MySQL dump 10.13  Distrib 5.7.24, for Linux (x86_64)
--
-- Host: localhost    Database: symfony
-- ------------------------------------------------------
-- Server version	5.7.24

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
-- Table structure for table `cron_job`
--

DROP TABLE IF EXISTS `cron_job`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cron_job` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `command` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `arguments` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `number` int(11) NOT NULL DEFAULT '1',
  `period` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `last_use` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `next_run` datetime NOT NULL COMMENT '(DC2Type:datetime)',
  `enable` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cron_job_result`
--

DROP TABLE IF EXISTS `cron_job_result`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cron_job_result` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cron_job_id` bigint(20) unsigned NOT NULL,
  `run_at` datetime NOT NULL COMMENT '(DC2Type:datetime)',
  `run_time` double NOT NULL,
  `status_code` int(11) NOT NULL,
  `output` longtext COLLATE utf8_unicode_ci,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime)',
  PRIMARY KEY (`id`),
  KEY `IDX_2CD346EE79099ED8` (`cron_job_id`),
  CONSTRAINT `FK_2CD346EE79099ED8` FOREIGN KEY (`cron_job_id`) REFERENCES `cron_job` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mb_email_email_data`
--

DROP TABLE IF EXISTS `mb_email_email_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mb_email_email_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `raw_email` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `body_text` longtext COLLATE utf8mb4_unicode_ci,
  `body_html` longtext COLLATE utf8mb4_unicode_ci,
  `header_from` longtext COLLATE utf8mb4_unicode_ci COMMENT '(DC2Type:simple_array)',
  `header_to` longtext COLLATE utf8mb4_unicode_ci COMMENT '(DC2Type:simple_array)',
  `header_cc` longtext COLLATE utf8mb4_unicode_ci COMMENT '(DC2Type:simple_array)',
  `header_bcc` longtext COLLATE utf8mb4_unicode_ci COMMENT '(DC2Type:simple_array)',
  `header_reply_to` longtext COLLATE utf8mb4_unicode_ci COMMENT '(DC2Type:simple_array)',
  `header_return_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `created_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mb_email_email_log`
--

DROP TABLE IF EXISTS `mb_email_email_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mb_email_email_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email_data_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `recipient_hashes` longtext COLLATE utf8mb4_unicode_ci COMMENT '(DC2Type:simple_array)',
  `sent_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `error_message` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fail_count` smallint(6) NOT NULL,
  `modified_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `created_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `anonymized_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_4C32742E8A35C9F3` (`email_data_id`),
  KEY `IDX_4C32742EDE12AB56` (`created_by`),
  KEY `IDX_4C32742E25F94802` (`modified_by`),
  CONSTRAINT `FK_4C32742E25F94802` FOREIGN KEY (`modified_by`) REFERENCES `mb_user_user` (`id`) ON DELETE SET NULL,
  CONSTRAINT `FK_4C32742E8A35C9F3` FOREIGN KEY (`email_data_id`) REFERENCES `mb_email_email_data` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_4C32742EDE12AB56` FOREIGN KEY (`created_by`) REFERENCES `mb_user_user` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mb_email_email_template`
--

DROP TABLE IF EXISTS `mb_email_email_template`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mb_email_email_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `use_default_values` tinyint(1) NOT NULL,
  `render_twig` tinyint(1) NOT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `html` longtext COLLATE utf8mb4_unicode_ci,
  `html_template_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `text` longtext COLLATE utf8mb4_unicode_ci,
  `text_template_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `header_from` longtext COLLATE utf8mb4_unicode_ci COMMENT '(DC2Type:simple_array)',
  `header_to` longtext COLLATE utf8mb4_unicode_ci COMMENT '(DC2Type:simple_array)',
  `header_cc` longtext COLLATE utf8mb4_unicode_ci COMMENT '(DC2Type:simple_array)',
  `header_bcc` longtext COLLATE utf8mb4_unicode_ci COMMENT '(DC2Type:simple_array)',
  `header_reply_to` longtext COLLATE utf8mb4_unicode_ci COMMENT '(DC2Type:simple_array)',
  `header_return_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `created_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `deleted_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `id_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_3AA416B4143443F3` (`id_key`),
  KEY `mb_email_email_template_key_idx` (`id_key`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mb_log_sys_log`
--

DROP TABLE IF EXISTS `mb_log_sys_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mb_log_sys_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `channel` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `level` int(11) NOT NULL,
  `level_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `formatted` longtext COLLATE utf8mb4_unicode_ci,
  `context` longtext COLLATE utf8mb4_unicode_ci,
  `modified_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `created_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mb_setting_setting`
--

DROP TABLE IF EXISTS `mb_setting_setting`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mb_setting_setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `default_value` tinyint(1) NOT NULL,
  `value` longtext COLLATE utf8mb4_unicode_ci,
  `modified_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `created_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `id_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_82E4ABF6143443F3` (`id_key`),
  KEY `mb_setting_setting_key_idx` (`id_key`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mb_user_group`
--

DROP TABLE IF EXISTS `mb_user_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mb_user_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(180) COLLATE utf8_unicode_ci NOT NULL,
  `roles` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT '(DC2Type:array)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_50CF03C5E237E06` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mb_user_user`
--

DROP TABLE IF EXISTS `mb_user_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mb_user_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(180) COLLATE utf8_unicode_ci NOT NULL,
  `username_canonical` varchar(180) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(180) COLLATE utf8_unicode_ci NOT NULL,
  `email_canonical` varchar(180) COLLATE utf8_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `salt` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `confirmation_token` varchar(180) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password_requested_at` datetime DEFAULT NULL,
  `roles` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT '(DC2Type:array)',
  `updated_at` datetime NOT NULL,
  `created_at` datetime NOT NULL,
  `gender` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  `facebook_uid` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `firstname` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lastname` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_of_birth` datetime DEFAULT NULL,
  `website` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `biography` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `locale` varchar(8) COLLATE utf8_unicode_ci DEFAULT NULL,
  `timezone` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `facebook_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `facebook_data` json DEFAULT NULL,
  `twitter_uid` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `twitter_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `twitter_data` json DEFAULT NULL,
  `gplus_uid` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gplus_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gplus_data` json DEFAULT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `two_step_code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_9C94D79B92FC23A8` (`username_canonical`),
  UNIQUE KEY `UNIQ_9C94D79BA0D96FBF` (`email_canonical`),
  UNIQUE KEY `UNIQ_9C94D79BC05FB297` (`confirmation_token`)
) ENGINE=InnoDB AUTO_INCREMENT=103 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mb_user_user_group_mm`
--

DROP TABLE IF EXISTS `mb_user_user_group_mm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mb_user_user_group_mm` (
  `user_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`group_id`),
  KEY `IDX_F81FC676A76ED395` (`user_id`),
  KEY `IDX_F81FC676FE54D947` (`group_id`),
  CONSTRAINT `FK_F81FC676A76ED395` FOREIGN KEY (`user_id`) REFERENCES `mb_user_user` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_F81FC676FE54D947` FOREIGN KEY (`group_id`) REFERENCES `mb_user_group` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `migration_versions`
--

DROP TABLE IF EXISTS `migration_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migration_versions` (
  `version` varchar(14) COLLATE utf8_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ozg_collaboration`
--

DROP TABLE IF EXISTS `ozg_collaboration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ozg_collaboration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `modified_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `created_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hidden` tinyint(1) NOT NULL,
  `deleted_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ozg_commune`
--

DROP TABLE IF EXISTS `ozg_commune`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ozg_commune` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `street` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zip_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `town` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact` longtext COLLATE utf8mb4_unicode_ci,
  `modified_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `created_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hidden` tinyint(1) NOT NULL,
  `deleted_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `coordinators` longtext COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=398 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ozg_commune_service`
--

DROP TABLE IF EXISTS `ozg_commune_service`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ozg_commune_service` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `commune_id` int(11) DEFAULT NULL,
  `service_id` int(11) DEFAULT NULL,
  `maturity_id` int(11) DEFAULT NULL,
  `service_provider_id` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `created_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `deleted_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `description` longtext COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `IDX_EC42799D131A4F72` (`commune_id`),
  KEY `IDX_EC42799DED5CA9E6` (`service_id`),
  KEY `IDX_EC42799D5074221B` (`maturity_id`),
  KEY `IDX_EC42799DC6C98E06` (`service_provider_id`),
  CONSTRAINT `FK_EC42799D131A4F72` FOREIGN KEY (`commune_id`) REFERENCES `ozg_commune` (`id`),
  CONSTRAINT `FK_EC42799D5074221B` FOREIGN KEY (`maturity_id`) REFERENCES `ozg_maturity` (`id`),
  CONSTRAINT `FK_EC42799DC6C98E06` FOREIGN KEY (`service_provider_id`) REFERENCES `ozg_service_provider` (`id`),
  CONSTRAINT `FK_EC42799DED5CA9E6` FOREIGN KEY (`service_id`) REFERENCES `ozg_service` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ozg_communes_service_provider`
--

DROP TABLE IF EXISTS `ozg_communes_service_provider`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ozg_communes_service_provider` (
  `commune_id` int(11) NOT NULL,
  `service_provider_id` int(11) NOT NULL,
  PRIMARY KEY (`commune_id`,`service_provider_id`),
  KEY `IDX_512C785C131A4F72` (`commune_id`),
  KEY `IDX_512C785CC6C98E06` (`service_provider_id`),
  CONSTRAINT `FK_512C785C131A4F72` FOREIGN KEY (`commune_id`) REFERENCES `ozg_commune` (`id`),
  CONSTRAINT `FK_512C785CC6C98E06` FOREIGN KEY (`service_provider_id`) REFERENCES `ozg_service_provider` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ozg_confidence_level`
--

DROP TABLE IF EXISTS `ozg_confidence_level`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ozg_confidence_level` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `availability` int(11) NOT NULL,
  `availability_comment` longtext COLLATE utf8mb4_unicode_ci,
  `modified_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `created_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hidden` tinyint(1) NOT NULL,
  `deleted_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ozg_implementation`
--

DROP TABLE IF EXISTS `ozg_implementation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ozg_implementation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_final` tinyint(1) NOT NULL,
  `is_planned` tinyint(1) NOT NULL,
  `modified_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `created_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hidden` tinyint(1) NOT NULL,
  `deleted_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ozg_import`
--

DROP TABLE IF EXISTS `ozg_import`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ozg_import` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `situation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `service_system` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `service_system_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `service` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `service_key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `service_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `legal_basis` longtext COLLATE utf8mb4_unicode_ci,
  `laws` longtext COLLATE utf8mb4_unicode_ci,
  `law_shortcuts` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `relevance1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `relevance2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `created_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `subject_import_ud` int(11) DEFAULT NULL,
  `situation_import_ud` int(11) DEFAULT NULL,
  `service_system_import_ud` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5250 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ozg_internal_procedure`
--

DROP TABLE IF EXISTS `ozg_internal_procedure`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ozg_internal_procedure` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `provider` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `created_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hidden` tinyint(1) NOT NULL,
  `deleted_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ozg_laboratory`
--

DROP TABLE IF EXISTS `ozg_laboratory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ozg_laboratory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `modified_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `created_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hidden` tinyint(1) NOT NULL,
  `deleted_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `participants_other` longtext COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ozg_laboratory_service_provider`
--

DROP TABLE IF EXISTS `ozg_laboratory_service_provider`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ozg_laboratory_service_provider` (
  `laboratory_id` int(11) NOT NULL,
  `service_provider_id` int(11) NOT NULL,
  PRIMARY KEY (`laboratory_id`,`service_provider_id`),
  KEY `IDX_8A17B9B22F2A371E` (`laboratory_id`),
  KEY `IDX_8A17B9B2C6C98E06` (`service_provider_id`),
  CONSTRAINT `FK_8A17B9B22F2A371E` FOREIGN KEY (`laboratory_id`) REFERENCES `ozg_laboratory` (`id`),
  CONSTRAINT `FK_8A17B9B2C6C98E06` FOREIGN KEY (`service_provider_id`) REFERENCES `ozg_service_provider` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ozg_maturity`
--

DROP TABLE IF EXISTS `ozg_maturity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ozg_maturity` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `modified_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `created_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hidden` tinyint(1) NOT NULL,
  `deleted_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ozg_ministry_country`
--

DROP TABLE IF EXISTS `ozg_ministry_country`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ozg_ministry_country` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `modified_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `created_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hidden` tinyint(1) NOT NULL,
  `deleted_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ozg_ministry_state`
--

DROP TABLE IF EXISTS `ozg_ministry_state`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ozg_ministry_state` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `modified_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `created_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hidden` tinyint(1) NOT NULL,
  `deleted_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ozg_office`
--

DROP TABLE IF EXISTS `ozg_office`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ozg_office` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `commune_id` int(11) DEFAULT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `modified_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `created_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hidden` tinyint(1) NOT NULL,
  `deleted_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact` longtext COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `IDX_6CCB849D131A4F72` (`commune_id`),
  CONSTRAINT `FK_6CCB849D131A4F72` FOREIGN KEY (`commune_id`) REFERENCES `ozg_commune` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ozg_portal`
--

DROP TABLE IF EXISTS `ozg_portal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ozg_portal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service_provider_id` int(11) DEFAULT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `modified_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `created_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hidden` tinyint(1) NOT NULL,
  `deleted_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  PRIMARY KEY (`id`),
  KEY `IDX_13347C6FC6C98E06` (`service_provider_id`),
  CONSTRAINT `FK_13347C6FC6C98E06` FOREIGN KEY (`service_provider_id`) REFERENCES `ozg_service_provider` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ozg_priority`
--

DROP TABLE IF EXISTS `ozg_priority`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ozg_priority` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `level` int(11) NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `modified_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `created_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hidden` tinyint(1) NOT NULL,
  `deleted_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ozg_service`
--

DROP TABLE IF EXISTS `ozg_service`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ozg_service` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `modified_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `created_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `name` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hidden` tinyint(1) NOT NULL,
  `deleted_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `service_system_id` int(11) DEFAULT NULL,
  `service_key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `service_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `legal_basis` longtext COLLATE utf8mb4_unicode_ci,
  `laws` longtext COLLATE utf8mb4_unicode_ci,
  `law_shortcuts` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `relevance1` tinyint(1) NOT NULL,
  `relevance2` tinyint(1) NOT NULL,
  `status_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_81358EE8880415EF` (`service_system_id`),
  KEY `IDX_81358EE86BF700BD` (`status_id`),
  CONSTRAINT `FK_81358EE86BF700BD` FOREIGN KEY (`status_id`) REFERENCES `ozg_status` (`id`) ON DELETE SET NULL,
  CONSTRAINT `FK_81358EE8880415EF` FOREIGN KEY (`service_system_id`) REFERENCES `ozg_service_system` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8192 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ozg_service_provider`
--

DROP TABLE IF EXISTS `ozg_service_provider`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ozg_service_provider` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `street_nr` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `street` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zip_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `town` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact` longtext COLLATE utf8mb4_unicode_ci,
  `modified_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `created_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hidden` tinyint(1) NOT NULL,
  `deleted_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ozg_service_solution`
--

DROP TABLE IF EXISTS `ozg_service_solution`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ozg_service_solution` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service_id` int(11) DEFAULT NULL,
  `solution_id` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `created_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `hidden` tinyint(1) NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `status_id` int(11) DEFAULT NULL,
  `maturity_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_591B9528ED5CA9E6` (`service_id`),
  KEY `IDX_591B95281C0BE183` (`solution_id`),
  KEY `IDX_591B95286BF700BD` (`status_id`),
  KEY `IDX_591B95285074221B` (`maturity_id`),
  CONSTRAINT `FK_591B95281C0BE183` FOREIGN KEY (`solution_id`) REFERENCES `ozg_solution` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_591B95285074221B` FOREIGN KEY (`maturity_id`) REFERENCES `ozg_maturity` (`id`) ON DELETE SET NULL,
  CONSTRAINT `FK_591B95286BF700BD` FOREIGN KEY (`status_id`) REFERENCES `ozg_status` (`id`) ON DELETE SET NULL,
  CONSTRAINT `FK_591B9528ED5CA9E6` FOREIGN KEY (`service_id`) REFERENCES `ozg_service` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ozg_service_system`
--

DROP TABLE IF EXISTS `ozg_service_system`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ozg_service_system` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `situation_id` int(11) DEFAULT NULL,
  `priority_id` int(11) DEFAULT NULL,
  `service_key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `execution` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact` longtext COLLATE utf8mb4_unicode_ci,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `modified_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `created_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hidden` tinyint(1) NOT NULL,
  `deleted_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `status_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_18AF8D783408E8AF` (`situation_id`),
  KEY `IDX_18AF8D78497B19F9` (`priority_id`),
  KEY `IDX_18AF8D786BF700BD` (`status_id`),
  CONSTRAINT `FK_18AF8D783408E8AF` FOREIGN KEY (`situation_id`) REFERENCES `ozg_situation` (`id`),
  CONSTRAINT `FK_18AF8D78497B19F9` FOREIGN KEY (`priority_id`) REFERENCES `ozg_priority` (`id`) ON DELETE SET NULL,
  CONSTRAINT `FK_18AF8D786BF700BD` FOREIGN KEY (`status_id`) REFERENCES `ozg_status` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=1024 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ozg_situation`
--

DROP TABLE IF EXISTS `ozg_situation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ozg_situation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject_id` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `created_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hidden` tinyint(1) NOT NULL,
  `deleted_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  PRIMARY KEY (`id`),
  KEY `IDX_D35EF06623EDC87` (`subject_id`),
  CONSTRAINT `FK_D35EF06623EDC87` FOREIGN KEY (`subject_id`) REFERENCES `ozg_subject` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ozg_solution`
--

DROP TABLE IF EXISTS `ozg_solution`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ozg_solution` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service_provider_id` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `created_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hidden` tinyint(1) NOT NULL,
  `deleted_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `platform_technology` longtext COLLATE utf8mb4_unicode_ci,
  `status_id` int(11) DEFAULT NULL,
  `portal_id` int(11) DEFAULT NULL,
  `custom_provider` longtext COLLATE utf8mb4_unicode_ci,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact` longtext COLLATE utf8mb4_unicode_ci,
  `licensing` longtext COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `IDX_595F587DC6C98E06` (`service_provider_id`),
  KEY `IDX_595F587D6BF700BD` (`status_id`),
  KEY `IDX_595F587DB887E1DD` (`portal_id`),
  CONSTRAINT `FK_595F587D6BF700BD` FOREIGN KEY (`status_id`) REFERENCES `ozg_status` (`id`) ON DELETE SET NULL,
  CONSTRAINT `FK_595F587DB887E1DD` FOREIGN KEY (`portal_id`) REFERENCES `ozg_portal` (`id`) ON DELETE SET NULL,
  CONSTRAINT `FK_595F587DC6C98E06` FOREIGN KEY (`service_provider_id`) REFERENCES `ozg_service_provider` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ozg_solutions_specialized_procedures`
--

DROP TABLE IF EXISTS `ozg_solutions_specialized_procedures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ozg_solutions_specialized_procedures` (
  `solution_id` int(11) NOT NULL,
  `specialized_procedure_id` int(11) NOT NULL,
  PRIMARY KEY (`solution_id`,`specialized_procedure_id`),
  KEY `IDX_E4A320051C0BE183` (`solution_id`),
  KEY `IDX_E4A32005452D2882` (`specialized_procedure_id`),
  CONSTRAINT `FK_E4A320051C0BE183` FOREIGN KEY (`solution_id`) REFERENCES `ozg_solution` (`id`),
  CONSTRAINT `FK_E4A32005452D2882` FOREIGN KEY (`specialized_procedure_id`) REFERENCES `ozg_specialized_procedure` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ozg_specialized_procedure`
--

DROP TABLE IF EXISTS `ozg_specialized_procedure`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ozg_specialized_procedure` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `modified_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `created_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hidden` tinyint(1) NOT NULL,
  `deleted_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ozg_state`
--

DROP TABLE IF EXISTS `ozg_state`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ozg_state` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `created_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hidden` tinyint(1) NOT NULL,
  `deleted_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ozg_status`
--

DROP TABLE IF EXISTS `ozg_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ozg_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `level` int(11) NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `modified_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `created_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hidden` tinyint(1) NOT NULL,
  `deleted_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ozg_subject`
--

DROP TABLE IF EXISTS `ozg_subject`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ozg_subject` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `modified_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `created_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hidden` tinyint(1) NOT NULL,
  `deleted_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-10-05 18:02:26
