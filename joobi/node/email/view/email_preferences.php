<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Email_Email_preferences_view extends Output_Forms_class {
protected function prepareView(){
$mailerIUsed=WPref::load('PEMAIL_NODE_MAILER');
$usecms=WPref::load('PEMAIL_NODE_USECMS');
if($mailerIUsed=='phpmail'){
$this->removeElements( array('apps_preferences_form_email_node_sendmailpath','apps_preferences_form_email_node_hostname','apps_general_preferences_smtp_config'));
}elseif($mailerIUsed=='sendmail' || $mailerIUsed=='qmail'){
$this->removeElements( array('apps_general_preferences_smtp_config'));
}elseif($mailerIUsed=='smtp'){
$this->removeElements( array('apps_preferences_form_email_node_sendmailpath','apps_preferences_form_email_node_hostname','apps_general_preferences_dkim_settings'));
}
$preferencesA=array('apps_general_preferences_mail_config','apps_general_preferences_smtp_config','apps_general_preferences_dkim_settings');
if($usecms==0){
$preferencesA[]='apps_preferences_form_mailers';
$this->removeElements($preferencesA );
}elseif($usecms==9){
$preferencesA[]='apps_preferences_form_email_node_fromname';
$preferencesA[]='apps_preferences_form_email_node_fromemail';
$preferencesA[]='apps_preferences_form_email_node_replyname';
$preferencesA[]='apps_preferences_form_email_node_replyemail';
$preferencesA[]='apps_preferences_form_email_node_senderemail';
$preferencesA[]='apps_preferences_form_email_node_embedimages';
$preferencesA[]='apps_preferences_form_email_node_multiplepart';
$preferencesA[]='apps_preferences_form_email_node_encoding';
$preferencesA[]='apps_preferences_form_email_node_charset';
$preferencesA[]='apps_preferences_form_email_node_wordwrap';
$preferencesA[]='apps_preferences_form_email_node_displayname';
$preferencesA[]='apps_preferences_form_email_node_encoding';
$this->removeElements($preferencesA );
}else{
$this->removeElements( array('apps_preferences_form_mailers'));
}
$dkim=WPref::load('PEMAIL_NODE_MAILERDKIMYESNO');
if(!$dkim){
$this->removeElements( array('apps_preferences_mail_form_dkim_dkimkeyloc','apps_preferences_mail_form_dkim_dkimpassphrase'));
}
return true;
}
}