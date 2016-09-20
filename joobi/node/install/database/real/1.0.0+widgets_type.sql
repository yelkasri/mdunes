CREATE TABLE IF NOT EXISTS `#__widgets_type` (
 `wgtypeid` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
 `namekey` varchar(100) NOT NULL,
 `alias` varchar(150) NOT NULL,
 `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
 `rolid` smallint(5) unsigned NOT NULL DEFAULT '1',
 `uid` int(10) unsigned NOT NULL DEFAULT '0',
 `core` tinyint(4) NOT NULL DEFAULT '0',
 `publish` tinyint(4) NOT NULL DEFAULT '0',
 `created` int(10) unsigned NOT NULL DEFAULT '0',
 `modified` int(10) unsigned NOT NULL DEFAULT '0',
 `groupid` tinyint(3) unsigned NOT NULL DEFAULT '0',
 PRIMARY KEY (`wgtypeid`),
 UNIQUE KEY `UK_widgets_type_namekey` (`namekey`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;