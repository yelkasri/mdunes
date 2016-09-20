CREATE TABLE IF NOT EXISTS `#__ticketmaster_waitinglist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticketid` int(11) NOT NULL,
  `eventid` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `ordercode` varchar(50) NOT NULL,
  `confirmed` tinyint(1) NOT NULL,
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `processed` tinyint(1) NOT NULL,
  `ip_address` varchar(50) NOT NULL,
  `sent` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=41;

ALTER TABLE  `#__ticketmaster_config` 
ADD  `eventname_position` VARCHAR( 35 ) NOT NULL ,
ADD  `date_position` VARCHAR( 35 ) NOT NULL ,
ADD  `location_position` VARCHAR( 35 ) NOT NULL ,
ADD  `orderid_position` VARCHAR( 35 ) NOT NULL ,
ADD  `ordernumber_position` VARCHAR( 35 ) NOT NULL ,
ADD  `price_position` VARCHAR( 35 ) NOT NULL ,
ADD  `bar_position` VARCHAR( 35 ) NOT NULL ,
ADD  `name_position` VARCHAR( 35 ) NOT NULL ,
ADD  `free_text2_position` VARCHAR( 35 ) NOT NULL ,
ADD  `free_text1_position` VARCHAR( 35 ) NOT NULL ,
ADD  `position_seatnumber` VARCHAR( 35 ) NOT NULL ,
ADD  `orderdate_position` VARCHAR( 35 ) NOT NULL ,
ADD  `show_mailchimps` TINYINT( 1 ) NOT NULL;

ALTER TABLE  `#__ticketmaster_config` ADD  `show_waitinglist` TINYINT( 1 ) NOT NULL;
ALTER TABLE  `#__ticketmaster_tickets` ADD  `counter_choice` TINYINT( 1 ) NOT NULL;
ALTER TABLE  `#__ticketmaster_tickets` ADD  `starting_total_tickets` INT( 11 ) NOT NULL;
ALTER TABLE  `#__ticketmaster_tickets` ADD  `show_end_date` TINYINT( 1 ) NOT NULL;

ALTER TABLE  `#__ticketmaster_clients` ADD  `firstname` VARCHAR( 30 ) NOT NULL;

