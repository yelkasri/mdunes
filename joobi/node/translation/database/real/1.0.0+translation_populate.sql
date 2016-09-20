CREATE TABLE IF NOT EXISTS `#__translation_populate` (
 `dbcid` int(10) unsigned NOT NULL,
 `eid` int(10) unsigned NOT NULL,
 `imac` varchar(50) NOT NULL,
 `wid` mediumint(8) unsigned NOT NULL DEFAULT '0',
 PRIMARY KEY (`dbcid`,`eid`),
 KEY `IK_translation_populate_wid` (`wid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;