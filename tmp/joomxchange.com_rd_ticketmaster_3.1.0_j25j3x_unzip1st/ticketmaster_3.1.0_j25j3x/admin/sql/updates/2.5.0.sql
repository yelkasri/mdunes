ALTER TABLE `#__ticketmaster_config` 
ADD `show_mailchimp_signup` TINYINT( 1 ) NOT NULL ,
ADD `mailchimp_api` VARCHAR( 50 ) NOT NULL ,
ADD `mailchimp_listid` VARCHAR( 50 ) NOT NULL,
ADD `activation_email` TINYINT (1) NOT NULL;

ALTER TABLE #__ticketmaster_emails AUTO_INCREMENT = 250;

DROP TABLE `#__ticketmaster_subtickets` ;

ALTER TABLE `#__ticketmaster_config`  ADD `send_profile_mail` TINYINT( 1 ) NOT NULL,
ADD `tos_tpl` INT( 11 ) NOT NULL,
ADD `send_confirmation_pdf` TINYINT( 1 ) NOT NULL;

INSERT INTO `#__ticketmaster_emails` (`emailid`, `template_type`, `mailbody`, `secured`, `userid`, `approved`, `mailsubject`, `lastchange`, `description`, `from_email`, `from_name`, `receive_bcc`, `reply_to_email`, `reply_to_name`, `published`) VALUES (50, 1, 'Terms of Service Page', 1, 42, 1, 'Terms and Conditions for ticket ordering.', '2012-02-20 15:21:15', 'Will be shown when user clicks the terms and services link.', '', '', 0, '', '', 1);

INSERT INTO `#__ticketmaster_emails` (`emailid`, `template_type`, `mailbody`, `secured`, `userid`, `approved`, `mailsubject`, `lastchange`, `description`, `from_email`, `from_name`, `receive_bcc`, `reply_to_email`, `reply_to_name`, `published`) VALUES (51, 1, '<p>Here you can find our upcoming events.</p>', 1, 42, 1, 'Upcoming Events!', '2012-02-20 15:21:15', 'Will be shown when user is at the upcoming events page. (this is the free text above the listings)', '', '', 0, '', '', 1);

ALTER TABLE `#__ticketmaster_tickets` ADD `max_ordering` INT( 5 ) NOT NULL ,
ADD `min_ordering` INT( 5 ) NOT NULL ,
ADD `end_date` DATE NOT NULL ,
ADD `end_time` VARCHAR( 50 ) NOT NULL ,
ADD `sale_stop` DATETIME NOT NULL ,
ADD `use_sale_stop`   INT( 5 )  NOT NULL;