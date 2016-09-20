<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class Email_type_type extends WTypes {
public $type=array(
 1=> 'framework'
,2=> 'phpmailer'
,3=> 'sendmail'
,4=> 'qmail'
,10=> 'smtp'
,101=> 'bulksms'
,102=> 'callfire'
,103=> 'clickatell'
,104=> 'itagg'
,105=> 'nexmo'
,110=> 'masivos'
,115=> 'zwetext'
);
}