CREATE TABLE IF NOT EXISTS `#__dataset_constraintsitems` (
 `ctid` mediumint(5) unsigned NOT NULL,
 `ordering` tinyint(3) unsigned NOT NULL DEFAULT '5',
 `sort` tinyint(4) NOT NULL DEFAULT '0',
 `dbcid` smallint(5) unsigned NOT NULL,
 PRIMARY KEY (`dbcid`,`ctid`),
 KEY `IX_dataset_constraintsitems_ordering` (`ordering`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;