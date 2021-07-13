DROP TABLE IF EXISTS `{PREFIX1}_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `{PREFIX1}_role`
(
    `id`   int                                             NOT NULL AUTO_INCREMENT,
    `name` varchar(40) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `{PREFIX2}_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `{PREFIX2}_user`
(
    `id`         int                                              NOT NULL AUTO_INCREMENT,
    `username`   varchar(45) CHARACTER SET utf8 COLLATE utf8_bin  NOT NULL,
    `password`   varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
    `email`      varchar(65) CHARACTER SET utf8 COLLATE utf8_bin  NOT NULL,
    `role`       int                                                       DEFAULT NULL,
    `status`     tinyint                                          NOT NULL DEFAULT '1',
    `created_at` timestamp                                        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp                                        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `{PREFIX2}_user_username_uindex` (`username`),
    KEY          `{PREFIX2}_user_{PREFIX1}_role_id_fk` (`role`),
    CONSTRAINT `{PREFIX2}_user_{PREFIX1}_role_id_fk` FOREIGN KEY (`role`) REFERENCES `{PREFIX1}_role` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `{PREFIX3}_permission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `{PREFIX3}_permission`
(
    `id`          int                                             NOT NULL AUTO_INCREMENT,
    `name`        varchar(60) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
    `description` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `{PREFIX4}_role_permission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `{PREFIX4}_role_permission`
(
    `id`            int NOT NULL AUTO_INCREMENT,
    `role_id`       int DEFAULT NULL,
    `permission_id` int DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY             `{PREFIX4}_role_permission_{PREFIX1}_role_id_fk` (`role_id`),
    KEY             `{PREFIX4}_role_permission_{PREFIX3}_permission_id_fk` (`permission_id`),
    CONSTRAINT `{PREFIX4}_role_permission_{PREFIX1}_role_id_fk` FOREIGN KEY (`role_id`) REFERENCES `{PREFIX1}_role` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `{PREFIX4}_role_permission_{PREFIX3}_permission_id_fk` FOREIGN KEY (`permission_id`) REFERENCES `{PREFIX3}_permission` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `{PREFIX5}_validation_token`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `{PREFIX5}_validation_token`
(
    `id`         int                                          NOT NULL AUTO_INCREMENT,
    `user_id`    int                                          NOT NULL,
    `type`       tinyint                                      NOT NULL,
    `reference`  char(16) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
    `token`      char(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
    `created_at` timestamp                                    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `expires_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY          `{PREFIX5}_validation_token_{PREFIX2}_user_id_fk` (`user_id`),
    CONSTRAINT `{PREFIX5}_validation_token_{PREFIX2}_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `{PREFIX2}_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `{PREFIX6}_post`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `{PREFIX6}_post`
(
    `id`           int       NOT NULL AUTO_INCREMENT,
    `author_id`    int                                             DEFAULT NULL,
    `title`        varchar(45) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
    `content`      longtext CHARACTER SET utf8 COLLATE utf8_bin,
    `type`         tinyint   NOT NULL,
    `status`       tinyint   NOT NULL,
    `published_at` timestamp NULL DEFAULT NULL,
    `updated_at`   timestamp NOT NULL                              DEFAULT CURRENT_TIMESTAMP,
    `created_at`   timestamp NOT NULL                              DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY            `{PREFIX6}_post_{PREFIX2}_user_id_fk` (`author_id`),
    CONSTRAINT `{PREFIX6}_post_{PREFIX2}_user_id_fk` FOREIGN KEY (`author_id`) REFERENCES `{PREFIX2}_user` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `{PREFIX7}_page_extra`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `{PREFIX7}_page_extra`
(
    `id`               int NOT NULL AUTO_INCREMENT,
    `post_id`          int                                              DEFAULT NULL,
    `slug`             varchar(60) CHARACTER SET utf8 COLLATE utf8_bin  DEFAULT NULL,
    `visibility`       tinyint                                          DEFAULT '0',
    `allow_comments`   tinyint                                          DEFAULT '0',
    `meta_title`       varchar(100) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
    `meta_description` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
    `meta_indexable`   tinyint                                          DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY                `{PREFIX7}_page_extra_{PREFIX6}_post_id_fk` (`post_id`),
    CONSTRAINT `{PREFIX7}_page_extra_{PREFIX6}_post_id_fk` FOREIGN KEY (`post_id`) REFERENCES `{PREFIX6}_post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `{PREFIX8}_navigation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `{PREFIX8}_navigation`
(
    `id`     int                          NOT NULL AUTO_INCREMENT,
    `title`  varchar(60) COLLATE utf8_bin NOT NULL,
    `type`   tinyint                      NOT NULL,
    `status` tinyint                      NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `{PREFIX9}_navigation_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `{PREFIX9}_navigation_item`
(
    `id`            int                          NOT NULL AUTO_INCREMENT,
    `navigation_id` int DEFAULT NULL,
    `post_id`       int DEFAULT NULL,
    `label`         varchar(20) COLLATE utf8_bin NOT NULL,
    PRIMARY KEY (`id`),
    KEY             `{PREFIX9}_navigation_item_{PREFIX6}_post_id_fk` (`post_id`),
    KEY             `{PREFIX9}_navigation_item_{PREFIX8}_navigation_id_fk` (`navigation_id`),
    CONSTRAINT `{PREFIX9}_navigation_item_{PREFIX6}_post_id_fk` FOREIGN KEY (`post_id`) REFERENCES `{PREFIX6}_post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `{PREFIX9}_navigation_item_{PREFIX8}_navigation_id_fk` FOREIGN KEY (`navigation_id`) REFERENCES `{PREFIX8}_navigation` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;
