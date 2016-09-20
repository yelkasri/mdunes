CREATE TABLE IF NOT EXISTS `#__extension_userinfos` (
 `wid` int(10) unsigned NOT NULL,
 `level` tinyint(3) unsigned NOT NULL DEFAULT '0',
 `enabled` tinyint(3) unsigned NOT NULL DEFAULT '0',
 `ltype` tinyint(3) unsigned NOT NULL DEFAULT '1',
 `license` varchar(255) NOT NULL,
 `token` varchar(254) NOT NULL,
 `expire` int(10) unsigned NOT NULL DEFAULT '0',
 `maintenance` int(10) unsigned NOT NULL DEFAULT '0',
 `flag` tinyint(3) unsigned NOT NULL DEFAULT '0',
 `subtype` tinyint(3) unsigned NOT NULL DEFAULT '0',
 PRIMARY KEY (`wid`,`level`),
 KEY `IX_extension_userinfos_enabled_license` (`enabled`,`license`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;