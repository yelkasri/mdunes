CREATE TABLE IF NOT EXISTS `#__layout_dropset` (
 `did` mediumint(8) unsigned NOT NULL,
 `yid` mediumint(8) unsigned NOT NULL,
 `ordering` tinyint(3) unsigned NOT NULL DEFAULT '0',
 PRIMARY KEY (`did`,`yid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;