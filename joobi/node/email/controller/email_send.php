<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Email_send_controller extends WController {
function send(){
if(!WPref::load('PEMAIL_NODE_ALLOWBYURL')) return true;
$pw=WPref::load('PEMAIL_NODE_ALLOWBYURLPWD');
if( strlen($pw) < 10){
echo 'The password need to be at least 10 characters!';
exit();
}
$key=WGlobals::get('key');
if(empty($key)){
echo 'key not provided!';
exit();
}
if($key !=$pw){
echo 'Password do not match!';
exit();
}
$email=WGlobals::get('toemail');
$subject=WGlobals::get('subject');
$body=WGlobals::get('body');
$subject=urldecode($subject );
$body=urldecode($body );
$body=base64_decode($body );
$senderName=WGlobals::get('senderName');
$senderEmail=WGlobals::get('senderEmail');
$replyName=WGlobals::get('replyName');
$replyEmail=WGlobals::get('replyEmail');
$backHome=WGlobals::get('backHome');
$mail=WMail::get();
if(!empty($senderEmail))$mail->addSender($senderEmail, $senderName );
if(!empty($replyEmail))$mail->replyTo($replyEmail, $replyName );
$mail->sendTextNow($email, $subject, $body );
$params=new stdClass;
$params->email=$email;
$params->subject=$subject;
$params->body=$body;
$mail->setParameters($params );
$mail->sendAdmin('email_notify_admin');
if(!empty($backHome)){
$backHome=base64_decode($backHome );
WPages::redirect($backHome );
}
echo 'email sent!';
exit;
return true;
}
}