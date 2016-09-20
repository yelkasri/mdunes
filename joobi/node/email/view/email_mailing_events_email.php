<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Email_Email_mailing_events_email_view extends Output_Forms_class {
function prepareView(){
$receivertype=$this->getValue('receivertype');
switch ($receivertype){
case 1:
$this->removeElements( array('events_email_email'));
break;
case 2:
$this->removeElements( array('events_email_email'));
break;
case 3:
$this->removeElements( array('events_email_email'));
break;
default:
$this->removeElements( array('events_email_email'));
break;
}
return true;
}}