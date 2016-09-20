CREATE TABLE IF NOT EXISTS `#__translation_en` (
 `imac` varchar(20) NOT NULL,
 `text` text NOT NULL,
 `auto` tinyint(3) unsigned NOT NULL DEFAULT '1',
 `nbchars` int(10) unsigned NOT NULL DEFAULT '0',
 PRIMARY KEY (`imac`),
 KEY `ix_translation_en_nbchars` (`nbchars`),
 FULLTEXT KEY `FTXT_translation_en_text` (`text`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;