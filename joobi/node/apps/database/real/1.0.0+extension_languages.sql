CREATE TABLE IF NOT EXISTS `#__extension_languages` (
 `wid` mediumint(8) unsigned NOT NULL,
 `lgid` tinyint(3) unsigned NOT NULL,
 `available` tinyint(4) NOT NULL DEFAULT '0',
 `translation` tinyint(4) NOT NULL DEFAULT '1',
 `completed` double(5,2) unsigned NOT NULL DEFAULT '0.00',
 `automatic` double(5,2) unsigned NOT NULL DEFAULT '0.00',
 `totalimac` int(10) unsigned NOT NULL DEFAULT '0',
 `manual` double(5,2) unsigned NOT NULL DEFAULT '0.00',
 PRIMARY KEY (`wid`,`lgid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;