<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
WLoadFile('netcom.class.client');
class Email_Netcom_class extends Netcom_Client_class {
public function statistics($data){
$emailStatisticsC=WClass::get('email.statistics');
$emailStatisticsC->recordOpenMail($data );
return true;
}
}
