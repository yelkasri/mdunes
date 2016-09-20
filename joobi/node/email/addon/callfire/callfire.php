<?php 


* @license GNU GPLv3 */

class Email_Callfire_addon extends Email_Parent_class {
public function sendSMS($countryCode,$phoneNumber,$SMSMessage){
$status=false;
$mobile=WGlobals::filter($user->mobile, 'numeric');
$fieldsA=array(
"apikey"=> $this->mailerInfoO->sms_api,
"ToNumber"=> $countryCode . $phoneNumber,
"Message"=> $SMSMessage
);
$netcomRestC=WClass::get('netcom.rest', null, 'class', false);
$result=$netcomRestC->send('https://www.callfire.com/api/1.1/rest/text',$fieldsA );
if('Error'==substr($result, 0, 5)){
$status=false;
}else{
$status=true;
}
return $status;
}
}