CREATE TABLE IF NOT EXISTS `#__extension_info` (
 `wid` int(10) unsigned NOT NULL AUTO_INCREMENT,
 `author` varchar(100) NOT NULL,
 `documentation` varchar(255) NOT NULL,
 `images` varchar(255) NOT NULL,
 `flash` varchar(255) NOT NULL,
 `support` varchar(255) NOT NULL,
 `forum` varchar(255) NOT NULL,
 `homeurl` varchar(200) NOT NULL,
 `feedback` varchar(255) NOT NULL,
 `userversion` varchar(100) NOT NULL,
 `userlversion` varchar(100) NOT NULL,
 `beta` smallint(5) NOT NULL DEFAULT '0',
 `keyword` varchar(200) NOT NULL,
 PRIMARY KEY (`wid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;