INSERT INTO `#__ticketmaster_emails` (`emailid`, `template_type`, `mailbody`, `secured`, `userid`, `approved`, `mailsubject`, `lastchange`, `description`, `from_email`, `from_name`, `receive_bcc`, `reply_to_email`, `reply_to_name`, `published`) VALUES
(102, 0, '<p><span style="font-family: arial, helvetica, sans-serif; font-size: 10pt;">Dear %%NAME%%,</span></p>\r\n<p><span style="font-family: arial, helvetica, sans-serif; font-size: 10pt;">Thank you for your order at&nbsp;%%COMPANYNAME%%, as we have no availabillity at the moment you have placed your order on the waitinglist. As soon as we get availabillity again, your tickets will be processed in order ot the waiting list. (First on the list will have their tickets also first)</span><br /><br /><span style="font-family: arial, helvetica, sans-serif; font-size: 10pt;">Below you will find a list of the ticket you have placed on the list:</span></p>\r\n<p><span style="font-family: arial, helvetica, sans-serif; font-size: 10pt;">%%ORDERLIST%%</span></p>\r\n<p><span style="font-family: arial, helvetica, sans-serif; font-size: 10pt;">Please, confirm your order by clicking the link below:</span><br /><span style="font-family: arial, helvetica, sans-serif; font-size: 10pt;"><a href="%%CONFIRMATIONLINK%%"><strong>%%CONFIRMATIONLINK%%</strong></a></span></p>\r\n<p><span style="font-family: arial, helvetica, sans-serif; font-size: 10pt;">What happens when your tickets will be processed?</span></p>\r\n<p><span style="font-family: arial, helvetica, sans-serif; font-size: 10pt;">Well, when processing your tickets, you will be notified by email. This email contains a link to complete your order. The ordered tickets needs to be paid within %%COUNT_OF_DAYS%% days, when you do not pay your tickets within this timeperiod they will be released. (They don''t come back on the waiting list then)</span></p>\r\n<p><span style="font-family: arial, helvetica, sans-serif; font-size: 10pt;">We hope you will be at our event!</span></p>\r\n<p><span style="font-family: arial, helvetica, sans-serif; font-size: 10pt;">Best Regards</span><br /><span style="font-family: arial, helvetica, sans-serif; font-size: 10pt;">[YOUR_NAME]</span><br /><span style="font-family: arial, helvetica, sans-serif; font-size: 10pt;">%%COMPANYNAME%%</span></p>', 1, 0, 0, 'Your tickets on the waitinglist', '2013-08-10 09:51:41', 'Please confirm tickets on the waiting list.', 'robert.dam@outlook.com', 'Your Website', 1, 'robert.dam@outlook.com', 'Your Website', 1),
(103, 1, '<p><span style="font-family: arial, helvetica, sans-serif; font-size: 10pt;">Thank you for your order at&nbsp;%%COMPANYNAME%%, as we have no availabillity at the moment you have placed your order on the waitinglist. As soon as we get availabillity again, your tickets will be processed in order ot the waiting list. (First on the list will have their tickets also first)</span></p>\r\n<p><strong><span style="font-family: arial, helvetica, sans-serif; font-size: 10pt;">What happens when your tickets will be processed?</span></strong></p>\r\n<p><span style="font-family: arial, helvetica, sans-serif; font-size: 10pt;">Well, when processing your tickets, you will be notified by email. This email contains a link to complete your order. The ordered tickets needs to be paid within 3 days, when you do not pay your tickets within this timeperiod they will be released. (They don''t come back on the waiting list then)</span></p>\r\n<p><span style="font-family: arial, helvetica, sans-serif; font-size: 10pt;">We hope you will be at our event!</span></p>', 1, 0, 0, 'Your tickets have been placed on our waiting list.', '2013-08-10 07:51:41', 'Your tickets have been placed on our waiting list.', 'robert.dam@outlook.com', 'Your Website', 1, 'robert.dam@outlook.com', 'Your Website', 1),
(104, 0, '<p><span style="font-family: tahoma, arial, helvetica, sans-serif; font-size: 10pt;">Dear %%NAME%%,</span></p>\r\n<p><span style="font-family: tahoma, arial, helvetica, sans-serif;"><span style="font-size: 10pt;"></span><strong>We have some good news for you!</strong> Your tickets that were placed on the waiting list has been processed! You only have to pay the tickets to get your entrance tickets! The payment needs to be done within the coming 3 days by clicking the payment link below in this email or by making a wired transfer to account number 11.11.11.345.56 on behalf of [YOUR_COMAPNY_NAME].</span><br /><br /><span style="font-family: tahoma, arial, helvetica, sans-serif; font-size: 10pt;">Below you will find a list of your ordered tickets:</span></p>\r\n<p><span style="font-family: tahoma, arial, helvetica, sans-serif; font-size: 10pt;">%%ORDERLIST%%</span></p>\r\n<p><span style="font-family: tahoma, arial, helvetica, sans-serif; font-size: 10pt;">Please, pay your order by clicking the link below:</span><br /><a href="%%PAYMENTLINK%%"><span style="font-family: tahoma, arial, helvetica, sans-serif; font-size: 10pt;"><strong>%%PAYMENTLINK%%</strong></span></a></p>\r\n<p><span style="font-family: tahoma, arial, helvetica, sans-serif;">As soon as your ticket have been paid by our website, they will be send instantly to this email address. You only have to print them, and take them with you!</span></p>\r\n<p><span style="font-family: tahoma, arial, helvetica, sans-serif; font-size: 10pt;">Best Regards</span><br /><span style="font-family: tahoma, arial, helvetica, sans-serif; font-size: 10pt;">[YOUR_NAME]</span><br /><span style="font-family: tahoma, arial, helvetica, sans-serif; font-size: 10pt;">%%COMPANYNAME%%</span></p>', 1, 0, 0, 'Tickets from waiting list have been processed.', '2013-08-10 07:51:41', 'Please pay tickets on the waiting list.', 'robert.dam@outlook.com', 'Your Website', 1, 'robert.dam@outlook.com', 'Your Website', 1);
