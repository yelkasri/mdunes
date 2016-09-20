CREATE TABLE IF NOT EXISTS `#__sesion_node` (
 `sessid` varchar(250) NOT NULL,
 `uid` int(10) unsigned NOT NULL DEFAULT '0',
 `created` int(10) unsigned NOT NULL DEFAULT '0',
 `modified` int(10) unsigned NOT NULL DEFAULT '0',
 `ip` double unsigned NOT NULL DEFAULT '0',
 `data` longtext,
 `framework` tinyint(3) unsigned NOT NULL DEFAULT '0',
 PRIMARY KEY (`sessid`(64)),
 KEY `IX_sesion_node_uid` (`uid`),
 KEY `IX_sesion_node_modified` (`modified`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;