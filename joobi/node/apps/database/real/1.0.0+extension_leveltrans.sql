CREATE TABLE IF NOT EXISTS `#__extension_leveltrans` (
 `lwid` int(10) unsigned NOT NULL AUTO_INCREMENT,
 `lgid` tinyint(3) unsigned NOT NULL,
 `description` text NOT NULL,
 `auto` tinyint(4) NOT NULL DEFAULT '1',
 `fromlgid` tinyint(3) unsigned NOT NULL DEFAULT '0',
 PRIMARY KEY (`lwid`,`lgid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;