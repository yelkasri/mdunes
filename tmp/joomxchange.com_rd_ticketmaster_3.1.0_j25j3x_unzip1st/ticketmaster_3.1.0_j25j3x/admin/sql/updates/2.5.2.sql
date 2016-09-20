ALTER TABLE `#__ticketmaster_transactions_temp` ADD `errorcode` INT( 11 ) NOT NULL ,
ADD `errormessage` VARCHAR( 255 ) NOT NULL;

ALTER TABLE `#__ticketmaster_config` ADD `use_automatic_login` TINYINT( 1 ) NOT NULL; 

ALTER TABLE `#__ticketmaster_tickets` ADD `ordering` INT( 11 ) NOT NULL;

ALTER TABLE `#__ticketmaster_clients` ADD `gender` VARCHAR( 25 ) NOT NULL ,
ADD `birthday` DATE NOT NULL;

ALTER TABLE `#__ticketmaster_events` ADD `ticketcounter` INT( 11 ) NOT NULL;

ALTER TABLE `#__ticketmaster_orders` ADD `blacklisted` TINYINT( 1 ) NOT NULL;

INSERT INTO `#__ticketmaster_emails` (`emailid`, `template_type`, `mailbody`, `secured`, `userid`, `approved`, `mailsubject`, `lastchange`, `description`, `from_email`, `from_name`, `receive_bcc`, `reply_to_email`, `reply_to_name`, `published`) 
VALUES (52, '1', 'You want to cancel your order.<br/>Please click the button below if you realy want to cancel this order.', '1', '42', '1', 'Cancelling the order', CURRENT_TIMESTAMP, 'This is the message clients will see when they want to cancel their order.', '', '', '', '', '', '1');