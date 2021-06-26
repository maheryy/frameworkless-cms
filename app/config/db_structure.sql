DROP TABLE IF EXISTS `{PREFIX1}_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `{PREFIX1}_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(45) COLLATE utf8_bin NOT NULL,
  `password` varchar(255) COLLATE utf8_bin NOT NULL,
  `email` varchar(65) COLLATE utf8_bin NOT NULL,
  `role` tinyint(4) NOT NULL DEFAULT '1',
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `{PREFIX1}_user_username_uindex` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `{PREFIX2}_post`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `{PREFIX2}_post` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `author_id` int(11) DEFAULT NULL,
  `title` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `content` longtext COLLATE utf8_bin,
  `type` tinyint(4) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `published_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `{PREFIX2}_post_{PREFIX1}_user_id_fk` (`author_id`),
  CONSTRAINT `{PREFIX2}_post_{PREFIX1}_user_id_fk` FOREIGN KEY (`author_id`) REFERENCES `{PREFIX1}_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `{PREFIX3}_validation_token`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `{PREFIX3}_validation_token` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `reference` char(16) COLLATE utf8_bin NOT NULL,
  `token` char(64) COLLATE utf8_bin NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `{PREFIX3}_validation_token_{PREFIX1}_user_id_fk` (`user_id`),
  CONSTRAINT `{PREFIX3}_validation_token_{PREFIX1}_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `{PREFIX1}_user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `{PREFIX4}_page_extra`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `{PREFIX4}_page_extra` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) DEFAULT NULL,
  `slug` varchar(60) COLLATE utf8_bin DEFAULT NULL,
  `visibility` tinyint(4) DEFAULT '0',
  `allow_comments` tinyint(4) DEFAULT '0',
  `meta_title` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `meta_description` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `meta_indexable` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `{PREFIX4}_page_extra_{PREFIX2}_post_id_fk` (`post_id`),
  CONSTRAINT `{PREFIX4}_page_extra_{PREFIX2}_post_id_fk` FOREIGN KEY (`post_id`) REFERENCES `{PREFIX2}_post` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;
