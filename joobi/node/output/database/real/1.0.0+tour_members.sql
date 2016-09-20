CREATE TABLE IF NOT EXISTS `#__tour_members` (
 `trid` int(10) unsigned NOT NULL DEFAULT '0',
 `uid` int(10) unsigned NOT NULL DEFAULT '0',
 `status` tinyint(3) unsigned NOT NULL DEFAULT '0',
 `step` smallint(5) unsigned NOT NULL DEFAULT '1',
 `yid` mediumint(8) unsigned NOT NULL DEFAULT '0',
 PRIMARY KEY (`trid`,`yid`,`uid`),
 KEY `IX_tour_members_trid_uid` (`trid`,`uid`),
 KEY `IX_tour_members_yid_uid` (`yid`,`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;