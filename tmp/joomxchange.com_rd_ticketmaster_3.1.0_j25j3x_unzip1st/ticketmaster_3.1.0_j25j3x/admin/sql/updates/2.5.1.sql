ALTER TABLE `#__ticketmaster_tickets` ADD `max_ordering` INT( 5 ) NOT NULL ,
ADD `min_ordering` INT( 5 ) NOT NULL ,
ADD `end_date` DATE NOT NULL ,
ADD `end_time` VARCHAR( 50 ) NOT NULL ,
ADD `sale_stop` DATETIME NOT NULL ,
ADD `use_sale_stop`   INT( 5 )  NOT NULL;

ALTER TABLE `#__ticketmaster_config` ADD `show_venuebox` TINYINT( 1 ) NOT NULL;