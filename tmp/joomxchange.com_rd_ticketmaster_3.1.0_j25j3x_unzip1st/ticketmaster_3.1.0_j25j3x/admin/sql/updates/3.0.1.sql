ALTER TABLE  `#__ticketmaster_venues` ADD  `phone_number` VARCHAR( 50 ) NOT NULL ,
ADD  `contact_person` VARCHAR( 200 ) NOT NULL ,
ADD  `email_address` VARCHAR( 200 ) NOT NULL;

ALTER TABLE  `#__ticketmaster_tickets` 
ADD  `start_price` DOUBLE NOT NULL ,
ADD  `end_price` DOUBLE NOT NULL;