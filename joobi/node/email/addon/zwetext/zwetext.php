<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Email_zwetext_addon extends Email_Parent_class {
public function sendSMS($countryCode,$phoneNumber,$SMSMessage){
if(empty($phoneNumber)){
WMessage::log('Phone Number not provided','Email_zwetext_addon');
return false;
}$status=false;
$mobile=WGlobals::filter($phoneNumber, 'numeric');
$fieldsA=array(
'senderid'=> $this->mailerInfoO->sms_username, 'user'=> $this->mailerInfoO->sms_api,
'password'=> $this->mailerInfoO->sms_password,
'message'=> $SMSMessage,
'mobile'=> $mobile
,'unicode'=> 1
,'groupid'=>'1,2'
);
$netcomRestC=WClass::get('netcom.rest', null, 'class', false);
$result=$netcomRestC->send('http://etext.co.zw/sendsms.php',$fieldsA );
if(empty($result)){
$status=false;
}else{
$status=true;
}
return $status;
}
}