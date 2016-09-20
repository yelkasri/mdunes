<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class Email_Node_install {
public function install($object){
if(!empty($this->newInstall ) || (property_exists($object, 'newInstall') && $object->newInstall)){
$pref=WPref::get('email.node', false, false);
$pref->updatePref('replyname', JOOBI_SITE_NAME );
$pref->updatePref('fromname', JOOBI_SITE_NAME );
$this->_installDefaultMailer();
$this->_installDefaultMailingType();
}else{
}
return true;
}
private function _installDefaultMailingType(){
$mailingOfTypeA=array();
$typeO=new stdClass;
$typeO->name='System';
$typeO->description='System';
$typeO->namekey='system';
$typeO->designation=11;
$typeO->publish=1;
$typeO->rolid=1;
$mailingOfTypeA[]=$typeO;
$shoppersTypeM=WModel::get('email.type');
$ordering=0;
foreach($mailingOfTypeA as $oneType){
$shoppersTypeM->whereE('namekey',$oneType->namekey );
$exist=$shoppersTypeM->exist();
if($exist ) continue;
$ordering++;
$shoppersTypeM->mgtypeid=null;
$shoppersTypeM->setChild('email.typetrans','name',$oneType->name );
$shoppersTypeM->setChild('email.typetrans','description',$oneType->description );
$shoppersTypeM->namekey=$oneType->namekey;
$shoppersTypeM->designation=$oneType->designation;
$shoppersTypeM->publish=$oneType->publish;
$shoppersTypeM->rolid=$oneType->rolid;
$shoppersTypeM->ordering=$ordering;
$shoppersTypeM->core=1;
$shoppersTypeM->save();
}
return true;
}
private function _installDefaultMailer(){
$uid=WUser::get('uid');
$allMailerA=array();
$mailerO=new stdClass;
$mailerO->namekey='cms';
$mailerO->alias='CMS default';
$mailerO->type=1;
$mailerO->designation=1;
$mailerO->core=1;
$mailerO->publish=1;
$mailerO->premium=1;
$mailerO->rolid=1;
$mailerO->uid=$uid;
$allMailerA[]=$mailerO;
$mailerO=new stdClass;
$mailerO->namekey='phpmailer';
$mailerO->alias='PHP Mailer';
$mailerO->type=2;
$mailerO->designation=1;
$mailerO->core=1;
$mailerO->publish=1;
$mailerO->premium=0;
$mailerO->rolid=1;
$mailerO->uid=$uid;
$allMailerA[]=$mailerO;
foreach($allMailerA as $oneMailer){
$emailMailerM=WModel::get('email.mailer');
foreach($oneMailer as $oneKey=> $oneVal){
$emailMailerM->setVal($oneKey, $oneVal );
}
$emailMailerM->insertIgnore();
}
}
function joomla30($object){
if(!$object->newInstall){
return true;
}
if( defined('JVERSION')){
$conf=JFactory::getConfig();
$pref=WPref::get('email.node',false,false);
if( version_compare( JVERSION, '3.0.0','<')){
$pref->updatePref('mailer',$conf->getValue('config.mailer'));
$pref->updatePref('senderemail',$conf->getValue('config.mailfrom'));
$pref->updatePref('replyemail',$conf->getValue('config.mailfrom'));
$pref->updatePref('fromemail',$conf->getValue('config.mailfrom'));
$pref->updatePref('sendmailpath',$conf->getValue('config.sendmail'));
$pref->updatePref('smtp_auth_required',$conf->getValue('config.smtpauth'));
$pref->updatePref('smtp_host',$conf->getValue('config.smtphost'));
$pref->updatePref('smtp_username',$conf->getValue('config.smtpuser'));
$pref->updatePref('smtp_password',$conf->getValue('config.smtppass'));
}else{
$pref->updatePref('mailer',$conf->get('config.mailer'));
$pref->updatePref('senderemail',$conf->get('config.mailfrom'));
$pref->updatePref('replyemail',$conf->get('config.mailfrom'));
$pref->updatePref('fromemail',$conf->get('config.mailfrom'));
$pref->updatePref('sendmailpath',$conf->get('config.sendmail'));
$pref->updatePref('smtp_auth_required',$conf->get('config.smtpauth'));
$pref->updatePref('smtp_host',$conf->get('config.smtphost'));
$pref->updatePref('smtp_username',$conf->get('config.smtpuser'));
$pref->updatePref('smtp_password',$conf->get('config.smtppass'));
}
}
return true;
}
}