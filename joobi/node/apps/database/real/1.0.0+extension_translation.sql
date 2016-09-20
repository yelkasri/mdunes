CREATE TABLE IF NOT EXISTS `#__extension_translation` (
 `wid` int(10) unsigned NOT NULL,
 `lgid` int(10) unsigned NOT NULL,
 `modifiedby` int(10) unsigned NOT NULL DEFAULT '0',
 `modified` int(10) unsigned NOT NULL DEFAULT '0',
 PRIMARY KEY (`wid`,`lgid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;