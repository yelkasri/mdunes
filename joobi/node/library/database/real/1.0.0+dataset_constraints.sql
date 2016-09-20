CREATE TABLE IF NOT EXISTS `#__dataset_constraints` (
 `ctid` mediumint(5) unsigned NOT NULL AUTO_INCREMENT,
 `dbtid` smallint(5) unsigned NOT NULL DEFAULT '0',
 `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
 `namekey` varchar(255) NOT NULL,
 `modified` int(10) unsigned NOT NULL DEFAULT '0',
 PRIMARY KEY (`ctid`),
 UNIQUE KEY `UK_dataset_constraints_namekey` (`namekey`(100)),
 KEY `IX_dataset_constraints_dbtid_type` (`dbtid`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;