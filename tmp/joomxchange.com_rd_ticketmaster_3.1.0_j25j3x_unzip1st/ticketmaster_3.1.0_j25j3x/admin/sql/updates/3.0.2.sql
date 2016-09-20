ALTER TABLE  `#__ticketmaster_config` ADD  `send_pdf_tickets` TINYINT( 1 ) NOT NULL ,
ADD  `send_multi_ticket_only` TINYINT( 0 ) NOT NULL ,
ADD  `send_multi_ticket_admin` TINYINT( 1 ) NOT NULL;