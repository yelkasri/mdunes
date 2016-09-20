CREATE TABLE IF NOT EXISTS `#__dropset_valuestrans` (
 `vid` int(10) unsigned NOT NULL,
 `name` varchar(255) NOT NULL,
 `lgid` tinyint(4) unsigned NOT NULL,
 `auto` tinyint(4) NOT NULL DEFAULT '1',
 `fromlgid` tinyint(3) unsigned NOT NULL DEFAULT '0',
 PRIMARY KEY (`vid`,`lgid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;