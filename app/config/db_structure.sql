DROP TABLE IF EXISTS `{PREFIX10}_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `{PREFIX10}_role`
(
    `id`   int                           NOT NULL AUTO_INCREMENT,
    `name` varchar(255) COLLATE utf8_bin NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `{PREFIX11}_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `{PREFIX11}_user`
(
    `id`         int                                              NOT NULL AUTO_INCREMENT,
    `username`   varchar(45) CHARACTER SET utf8 COLLATE utf8_bin  NOT NULL,
    `password`   varchar(255) CHARACTER SET utf8 COLLATE utf8_bin          DEFAULT NULL,
    `email`      varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
    `role`       int                                                       DEFAULT NULL,
    `status`     tinyint                                          NOT NULL DEFAULT '1',
    `created_at` timestamp                                        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp                                        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY          `{PREFIX11}_user_{PREFIX10}_role_id_fk` (`role`),
    CONSTRAINT `{PREFIX11}_user_{PREFIX10}_role_id_fk` FOREIGN KEY (`role`) REFERENCES `{PREFIX10}_role` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `{PREFIX12}_permission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `{PREFIX12}_permission`
(
    `id`          int                           NOT NULL AUTO_INCREMENT,
    `name`        varchar(255) COLLATE utf8_bin NOT NULL,
    `description` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `{PREFIX13}_role_permission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `{PREFIX13}_role_permission`
(
    `id`            int NOT NULL AUTO_INCREMENT,
    `role_id`       int DEFAULT NULL,
    `permission_id` int DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY             `{PREFIX13}_role_permission_{PREFIX10}_role_id_fk` (`role_id`),
    KEY             `{PREFIX13}_role_permission_{PREFIX12}_permission_id_fk` (`permission_id`),
    CONSTRAINT `{PREFIX13}_role_permission_{PREFIX10}_role_id_fk` FOREIGN KEY (`role_id`) REFERENCES `{PREFIX10}_role` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `{PREFIX13}_role_permission_{PREFIX12}_permission_id_fk` FOREIGN KEY (`permission_id`) REFERENCES `{PREFIX12}_permission` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `{PREFIX14}_validation_token`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `{PREFIX14}_validation_token`
(
    `id`         int                                          NOT NULL AUTO_INCREMENT,
    `user_id`    int                                          NOT NULL,
    `type`       tinyint                                      NOT NULL,
    `reference`  char(16) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
    `token`      char(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
    `created_at` timestamp                                    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `expires_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY          `{PREFIX14}_validation_token_{PREFIX11}_user_id_fk` (`user_id`),
    CONSTRAINT `{PREFIX14}_validation_token_{PREFIX11}_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `{PREFIX11}_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `{PREFIX15}_post`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `{PREFIX15}_post`
(
    `id`           int       NOT NULL AUTO_INCREMENT,
    `author_id`    int                           DEFAULT NULL,
    `title`        varchar(255) COLLATE utf8_bin DEFAULT NULL,
    `content`      longtext CHARACTER SET utf8 COLLATE utf8_bin,
    `type`         tinyint   NOT NULL,
    `status`       tinyint   NOT NULL,
    `published_at` timestamp NULL DEFAULT NULL,
    `updated_at`   timestamp NOT NULL            DEFAULT CURRENT_TIMESTAMP,
    `created_at`   timestamp NOT NULL            DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY            `{PREFIX15}_post_{PREFIX11}_user_id_fk` (`author_id`),
    CONSTRAINT `{PREFIX15}_post_{PREFIX11}_user_id_fk` FOREIGN KEY (`author_id`) REFERENCES `{PREFIX11}_user` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `{PREFIX16}_page_extra`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `{PREFIX16}_page_extra`
(
    `id`               int NOT NULL AUTO_INCREMENT,
    `post_id`          int                                              DEFAULT NULL,
    `slug`             varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
    `visibility`       tinyint                                          DEFAULT '0',
    `meta_title`       varchar(100) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
    `meta_description` text CHARACTER SET utf8 COLLATE utf8_bin,
    `meta_indexable`   tinyint                                          DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY                `{PREFIX16}_page_extra_{PREFIX15}_post_id_fk` (`post_id`),
    CONSTRAINT `{PREFIX16}_page_extra_{PREFIX15}_post_id_fk` FOREIGN KEY (`post_id`) REFERENCES `{PREFIX15}_post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `{PREFIX17}_menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `{PREFIX17}_menu`
(
    `id`     int                           NOT NULL AUTO_INCREMENT,
    `title`  varchar(255) COLLATE utf8_bin NOT NULL,
    `type`   tinyint                       NOT NULL,
    `status` tinyint                       NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `{PREFIX18}_menu_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `{PREFIX18}_menu_item`
(
    `id`      int                           NOT NULL AUTO_INCREMENT,
    `menu_id` int                           NOT NULL,
    `post_id` int                                               DEFAULT NULL,
    `label`   varchar(255) COLLATE utf8_bin NOT NULL,
    `icon`    varchar(30) CHARACTER SET utf8 COLLATE utf8_bin   DEFAULT NULL,
    `url`     varchar(2000) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY       `{PREFIX18}_menu_item_{PREFIX15}_post_id_fk` (`post_id`),
    KEY       `{PREFIX18}_menu_item_{PREFIX17}_menu_id_fk` (`menu_id`),
    CONSTRAINT `{PREFIX18}_menu_item_{PREFIX15}_post_id_fk` FOREIGN KEY (`post_id`) REFERENCES `{PREFIX15}_post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `{PREFIX18}_menu_item_{PREFIX17}_menu_id_fk` FOREIGN KEY (`menu_id`) REFERENCES `{PREFIX17}_menu` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `{PREFIX19}_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `{PREFIX19}_settings`
(
    `id`    int                                             NOT NULL AUTO_INCREMENT,
    `name`  varchar(30) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
    `value` longtext CHARACTER SET utf8 COLLATE utf8_bin,
    PRIMARY KEY (`id`),
    UNIQUE KEY `{PREFIX19}_settings_name_uindex` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `{PREFIX20}_visitor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `{PREFIX20}_visitor`
(
    `id`    int                                              NOT NULL AUTO_INCREMENT,
    `ip`    varchar(15) CHARACTER SET utf8 COLLATE utf8_bin  NOT NULL,
    `agent` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
    `uri`   varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
    `date`  timestamp                                        NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `{PREFIX21}_subscriber`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `{PREFIX21}_subscriber`
(
    `id`     int                                              NOT NULL AUTO_INCREMENT,
    `email`  varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
    `status` tinyint                                          NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `{PREFIX22}_review`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `{PREFIX22}_review`
(
    `id`     int                                              NOT NULL AUTO_INCREMENT,
    `rate`   int                                              NOT NULL,
    `author` varchar(255) COLLATE utf8_bin                    NOT NULL,
    `email`  varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
    `review` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
    `status` tinyint                                          NOT NULL,
    `date`   timestamp                                        NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;
