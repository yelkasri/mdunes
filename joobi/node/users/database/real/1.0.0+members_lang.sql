CREATE TABLE IF NOT EXISTS `#__members_lang` (
 `uid` int(10) unsigned NOT NULL,
 `lgid` tinyint(3) unsigned NOT NULL DEFAULT '1',
 `ordering` tinyint(3) unsigned NOT NULL DEFAULT '0',
 PRIMARY KEY (`uid`,`lgid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;