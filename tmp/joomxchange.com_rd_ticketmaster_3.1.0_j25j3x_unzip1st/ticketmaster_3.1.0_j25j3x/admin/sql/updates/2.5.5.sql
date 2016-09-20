ALTER TABLE `#__ticketmaster_config` 
ADD `load_bootstrap` TINYINT( 1 ) NOT NULL ,
ADD `load_bootstrap_tpl` TINYINT( 1 ) NOT NULL,
ADD `scan_api` INT( 6 ) NOT NULL;

ALTER TABLE `#__ticketmaster_config`
  DROP `payment_received`,
  DROP `ordercomplete_msg`,
  DROP `payment_email_manual`;

ALTER TABLE `#__ticketmaster_tickets` 
ADD COLUMN `ticket_size` VARCHAR(2) DEFAULT 'A5' NOT NULL,
ADD COLUMN `ticket_orientation` VARCHAR(1) DEFAULT 'L' NOT NULL, 
ADD COLUMN `combine_multitickets` TINYINT(1) DEFAULT 0 NOT NULL,
ADD `scan_pin` INT( 3 ) NOT NULL DEFAULT '0'; 

CREATE TABLE IF NOT EXISTS `#__ticketmaster_coupons` (
  `coupon_id` int(11) NOT NULL AUTO_INCREMENT,
  `coupon_limit` int(11) NOT NULL,
  `coupon_code` varchar(15) NOT NULL,
  `coupon_name` varchar(100) NOT NULL,
  `coupon_valid_to` date NOT NULL,
  `coupon_addedby` int(11) NOT NULL,
  `coupon_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `coupon_type` tinyint(1) NOT NULL,
  `coupon_discount` int(11) NOT NULL,
  `coupon_used` int(11) NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '0',
  `published` tinyint(1) NOT NULL,
  PRIMARY KEY (`coupon_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

ALTER TABLE `#__ticketmaster_orders` ADD `coupon` VARCHAR( 10 ) NOT NULL;