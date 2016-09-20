<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Email_Clickatell_addon extends Email_Parent_class {
public function sendSMS($countryCode,$phoneNumber,$SMSMessage){
$status=false;
$mobile=WGlobals::filter($user->mobile, 'numeric');
$message=urlencode($SMSMessage );
$user=$this->mailerInfoO->sms_username;
$password=$this->mailerInfoO->sms_password;
$api_id=$this->mailerInfoO->sms_api;
$baseurl="http://api.clickatell.com";
$to=$mobile;
$url="$baseurl/http/auth?user=$user&password=$password&api_id=$api_id";
$ret=file($url);
$sess=explode(":",$ret[0]);
$status=false;
if($sess[0]=="OK"){
$sess_id=trim($sess[1]); $url="$baseurl/http/sendmsg?session_id=$sess_id&to=$to&text=$message";
$ret=file($url);
$send=explode(":",$ret[0]);
if($send[0]=="ID"){
$status=true;
}else{
$status=false;
}
}else{
$status=false;
}
return $status;
}
}
