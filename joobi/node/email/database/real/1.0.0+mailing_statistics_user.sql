CREATE TABLE IF NOT EXISTS `#__mailing_statistics_user` (
 `mgid` int(10) unsigned NOT NULL,
 `uid` int(10) unsigned NOT NULL,
 `created` int(10) unsigned NOT NULL DEFAULT '0',
 `htmlsent` tinyint(3) unsigned NOT NULL DEFAULT '0',
 `textsent` tinyint(3) unsigned NOT NULL DEFAULT '0',
 `failed` tinyint(3) unsigned NOT NULL DEFAULT '0',
 `bounced` tinyint(3) unsigned NOT NULL DEFAULT '0',
 `smssent` tinyint(3) unsigned NOT NULL DEFAULT '0',
 `hitlinks` tinyint(3) unsigned NOT NULL DEFAULT '0',
 `read` tinyint(3) unsigned NOT NULL DEFAULT '0',
 `readdate` int(10) unsigned NOT NULL DEFAULT '0',
 `mailerid` smallint(5) unsigned NOT NULL DEFAULT '0',
 `sent` tinyint(3) unsigned NOT NULL DEFAULT '0',
 PRIMARY KEY (`mgid`,`uid`,`created`),
 KEY `IX_mailing_statistics_user_created` (`created`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;