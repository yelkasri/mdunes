CREATE TABLE IF NOT EXISTS `#__language_node` (
 `lgid` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
 `code` char(10) NOT NULL,
 `name` varchar(100) NOT NULL,
 `main` tinyint(3) unsigned NOT NULL DEFAULT '0',
 `publish` tinyint(4) NOT NULL DEFAULT '0',
 `real` varchar(100) NOT NULL,
 `premium` tinyint(3) unsigned NOT NULL DEFAULT '0',
 `rtl` tinyint(3) unsigned NOT NULL DEFAULT '0',
 `availsite` tinyint(3) unsigned NOT NULL DEFAULT '0',
 `availadmin` tinyint(3) unsigned NOT NULL DEFAULT '0',
 `localeconv` text NOT NULL,
 `locale` varchar(255) NOT NULL,
 `core` tinyint(4) NOT NULL DEFAULT '0',
 `automatic` tinyint(3) unsigned NOT NULL DEFAULT '0',
 PRIMARY KEY (`lgid`),
 UNIQUE KEY `UK_languages_code` (`code`),
 KEY `IX_languages_main_publish_availadmin_availsite` (`main`,`publish`,`availadmin`,`availsite`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;