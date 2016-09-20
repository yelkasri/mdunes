<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Email_Mailermethod_picklist extends WPicklist {
function create(){
$this->addElement('--1a','--'.WText::t('1391449403DZJI'));
$this->addElement( 1, 'Framework method');
$this->addElement( 2, 'PHP Mailer');
switch( strtoupper( substr( PHP_OS, 0, 3 ))){
case "WIN":
break;
case "MAC":
case "DAR":
default:
$this->addElement( 3, 'Send mail');
$this->addElement( 4, 'Qmail');
break;
}$this->addElement( 10, 'SMTP Server');
$this->addElement('--100a','--'.WText::t('1391449403DZJJ'));
$this->addElement( 101, 'SMS Bulk SMS');
$this->addElement( 102, 'SMS CallFire');
$this->addElement( 103, 'SMS Clickatell');
$this->addElement( 104, 'SMS iTagg');
$this->addElement( 105, 'SMS Nexmo');
$this->addElement( 110, 'SMS Masivos');
$this->addElement( 115, 'SMS ZW eText');
return true;
}
}