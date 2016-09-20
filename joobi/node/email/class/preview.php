<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class Email_Preview_class extends WClasses {
public function preview(){
$vars=WGlobals::get( JOOBI_VAR_DATA, array(), '','array');
$email=WController::getFormValue('testemail');
if(empty($email))$email=WUser::get('email');
$forceHtml=WController::getFormValue('html','x', 1 );
if(!empty($email) && is_string($email)){
WGlobals::set('testemail',$email );
}
WGlobals::set('html',$forceHtml );
}
public function sendTestEmail(){
$vars=WGlobals::get( JOOBI_VAR_DATA, array(), '','array');
$uid=null;
$email=WController::getFormValue('testemail');
if(!empty($email)){
$emailTo=trim($email);
$myMember=WModel::get('users');
$myMember->whereE('email',$emailTo);
$uid=$myMember->load('lr',array('uid'));
}
$user=WUser::get('object',$uid );
if(!empty($emailTo)){
$user->email=$emailTo;
}
if(!empty($user->email)){
$mailing=WMail::get();
$forceHtml=(bool)WController::getFormValue('html','x', true);
$mailing->setMailFormat($forceHtml );
$mailing->forceSending();
$maiId=WGlobals::getEID();
if(empty($maiId))$maiId='main_test_email';
$mailing->sendNow($user, $maiId );
}else{
$message=WMessage::get();
$message->userW('1230529130KYBD');
}
}
}
