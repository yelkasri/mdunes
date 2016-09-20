CREATE TABLE IF NOT EXISTS `#__mailing_queue` (
 `qid` int(10) unsigned NOT NULL AUTO_INCREMENT,
 `mgid` mediumint(8) unsigned NOT NULL DEFAULT '0',
 `uid` int(10) unsigned NOT NULL DEFAULT '0',
 `senddate` int(10) unsigned NOT NULL DEFAULT '0',
 `priority` tinyint(3) unsigned NOT NULL DEFAULT '100',
 `attempt` tinyint(3) unsigned NOT NULL DEFAULT '0',
 `publish` tinyint(4) NOT NULL DEFAULT '1',
 `params` text NOT NULL,
 `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
 `cmpgnid` mediumint(8) unsigned NOT NULL DEFAULT '0',
 `actid` mediumint(8) unsigned NOT NULL DEFAULT '0',
 `status` tinyint(4) NOT NULL DEFAULT '0',
 `lsid` int(10) unsigned NOT NULL DEFAULT '0',
 PRIMARY KEY (`qid`),
 KEY `IX_mailing_queue_publish_priority_senddate` (`publish`,`priority`,`senddate`),
 KEY `IX_mailing_queue_mgid_cmpgnid_uid_lsid_publish` (`mgid`,`cmpgnid`,`uid`,`publish`,`lsid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;