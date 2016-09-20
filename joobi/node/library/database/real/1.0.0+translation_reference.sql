CREATE TABLE IF NOT EXISTS `#__translation_reference` (
 `wid` mediumint(8) unsigned NOT NULL,
 `load` tinyint(4) NOT NULL DEFAULT '0',
 `imac` varchar(255) NOT NULL,
 PRIMARY KEY (`wid`,`imac`(20))
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;