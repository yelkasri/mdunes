<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Email_Nexmo_addon extends Email_Parent_class {
public function sendSMS($countryCode,$phoneNumber,$SMSMessage,$name=''){
$status=false;
if(empty($countryCode )){
$this->userE('1399506371OXYN');
return false;
}
if(!function_exists('mb_check_encoding') || ! mb_check_encoding($SMSMessage, 'UTF-8')){
$this->userE('1399988167SXRS');
return false;
}
$mobile=WGlobals::filter($phoneNumber, 'numeric');
if(empty($this->mailerInfoO->sms_api )){
$this->adminE('The API key for the SMS API is not defined!');
return false;
}
if(empty($this->mailerInfoO->sms_password )){
$this->adminE('The password for the SMS API is not defined!');
return false;
}
$containsUnicode=max( array_map('ord', str_split($SMSMessage)) ) > 127;
$this->_validateOriginator($name );
$name=urlencode($name );
$SMSMessage=urlencode($SMSMessage );
$fieldsA=array(
'from'=> $name,
'to'=>'+'.$countryCode . $mobile,
'text'=> $SMSMessage,
'type'=> ($containsUnicode?'unicode' : 'text'),
'username'=> $this->mailerInfoO->sms_api,
'password'=> $this->mailerInfoO->sms_password
);
$result=$this->sendRequest($fieldsA );
$jsonResultO=json_decode($result );
if($jsonResultO->messages[0]->status==0){
$status=true;
}else{
$status=false;
$p='error-text';
$ERROR_MESSAGE=$jsonResultO->messages[0]->$p;
$this->adminE('Error sending SMS : '.$ERROR_MESSAGE );
}
return $status;
}
private function sendRequest($data){
$post='';
foreach($data as $k=> $v){
$post .="&$k=$v";
}
if( function_exists('curl_version')){
$this->ssl_verify=false;
$to_nexmo=curl_init();
curl_setopt($to_nexmo, CURLOPT_POST, true);
curl_setopt($to_nexmo, CURLOPT_RETURNTRANSFER, true);
curl_setopt($to_nexmo, CURLOPT_POSTFIELDS, $post );
curl_setopt($to_nexmo, CURLOPT_URL, 'https://rest.nexmo.com/sms/json');
if(!$this->ssl_verify){
curl_setopt($to_nexmo, CURLOPT_SSL_VERIFYPEER, false);
}
$from_nexmo=curl_exec($to_nexmo );
curl_close ($to_nexmo );
}elseif(ini_get('allow_url_fopen')){
$opts=array('http'=>
array(
'method'=>'POST',
'header'=>'Content-type: application/x-www-form-urlencoded',
'content'=> $post
)
);
$context=stream_context_create($opts);
$from_nexmo=file_get_contents('https://rest.nexmo.com/sms/json', false, $context);
}else{
return false;
}
return $from_nexmo;
return $this->nexmoParse($from_nexmo );
}
private function _validateOriginator($inp){
$ret=preg_replace('/[^a-zA-Z0-9]/','', (string)$inp);
if(preg_match('/[a-zA-Z]/',$inp)){
$ret=substr($ret, 0, 11);
}else{
if(substr($ret, 0, 2)=='00'){
$ret=substr($ret, 2);
$ret=substr($ret, 0, 15);
}
}
return (string)$ret;
}
}