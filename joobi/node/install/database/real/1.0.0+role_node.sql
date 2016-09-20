CREATE TABLE IF NOT EXISTS `#__role_node` (
 `rolid` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
 `parent` smallint(5) unsigned NOT NULL DEFAULT '0',
 `lft` smallint(5) unsigned NOT NULL DEFAULT '0',
 `rgt` smallint(5) unsigned NOT NULL DEFAULT '0',
 `core` tinyint(3) unsigned NOT NULL DEFAULT '0',
 `joomla` smallint(5) unsigned NOT NULL DEFAULT '0',
 `j16` int(10) unsigned NOT NULL DEFAULT '1',
 `namekey` varchar(50) NOT NULL DEFAULT '',
 `color` varchar(20) NOT NULL DEFAULT 'black',
 `type` tinyint(3) unsigned NOT NULL DEFAULT '1',
 `depth` smallint(5) unsigned NOT NULL DEFAULT '0',
 PRIMARY KEY (`rolid`),
 UNIQUE KEY `UK_role_node_namekey` (`namekey`),
 KEY `IX_role_node_lft` (`lft`),
 KEY `IX_role_node_rgt` (`rgt`),
 KEY `IX_role_node_type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;