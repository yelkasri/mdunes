<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class Email_Sms_class extends WClasses {
private $_countryCode=0;
private $_phoneNumber=0;
public function sendSMS($user,$tempdata,$report=true){
if(empty($user) || empty($tempdata)) return false;
if(empty($user->mobile)){
if($report){
$this->userW('1391449403DZJK');
}
return false;
}
if(empty($user->uid) && empty($user->email)){
$this->adminE('The user is not define and SMS cannot be sent');
WMessage::log('The user is not define and SMS cannot be sent','sms-error');
WMessage::log($user, 'sms-error');
return false;
}
if(empty($user->uid))$user->uid=WUser::get('uid',$user->email );
if(empty($user->uid)) return false;
if(!$this->_checkPaidSMS($user->uid )) return false;
if(empty($tempdata->smsmessage)){
$this->adminW('There is no SMS message define for that email so the text version of the email was taken instead!');
if(empty($tempdata->ctext)){
$this->userE('1391449403DZJL');
return false;
}
$SMSMessage=$tempdata->ctext;
}else{
$SMSMessage=$tempdata->smsmessage;
}
$emailHelperC=WClass::get('email.conversion');
$SMSMessage=$emailHelperC->HTMLtoText($SMSMessage, false, false, true);
$status=false;
$emailLoadmailerC=WClass::get('email.loadmailer');
$SMSMailer=$emailLoadmailerC->getMailer('sms');
if(!empty($SMSMailer)){
$this->_formatPhoneNumber($user->mobile );
$countChar=strlen($SMSMessage );
$SMSsplit=WPref::load('PEMAIL_NODE_SMSSPLIT');
if($SMSsplit && $countChar > 160){
$leftoverMessage=$SMSMessage;
do {
$currenMessage=substr($leftoverMessage, 0, 160 );
$leftoverMessage=substr($leftoverMessage, 160 );
$status=$SMSMailer->sendSMS($this->_countryCode, $this->_phoneNumber, $currenMessage );
$countChar=strlen($leftoverMessage );
} while($countChar > 160 );
if(!empty($leftoverMessage ))$status=$SMSMailer->sendSMS($this->_countryCode, $this->_phoneNumber, $leftoverMessage );
}else{
$SMSMessage=substr($SMSMessage, 0, 160 );
$status=$SMSMailer->sendSMS($this->_countryCode, $this->_phoneNumber, $SMSMessage, $user->name );
}
}
if($report){
if($status){
$emailMailerC=Wclass::get('email.mailer');
if($emailMailerC->checkUserCanSeeMailMessage($user->uid )){
$MOBILE='+'.$this->_countryCode.' '.$this->_phoneNumber;
$this->userS('1391449404BHVJ',array('$MOBILE'=>$MOBILE));
}else{
$this->userS('1391449404BHVK');
}
}
}
$logFailed=WPref::load('PEMAIL_NODE_STATISTICS_LOGFAIL');
if($logFailed && !$status){
$message=WMessage::get();
$message->log('Mobile: '.$this->_countryCode . $this->_phoneNumber.' | Message: '.$SMSMessage .' | Error: '.$SMSMailer->getSentError(), 'sms-failed-sending');
}
return $status;
}
private function _formatPhoneNumber($mobile){
$mobile=str_replace( array('+(','+ ','+  ','+') , '+',$mobile );
$pos=strpos($mobile, '+');
if($pos !==false){
$pos++;
$finished=false;
$countryCode='';
$count=1;
do {
if(isset($mobile[$pos])){
if( is_numeric($mobile[$pos])){
$countryCode .=(string)$mobile[$pos];
}else{
$postionNumber=$pos;
$finished=true;
}
$pos++;
}else{
$finished=true;
}
$count++;
if($count > 3){
$postionNumber=$pos;
$finished=true;
}
} while( !$finished );
$this->_phoneNumber=WGlobals::filter( substr($mobile, $postionNumber ), 'numeric');
$this->_countryCode=$countryCode;
}else{
$this->_phoneNumber=WGlobals::filter($mobile, 'numeric');
}
}
private function _checkPaidSMS($uid){
if( WExtension::exist('subscription.node')){
$SMSPAID=WPref::load('PSUBSCRIPTION_NODE_SMS_PAID');
if(!$SMSPAID ) return true;
$subscriptionCheckC=WObject::get('subscription.check');
$subscriptionCheckC->restriction('email_sms_send', null, null, $uid );
if(!$subscriptionCheckC->getStatus(false)){
return false;
}
}
return true;
}
}
