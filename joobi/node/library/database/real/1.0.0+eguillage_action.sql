CREATE TABLE IF NOT EXISTS `#__eguillage_action` (
 `ctr_action_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
 `ctrid` mediumint(8) unsigned NOT NULL DEFAULT '0',
 `actid` mediumint(8) unsigned NOT NULL DEFAULT '0',
 `action` varchar(100) NOT NULL,
 `ordering` smallint(5) unsigned NOT NULL DEFAULT '0',
 `publish` tinyint(4) NOT NULL DEFAULT '1',
 `core` tinyint(4) NOT NULL DEFAULT '0',
 `params` text NOT NULL,
 `namekey` varchar(50) NOT NULL,
 PRIMARY KEY (`ctr_action_id`),
 KEY `IX_eguillage_action_ctrid_publish` (`ctrid`,`publish`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;