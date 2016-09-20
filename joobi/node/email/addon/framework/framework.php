<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Email_Framework_addon extends Email_Parent_class {
public function setUp(){
$mailInfoO=WPage::getMailInfo();
$this->mailerInfoO->sendername=$mailInfoO->fromname;
$this->mailerInfoO->senderemail=$mailInfoO->mailfrom;
$this->mailerInfoO->mailer=$mailInfoO->mailer;
$this->mailerInfoO->sendmail=$mailInfoO->sendmail;
$this->mailerInfoO->smtpauth=$mailInfoO->smtpauth;
$this->mailerInfoO->smtpsecure=$mailInfoO->smtpsecure;
$this->mailerInfoO->smtpport=$mailInfoO->smtpport;
$this->mailerInfoO->smtpuser=$mailInfoO->smtpuser;
$this->mailerInfoO->smtppass=$mailInfoO->smtppass;
$this->mailerInfoO->smtphost=$mailInfoO->smtphost;
$this->mailerInfoO->addnames=WPref::load('PEMAIL_NODE_DISPLAYNAME');
$this->mailerInfoO->bouncebackemail=WPref::load('PEMAIL_NODE_SENDEREMAIL');
$this->mailerInfoO->embedimages=WPref::load('PEMAIL_NODE_EMBEDIMAGES');
$this->mailerInfoO->charset=WPref::load('PEMAIL_NODE_CHARSET');
$this->mailerInfoO->multiplepart=WPref::load('PEMAIL_NODE_MULTIPLEPART');
$this->mailerInfoO->encodingformat=WPref::load('PEMAIL_NODE_ENCODING');
$this->mailerInfoO->hostname=WPref::load('PEMAIL_NODE_HOSTNAME');
$this->mailerInfoO->wordwrapping=WPref::load('PEMAIL_NODE_WORDWRAP');
$this->mailerInfoO->replyname=WPref::load('PEMAIL_NODE_REPLYNAME');
$this->mailerInfoO->replyemail=WPref::load('PEMAIL_NODE_REPLYEMAIL');
}
}