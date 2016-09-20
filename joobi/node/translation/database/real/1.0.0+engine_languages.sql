CREATE TABLE IF NOT EXISTS `#__engine_languages` (
 `englgid` int(10) unsigned NOT NULL AUTO_INCREMENT,
 `engid` tinyint(3) unsigned NOT NULL DEFAULT '0',
 `publish` tinyint(4) NOT NULL DEFAULT '1',
 `lgid` tinyint(3) unsigned NOT NULL DEFAULT '0',
 `ref_lgid` tinyint(3) unsigned NOT NULL DEFAULT '0',
 `ordering` tinyint(3) unsigned NOT NULL DEFAULT '0',
 PRIMARY KEY (`englgid`),
 UNIQUE KEY `UK_translation_engine_lgid_ref_lgid` (`engid`,`lgid`,`ref_lgid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;