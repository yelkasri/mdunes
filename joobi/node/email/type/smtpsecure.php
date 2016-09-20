<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Email_Smtpsecure_type extends WTypes {
public $smtpsecure=array(
0=> '- - -',
'ssl'=>'SSL',
'tls'=>'TLS',
);
}