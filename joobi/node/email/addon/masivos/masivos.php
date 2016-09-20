<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Email_Masivos_addon extends Email_Parent_class {
public function sendSMS($countryCode,$phoneNumber,$SMSMessage){
$status=false;
$mobile=WGlobals::filter($user->mobile, 'numeric');
$SMSMessage=str_replace(" ", "%20", $SMSMessage );
$fieldsA=array(
"apikey"=> $this->mailerInfoO->sms_api,
"mensaje"=> $SMSMessage,
"numcelular"=> $phoneNumber,
"numregion"=> $countryCode
);
$netcomRestC=WClass::get('netcom.rest', null, 'class', false);
$result=$netcomRestC->send('http://www.smsmasivos.com.mx/sms/api.envio.php',$fieldsA );
if('Error'==substr($result, 0, 5)){
$status=false;
}else{
$status=true;
}
return $status;
}
}