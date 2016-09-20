<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Email_Mailer_type extends WTypes {
public $mailer=array(
'phpmail'=>'PHP Mail Function',
'sendmail'=>'SendMail',
'qmail'=>'QMail',
'smtp'=>'SMTP Server'
);
}