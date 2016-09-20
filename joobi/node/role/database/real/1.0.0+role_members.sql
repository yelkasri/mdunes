CREATE TABLE IF NOT EXISTS `#__role_members` (
 `rolid` int(10) unsigned NOT NULL,
 `uid` int(10) unsigned NOT NULL,
 PRIMARY KEY (`uid`,`rolid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;