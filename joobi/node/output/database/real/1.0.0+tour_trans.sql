CREATE TABLE IF NOT EXISTS `#__tour_trans` (
 `trid` int(10) unsigned NOT NULL DEFAULT '0',
 `lgid` int(10) unsigned NOT NULL DEFAULT '0',
 `name` varchar(255) NOT NULL,
 `description` text NOT NULL,
 `auto` tinyint(255) NOT NULL DEFAULT '2',
 `fromlgid` tinyint(3) unsigned NOT NULL DEFAULT '0',
 PRIMARY KEY (`trid`,`lgid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;