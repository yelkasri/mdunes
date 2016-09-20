CREATE TABLE IF NOT EXISTS `#__tour_step` (
 `trstid` int(10) unsigned NOT NULL AUTO_INCREMENT,
 `uid` int(10) unsigned NOT NULL DEFAULT '0',
 `core` tinyint(4) NOT NULL DEFAULT '0',
 `publish` tinyint(4) NOT NULL DEFAULT '0',
 `modified` int(10) unsigned NOT NULL DEFAULT '0',
 `trid` tinyint(3) unsigned NOT NULL DEFAULT '0',
 `ordering` smallint(5) unsigned NOT NULL DEFAULT '1',
 `onnext` varchar(250) NOT NULL,
 `namekey` varchar(70) NOT NULL,
 `target` varchar(150) NOT NULL,
 PRIMARY KEY (`trstid`),
 KEY `IX_tour_step_trid_publish` (`trid`,`publish`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;