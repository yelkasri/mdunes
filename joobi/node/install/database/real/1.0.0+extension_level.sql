CREATE TABLE IF NOT EXISTS `#__extension_level` (
 `lwid` int(10) unsigned NOT NULL AUTO_INCREMENT,
 `wid` mediumint(8) unsigned NOT NULL DEFAULT '0',
 `level` tinyint(3) unsigned NOT NULL DEFAULT '0',
 `status` tinyint(4) NOT NULL DEFAULT '0',
 `namekey` varchar(50) NOT NULL,
 PRIMARY KEY (`lwid`),
 UNIQUE KEY `UK_extension_level_wid_level` (`wid`,`level`),
 UNIQUE KEY `NamekeyExtensionLevel` (`namekey`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;