CREATE TABLE IF NOT EXISTS `#__extension_trans` (
 `lgid` tinyint(3) unsigned NOT NULL,
 `wid` mediumint(8) unsigned NOT NULL,
 `description` text NOT NULL,
 `auto` tinyint(4) NOT NULL DEFAULT '1',
 `fromlgid` tinyint(3) unsigned NOT NULL DEFAULT '0',
 PRIMARY KEY (`lgid`,`wid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;