<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Email_Email_mailer_form_email_mailer_view extends Output_Forms_class {
function prepareView(){
$type=$this->getValue('type','email.mailer');
if($type !=10){
$this->removeElements( array('email_mailer_form_fieldset_smtp'));
}
if($type !=3){
$this->removeElements( array('email_mailer_form_fieldset_send_mail'));
}
if($type==10 || $type > 100){
$this->removeElements( array('email_mailer_form_fieldset_dkim'));
}
if($type < 100){
$this->removeElements( array('email_mailer_form_fieldset_sms_settings'));
}
if($type > 100){
$this->removeElements( array('email_mailer_form_fieldset_email_settings','email_mailer_form_fieldset_frequency_limitation_settings','email_mailer_form_fieldset_max_emails_limitation_settings'));
$type=$this->getValue('type');
switch($type){
case 105:$this->removeElements('email_mailer_form_email_mailer_p_sms_username');
break;
default:
break;
}
}
return true;
}
}