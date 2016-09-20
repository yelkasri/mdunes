CREATE TABLE IF NOT EXISTS `#__extension_dependency` (
 `exdpid` int(10) unsigned NOT NULL AUTO_INCREMENT,
 `wid` mediumint(8) unsigned DEFAULT '0',
 `ref_wid` mediumint(8) unsigned DEFAULT '0',
 PRIMARY KEY (`exdpid`),
 UNIQUE KEY `UK_extension_dependency_wid_wid_ref` (`wid`,`ref_wid`),
 KEY `IX_extension_dependency_wid` (`wid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;