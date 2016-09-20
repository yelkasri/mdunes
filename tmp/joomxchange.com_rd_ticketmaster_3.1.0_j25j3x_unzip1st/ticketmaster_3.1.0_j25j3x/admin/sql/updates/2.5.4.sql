ALTER TABLE `#__ticketmaster_tickets` 
ADD `free_text_1` VARCHAR( 150 ) NOT NULL,
ADD `free_text_2` VARCHAR( 150 ) NOT NULL,
ADD `free_text1_position` VARCHAR( 10 ) NOT NULL,
ADD `free_text2_position` VARCHAR( 10 ) NOT NULL,
ADD `show_seatplans` TINYINT( 1 ) NOT NULL,
ADD `position_seatnumber` VARCHAR( 10 ) NOT NULL,
ADD `font_size_seatnumber` INT( 3 ) NOT NULL ,
ADD `seatnumber_fontcolor_r` INT( 4 ) NOT NULL ,
ADD `seatnumber_fontcolor_g` INT( 4 ) NOT NULL ,
ADD `seatnumber_fontcolor_b` INT( 4 ) NOT NULL;

ALTER TABLE `#__ticketmaster_config` CHANGE `transcosts` `transcosts` DOUBLE NOT NULL;
ALTER TABLE `#__ticketmaster_config` CHANGE `variable_transcosts` `variable_transcosts` DOUBLE NOT NULL;
ALTER TABLE `#__ticketmaster_config` ADD `remove_unfinished` TINYINT( 1 ) NOT NULL;
ALTER TABLE `#__ticketmaster_config` ADD `removal_hours` TINYINT( 1 ) NOT NULL;
ALTER TABLE `#__ticketmaster_config` ADD `pro_installed` TINYINT( 1 ) NOT NULL;

ALTER TABLE `#__ticketmaster_orders` ADD `seat_sector` INT( 11 ) NOT NULL;
ALTER TABLE `#__ticketmaster_orders` ADD `requires_seat` TINYINT( 1 ) NOT NULL;

 