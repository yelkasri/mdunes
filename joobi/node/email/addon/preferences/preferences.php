<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Email_Preferences_addon extends Email_Parent_class {
public function setUp(){
$this->mailerInfoO->sendername=PEMAIL_NODE_FROMNAME;
$this->mailerInfoO->senderemail=PEMAIL_NODE_FROMEMAIL;
$this->mailerInfoO->mailer=PEMAIL_NODE_MAILER;
$this->mailerInfoO->sendmail=PEMAIL_NODE_SENDMAILPATH;
$this->mailerInfoO->smtpauth=PEMAIL_NODE_SMTP_AUTH_REQUIRED;
$this->mailerInfoO->smtpsecure=PEMAIL_NODE_SMTP_SECURE;
$this->mailerInfoO->smtpport=PEMAIL_NODE_SMTP_PORT;
$this->mailerInfoO->smtpuser=PEMAIL_NODE_SMTP_USERNAME;
$this->mailerInfoO->smtppass=PEMAIL_NODE_SMTP_PASSWORD;
$this->mailerInfoO->smtphost=PEMAIL_NODE_SMTP_HOST;
$this->mailerInfoO->addnames=PEMAIL_NODE_DISPLAYNAME;
$this->mailerInfoO->bouncebackemail=PEMAIL_NODE_SENDEREMAIL;
$this->mailerInfoO->embedimages=PEMAIL_NODE_EMBEDIMAGES;
$this->mailerInfoO->charset=PEMAIL_NODE_CHARSET;$this->mailerInfoO->multiplepart=PEMAIL_NODE_MULTIPLEPART;$this->mailerInfoO->encodingformat=PEMAIL_NODE_ENCODING;
$this->mailerInfoO->hostname=PEMAIL_NODE_HOSTNAME;
$this->mailerInfoO->wordwrapping=PEMAIL_NODE_WORDWRAP;
$this->mailerInfoO->replyname=PEMAIL_NODE_REPLYNAME;
$this->mailerInfoO->replyemail=PEMAIL_NODE_REPLYEMAIL;
}
}