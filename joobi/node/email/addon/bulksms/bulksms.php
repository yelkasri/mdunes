<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Email_Bulksms_addon extends Email_Parent_class {
public function sendSMS($countryCode,$phoneNumber,$SMSMessage){
$status=false;
$mobile=WGlobals::filter($user->mobile, 'numeric');
$message=urlencode($SMSMessage );
$fieldsA=array(
'username'=> $this->mailerInfoO->sms_username,
'password'=> $this->mailerInfoO->sms_password,
'message'=> $message,
'msisdn'=> $mobile
);
$netcomRestC=WClass::get('netcom.rest', null, 'class', false);
$result=$netcomRestC->send('http://bulksms.vsms.net:5567/eapi/submission/send_sms/2/2.0',$fieldsA );
if(empty($result)){
$status=false;
}else{
$status=true;
}
return $status;
}
}
