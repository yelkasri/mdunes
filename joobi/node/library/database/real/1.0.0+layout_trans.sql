CREATE TABLE IF NOT EXISTS `#__layout_trans` (
 `yid` mediumint(8) unsigned NOT NULL,
 `lgid` tinyint(3) unsigned NOT NULL,
 `name` varchar(255) NOT NULL,
 `description` text NOT NULL,
 `wname` varchar(255) NOT NULL,
 `wdescription` text NOT NULL,
 `auto` tinyint(4) NOT NULL DEFAULT '1',
 `fromlgid` tinyint(3) unsigned NOT NULL DEFAULT '0',
 `seotitle` varchar(255) NOT NULL,
 `seodescription` text NOT NULL,
 `seokeywords` text NOT NULL,
 PRIMARY KEY (`yid`,`lgid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;