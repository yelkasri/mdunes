CREATE TABLE IF NOT EXISTS `#__tour_node` (
 `trid` int(10) unsigned NOT NULL AUTO_INCREMENT,
 `namekey` varchar(150) NOT NULL,
 `alias` varchar(150) NOT NULL,
 `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
 `rolid` smallint(5) unsigned NOT NULL DEFAULT '1',
 `uid` int(10) unsigned NOT NULL DEFAULT '0',
 `modifiedby` int(10) unsigned NOT NULL DEFAULT '0',
 `core` tinyint(4) NOT NULL DEFAULT '0',
 `publish` tinyint(4) NOT NULL DEFAULT '0',
 `created` int(10) unsigned NOT NULL DEFAULT '0',
 `modified` int(10) unsigned NOT NULL DEFAULT '0',
 `wid` mediumint(8) unsigned NOT NULL DEFAULT '0',
 `isadmin` tinyint(3) unsigned NOT NULL DEFAULT '0',
 PRIMARY KEY (`trid`),
 UNIQUE KEY `UK_tour_node_namekey` (`namekey`),
 KEY `IX_tour_node_rolid_isadmin_publish_core` (`rolid`,`isadmin`,`publish`,`core`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;