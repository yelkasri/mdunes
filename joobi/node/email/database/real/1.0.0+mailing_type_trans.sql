CREATE TABLE IF NOT EXISTS `#__mailing_type_trans` (
 `mgtypeid` int(10) unsigned NOT NULL,
 `name` varchar(255) NOT NULL,
 `description` text NOT NULL,
 `auto` tinyint(4) NOT NULL DEFAULT '1',
 `fromlgid` tinyint(3) unsigned NOT NULL DEFAULT '0',
 `lgid` int(10) unsigned NOT NULL,
 PRIMARY KEY (`mgtypeid`,`lgid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;