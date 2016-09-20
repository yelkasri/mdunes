CREATE TABLE IF NOT EXISTS `#__mailing_statistics` (
 `mgid` int(10) unsigned NOT NULL AUTO_INCREMENT,
 `sent` int(10) unsigned NOT NULL DEFAULT '0',
 `failed` int(10) unsigned NOT NULL DEFAULT '0',
 `total` int(10) unsigned NOT NULL DEFAULT '0',
 `htmlsent` int(10) unsigned NOT NULL DEFAULT '0',
 `textsent` int(10) unsigned NOT NULL DEFAULT '0',
 `htmlread` int(10) unsigned NOT NULL DEFAULT '0',
 `textread` int(10) unsigned NOT NULL DEFAULT '0',
 `hitlinks` int(10) unsigned NOT NULL DEFAULT '0',
 `bounces` int(10) unsigned NOT NULL DEFAULT '0',
 `smssent` int(10) unsigned NOT NULL DEFAULT '0',
 `read` int(10) unsigned NOT NULL DEFAULT '0',
 PRIMARY KEY (`mgid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;