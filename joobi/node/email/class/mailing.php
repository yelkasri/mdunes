<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
 class Email_Mailing_class extends WClasses {
 private static $_mailer=null;
var $processTags=true;
private $_parameters=null;
private $_frequency=null;
var $_to=array();
 var $_cc=array();
 var $_bcc=array();
 private $_sender=array();
 var $_replyTo=array();
var $_donotsend=false;
private $_validMailer=false;
private $_setMailFormat=null;
public $_force=false;
private $_trackLinks=null;
private $_trackingParams=null;
private $_trackOpenRate=null;
private $_link=null;
private $_reportsA=array();
function __construct(){
if(!isset(self::$_mailer)) self::$_mailer=WClass::get('email.mailer');
$this->_validMailer=self::$_mailer->validMailer();
$this->_link=null;
}
public function setMailFormat($html=true){
$this->_setMailFormat=$html;
}
public function forceSending($force=true){
$this->_force=$force;
}
public function sendSMS($user,$mgid,$report=true){
$status=$this->_sendAnMail($user, $mgid, '','', null, true, $report );
$this->clear();
return $status;
}
public function sendNow($user,$mgid,$report=true){
$status=$this->_sendAnMail($user, $mgid, '','', null, false, $report );
$this->clear();
return $status;
}
public function setTracking($track,$trackingParams=null){
$this->_trackLinks=$track;
$this->_trackingParams=$trackingParams;
}
public function setNotificationLink($link){
$this->_link=$link;
}
public function setOpenRate($track=true){
$this->_trackOpenRate=$track;
}
private function _sendAnMail($user=null,$mgid=0,$subject='',$bodyText='',$html=false,$onlySMS=false,$report=true){
if(!$this->_validMailer || ! WPref::load('PEMAIL_NODE_ENABLE')) return false;
static $mailingM=null;
static $mailingTransM=null;
static $mailInfoA=array();
static $mailTransInfoA=array();
if(empty($user)){
return false;
}
if( is_string($user) && (false !=strpos($user, ',') || false !=strpos($user, "\r" ) || false !=strpos($user, "\n" )  )){
$userStr=strtolower( trim($user));
$userStr=str_replace(' ','',$userStr );
$userStr=str_replace( array( "\r\n", "\n\r", "\r", "\n" ), ',',$userStr );
$userA=explode(',',$userStr );
$user=array();
$usersEmail=WClass::get('users.email');
foreach($userA as $oneU){
if(false !==strpos($oneU, '@')){
if($usersEmail->validateEmail($oneU ))$user[]=$oneU;
}else{
$user[]=$oneU;
}
}
}
if(!empty($user) && is_array($user)){
$this->keepAlive(true);
$status=true;
foreach($user as $oneUser){
$status=$this->_sendAnMail($oneUser, $mgid, $subject, $bodyText, $html, $onlySMS, $report ) && $status;
}$this->keepAlive(false);
return $status;
}
$message=WMessage::get();
if(( is_numeric($user) || is_string($user))){
$userForTrace=$user;
$uid=$user;
$user=WUser::get('data',$uid );
if(empty($user->email) && is_string($uid)){
$user=new stdClass;
$user->email=$uid;
}}
if(empty($user) || empty($user->email)){
return false;
}
if(empty($user->uid)){
if( is_numeric($user->email)){
WMessage::log( "The user $user->email is not on the website any more", 'error-wrong-email');
return false;
}
$usersEmail=WClass::get('users.email');
if(!$usersEmail->validateEmail($user->email )){
$EMAIL=$user->email;
if($report){
$message->userE('1443227628BQVB',array('$EMAIL'=>$EMAIL));
}else{
WMessage::log( "The provided email address ($EMAIL ) is not valid!", 'error-wrong-email');
}
$this->_reportsA['failed'][]="The provided email address ($EMAIL ) is not valid!";
return false;
}else{
$uid=WUser::get('uid',$user->email );
if(!empty($uid))$user->uid=$uid;
}}
if(!isset($this->_trackLinks)){
$this->_trackLinks=WPref::load('PEMAIL_NODE_STATISTICS_LINK');
}
$manualMail=true;if(empty($subject)){
$manualMail=false;
$caching=WPref::load('PLIBRARY_NODE_CACHING');
$caching=($caching > 0 )?'cache' : 'static';
if(!empty($user->uid)){
$lgid=empty($user->lgid)?WUser::get('lgid',$user->uid ) : $user->lgid;
}else{
$lgid=WUser::get('lgid');
}
$tempdata=$this->_loadSQLMail($mgid, $lgid );
if(empty($tempdata ))$tempdata=$this->_loadSQLMail($mgid, 1 );
if(empty($tempdata)){
$message->codeE('The mail does not exist: '.$mgid );
return false;
}
if($tempdata->publish < 1 && !$this->_force){
if($report){
$MAILING=$tempdata->namekey;
$message->adminW('Mailing not published: '. $MAILING );
}return false;
}
$emailType=WPref::load('PEMAIL_NODE_TYPE');
switch($emailType){
case 'html':
$html=true;
break;
case 'text':
$html=false;
break;
default:
if(!isset($this->_setMailFormat)){
if(empty($tempdata->html) || $tempdata->html=='2' || ! isset($user->html)){
$html=$tempdata->html;
}else{
$html=$user->html;
}}else{
$html=$this->_setMailFormat;
}break;
}
if(!isset($this->_trackOpenRate))$this->_trackOpenRate=WPref::load('PEMAIL_NODE_STATISTICS_READ');
self::$_mailer->setParameters($this->_parameters );
self::$_mailer->setTracking($this->_trackLinks, $this->_trackingParams );
$uid=(!empty($user->uid)?$user->uid : 0 );
self::$_mailer->createFromTrans($tempdata, $html, $this->_trackOpenRate, $uid, $tempdata->mgid );
self::$_mailer->processTags=$this->processTags;
}else{
self::$_mailer->IsHTML($html );
self::$_mailer->Subject=$subject;
self::$_mailer->Body=$bodyText;
}
if(!$onlySMS){
self::$_mailer->returnObject($this->_donotsend );
if(!empty($this->_to)){
foreach($this->_to as $to ) self::$_mailer->address($to[0], $to[1] );
}
if(!empty($this->_replyTo)){
foreach($this->_replyTo as $to) self::$_mailer->replyTo($to[0], $to[1] );
}
if(!empty($this->_cc)){
foreach($this->_cc as $to) self::$_mailer->CC($to[0], $to[1] );
}
if(!empty($this->_bcc)){
foreach($this->_bcc as $to) self::$_mailer->BCC($to[0], $to[1] );
}
if(!empty($this->_sender)){
self::$_mailer->addSender($this->_sender[0], $this->_sender[1] );
}
if(empty($user)) return self::$_mailer->sendMail($report );
$status=self::$_mailer->sendUser($user, $report );
}
if(!$manualMail && WExtension::exist('main.node')){
if(!isset($tempdata))$tempdata=new stdClass;
$tempdata->subject=self::$_mailer->getSubject();
$tempdata->body=self::$_mailer->getBody();
$tempdata->text=self::$_mailer->getText();
$tempdata->parameters=$this->_parameters;
$this->_sendNotification($user, $tempdata, $this->_link );
}
$enableSMS=WPref::load('PEMAIL_NODE_SMSENABLE');
if($enableSMS && !empty($tempdata->sms)){
$tag=WClass::get('output.process');
$tag->setParameters($this->_parameters );
$tag->replaceTags($tempdata->smsmessage, $user );
$emailSMSC=WClass::get('email.sms');
$emailSMSC->sendSMS($user, $tempdata );
}
if(!isset($this->_trackOpenRate))$this->_trackOpenRate=WPref::load('PEMAIL_NODE_STATISTICS_SENT');
if($this->_trackOpenRate && !empty($tempdata)){
$emailStatisticsC=WClass::get('email.statistics');
$tempdata->html=$html;
$emailStatisticsC->recordSentMail($user, $tempdata, $status );
}
$EMAIL=$user->email;
if($status){
$this->_reportsA['success'][]=str_replace(array('$EMAIL'), array($EMAIL),WText::t('1451256801RGQN'));
}else{
$this->_reportsA['failed'][]=str_replace(array('$EMAIL'), array($EMAIL),WText::t('1451256801RGQM'));
}
return $status;
}
public function getReportA(){
return $this->_reportsA;
}
private function _sendNotification($user,$tempdata,$link=''){
if(!WPref::load('PEMAIL_NODE_USENOTIFICATION')){
if(!empty($tempdata->channel) && WRoles::isAdmin('manager')){
$this->adminN('A notification should have been sent, but notification are not turn on!');
}return false;
}
$useMobile=false;
if(!empty($tempdata->channel)){
$channelA=WTools::preference2Array($tempdata->channel );
if(!empty($channelA)){
if( in_array('mobile',$channelA ))$useMobile=true;
$mainCredentialsC=WClass::get('main.credentials', null, 'class', false);
foreach($channelA as $chanel){
if('mobile'==$chanel ) continue;
$data=$mainCredentialsC->loadFromID($chanel );
$classCommC=WClass::get('main.'.$data->typeNamekey, null, 'class', false);
if($classCommC && method_exists($classCommC, 'sendNotification')){
$classCommC->sendNotification($data, $tempdata, $user, $link );
}
}
}
}else{
$useMobile=true;
}
if($useMobile){
if(empty($user->uid)) return false;
if( Wuser::get('uid')==$user->uid ) return true;
$mainMessageQueueC=WClass::get('main.messagequeue');
$mainMessageQueueC->addEmailToQueue($user, $tempdata, $link );
}
}
private function _loadSQLMail($mgid,$lgid){
static $mailingM=null;
$mailingM=WModel::get('email');
$mailInfoA=$mailingM->loadMemory($mgid, $lgid );
if(!empty($mailInfoA->mgid))$mailInfoA->id=$mailInfoA->mgid;
return $mailInfoA;
}
public function sendSchedule($user,$mgid,$sendDate=0,$priority=null,$maxDelay=300,$report=false){
$hasCronTask=WPref::load('PLIBRARY_NODE_CRON');
if(empty($hasCronTask )){
$this->adminW('The cron task is disabled and the system is trying to send scheduled email. The emails are being sent directly but this could create an overload of the server!');
return $this->sendNow($user, $mgid, $report );
}if($maxDelay > 0){
$schedulerM=WModel::get('scheduler');
$schedulerM->rememberQuery(true);
$schedulerM->whereE('namekey','email.queue.scheduler');
$frequency=$schedulerM->load('lr');
if($frequency > $maxDelay|| ($sendDate>0 && $sendDate < time())){return $this->sendNow($user, $mgid, $report );
}}
$emailScheduledC=WClass::get('email.scheduled');
$status=$emailScheduledC->processScheduledMail($user, $mgid, $sendDate, $priority, $this->_parameters, $this->_frequency, $report );
if($report){
if($status){
$message=WMessage::get();
if($maxDelay<1){
$MINUTES=WTExt::translate('few');
}else{
$MINUTES=ceil($maxDelay / 60 );
}$message->userS('1374537510PXRN',array('$MINUTES'=>$MINUTES));
}}
return true;
}
function scheduleAdmin($mgid,$sendDate=0,$priority=null,$maxDelay=300,$report=false){
$hasCronTask=WPref::load('PLIBRARY_NODE_CRON');
if(empty($hasCronTask )){
$this->adminW('The cron task is disabled and the system is trying to send scheduled email. The emails are being sent directly but this could create an overload of the server!');
return $this->sendAdmin($mgid, $report );
}
return $this->sendAdmin($mgid, $report );
}
public function sendAdmin($mgid,$report=false,$onlySMS=false){
$this->clear(); 
$sadmin=WUser::getRoleUsers('sadmin',array('email','username','uid'));
if(empty($sadmin)){
$message=WMessage::get();
$message->codeE('There is no super admin on the website');
return false;
}
foreach($sadmin as $i=> $user){
if(empty($i)){
$admin=$user;
}else{
$this->address($user->email, $user->username, $user->uid );
}}
$status=$this->_sendAnMail($admin, $mgid, '','', null, $onlySMS, $report );
$this->clear();
return $status;
}
public function sendTextNow($uid,$subject,$bodyText='',$html=false,$report=true){
$status=$this->_sendAnMail($uid, 0, $subject, $bodyText, $html, false, $report );
$this->clear();
return $status;
}
public function sendTextQueue($uid,$subject,$bodyText='',$html=false,$report=false){
return $this->sendTextNow($uid, $subject, $bodyText, $html, $report );
}
public function sendTextAdmin($subject,$bodyText='',$report=false,$email=null,$name=null){
if(!isset($email)){$email=WPref::load('PEMAIL_NODE_ADMINNOTIF');if(!empty($email )){
if(empty($name))$name=JOOBI_SITE_NAME;
$sadmin=array('email'=> $email, 'name'=>$name );
}else{
$sadmin=WUser::getRoleUsers('sadmin',array('email','username','uid'));
if(!empty($sadmin)){
foreach($sadmin as $user)$this->address($user->email, $user->username, $user->uid );
}else{
$message=WMessage::get();
$message->codeE('There is no super admin on the website');
return false;
}
}}else{if(is_array($email)){
$i=0;
$sadmin=array();
foreach($email as $user){
$sender=(is_array($name))?$name[$i] : $name;
if(empty($name))$name=JOOBI_SITE_NAME;
$this->address($user, $sender );
$sadmin[]=array('email'=> $user, 'name'=>$sender );
}}else{
if(empty($name))$name=JOOBI_SITE_NAME;
$sadmin=array('email'=> $email, 'name'=>$name );
}}
$status=$this->_sendAnMail($sadmin, null, $subject, $bodyText, false, false, false);
$this->clear();
if($report){
$message=WMessage::get();
if($status){
$message->userS('1211280107OPVR');
}else{
$message->userE('1299236985FLIB');
$error=self::$_mailer->ErrorInfo;
$message->userE($error);
}}
return $status;
}
public function onlyReplaceTags($user,$mgid){
$this->_donotsend=true;
return $this->sendNow($user, $mgid, false);
}
function addParameter($name,$value=null){
if(empty($name)){
return;
}
if(is_array($name) OR is_object($name)){
foreach($name as $paramName=> $paramValue){
$this->_parameters->$paramName=$paramValue;
}
}else{
$this->_parameters->$name=$value;
}
}
public function setParameters($params){
$this->_parameters=$params;
}
public function setFrequency($frequencyO){
$this->_frequency=$frequencyO;
}
public function keepAlive($alive=true){
self::$_mailer->keepAlive($alive);
}
  public function address($email,$name=''){
$this->_to[]=array($email, $name );
  }
  public function replyTo($email,$name=''){
$this->_replyTo[]=array($email, $name );
  }
  public function CC($email,$name=''){
$this->_cc[]=array($email, $name );
  }
  public function BCC($email,$name=''){
$this->_bcc[]=array($email, $name );
  }
  public function addSender($email,$name=''){
      if( WPref::load('PEMAIL_NODE_STRICTSENDER')){
  $emailA=explode('@',$email );
  if( strpos( JOOBI_SITE, $emailA[1])===false){
  $this->adminW('The sender email is not part of the main domain, therefore the default one will be used instead.');
  return false;
  }  }
  $this->_sender=array($email, $name );
  }
public function addFile($path,$name='',$encoding='base64',$type='application/octet-stream'){
return self::$_mailer->AddAttachment($path, $name, $encoding, $type );
}
  public function getBody(){
  return self::$_mailer->getBody();
  }
function clear(){
$this->_bcc=array();
  $this->_cc=array();
  $this->_to=array();
  $this->_replyTo=array();
  $this->_donotsend=false;
}
}
