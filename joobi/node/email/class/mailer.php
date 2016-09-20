<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
WLoadFile('api.addon.'.JOOBI_FRAMEWORK.'.mailer');
class Email_Mailer_class extends Joobi_Mailer {
private $_parameters=null;
var $embedimages='';
var $processTags=true;
private $_user=null;
var $sendMultiplePart='';
private $_emailAddAddress=array();
private $_emailAddReplyTo=array();
private $_emailAddCC=array();
private $_emailAddBCC=array();
private $_errorInfo2='';
private $_mailerInfoO=null;
private $_trackLinks=false;
private $_trackingParams=null;
private $_sentSubject='';
private $_sentBody='';
private $_sentAltBody='';
function __construct(){
if(!$this->_validMailer){
return false;
}
$useCMS=WPref::load('PEMAIL_NODE_USECMS');
$emailLoadmailerC=WClass::get('email.loadmailer');
if(empty($useCMS)){
$mailerInstance=$emailLoadmailerC->getMailer('email','framework');
$this->_mailerInfoO=$mailerInstance->getMailerInformation();
}elseif($useCMS==1){
$mailerInstance=$emailLoadmailerC->getMailer('email','preferences');
$this->_mailerInfoO=$mailerInstance->getMailerInformation();
}elseif($useCMS==9){
$mailerInstance=$emailLoadmailerC->getMailer('email');
$this->_mailerInfoO=$mailerInstance->getMailerInformation();
}else{
$mailerInstance=$emailLoadmailerC->getMailer('email','framework');
$this->_mailerInfoO=$mailerInstance->getMailerInformation();
}
if(!$this->_mailerInfoO->addnames)$this->FromName='';
$this->addSender($this->_mailerInfoO->senderemail, $this->_mailerInfoO->sendername );
$this->Sender=trim($this->_mailerInfoO->bouncebackemail );
if(empty($this->Sender))$this->Sender='';
$this->embedimages=$this->_mailerInfoO->embedimages;
switch( strtoupper( substr( PHP_OS, 0, 3 ))){
case "WIN":
$this->LE="\r\n";
break;
case "MAC":
case "DAR":
$this->LE="\r";
break;
default:
$this->LE="\n";
break;
}
$useDKIM=false;
switch ($this->_mailerInfoO->mailer){
case 'smtp':
$this->IsSMTP();
$this->Host=$this->_mailerInfoO->smtphost;
$this->Port=$this->_mailerInfoO->smtpport;
$this->SMTPAuth=$this->_mailerInfoO->smtpauth;
$this->Username=$this->_mailerInfoO->smtpuser;
$this->Password=$this->_mailerInfoO->smtppass;
$this->SMTPSecure=$this->_mailerInfoO->smtpsecure;
if(empty($this->SMTPSecure))$this->SMTPSecure='';
if(empty($this->Sender))$this->Sender=$this->_mailerInfoO->smtpuser;
break;
case 'sendmail':
$useDKIM=true;
$this->IsSendmail();
$this->SendMail=$this->_mailerInfoO->sendmail;
if(empty($this->SendMail))$this->SendMail='/usr/sbin/sendmail';
break;
case 'qmail':
$useDKIM=true;
$this->IsQmail();
break;
default:
$useDKIM=true;
$this->IsMail();
break;
}
$dkim=WPref::load('PEMAIL_NODE_MAILERDKIMYESNO');
if($useDKIM && $dkim){
$keyloc=PEMAIL_NODE_DKIMKEYLOC;
$passphrase=PEMAIL_NODE_DKIMPASSPHRASE;
if(!empty($keyloc) && !empty($passphrase)){
$this->DKIM_domain=WGlobals::get('SERVER_NAME','','server');
$this->DKIM_private=JOOBI_DS_ROOT . $keyloc ;
$this->DKIM_selector='phpmailer';
$this->DKIM_passphrase=$passphrase;
$this->DKIM_CANON='simple';
}}
$this->CharSet=$this->_mailerInfoO->charset;
if(empty($this->CharSet))$this->CharSet='utf-8';
$this->SetLanguage('en',$this->PluginDir.'language'.DS );
$this->sendMultiplePart=$this->_mailerInfoO->multiplepart;
$this->Encoding=$this->_mailerInfoO->encodingformat;
if(empty($this->Encoding))$this->Encoding='binary';
$this->Hostname=$this->_mailerInfoO->hostname;
$this->WordWrap=intval($this->_mailerInfoO->wordwrapping );
return true;
}
public function validMailer(){
return $this->_validMailer;
}
function sendUser($uid,$report=true){
$this->_emailAddAddress=array();
$this->_emailAddReplyTo=array();
$this->_emailAddCC=array();
$this->_emailAddBCC=array();
$errorUser=false;
$userID=0;
if( is_numeric($uid) && $uid>0){
$user=WUser::get('data',$uid );
$this->_user=$user;
if(empty($user->email))$errorUser=true;
else{
$email=$user->email;
$name=(!empty($user->name)?$user->name : $user->username );
$userID=$uid;
}}elseif(is_object($uid) && !empty($uid->email)){
if(!empty($uid->username) && $uid->username !=$uid->email){
$name=$uid->username;
}elseif(!empty($uid->name)){
$name=$uid->name;
}else{
$name=$uid->email;
}
$this->_user=$uid;
$email=$uid->email;
if(!empty($uid->uid ))$userID=$uid->uid;
}elseif(is_string($uid) && !empty($uid)){
$email=$uid;
$name=$email;
} else $errorUser=true;
if($errorUser){
$mess=WMessage::get();
$mess->codeE('Wrong user in function sendUser in class node/mail/mailer.php');
$mess->codeE('Two possibilities, either you specified a wrong uid or you are not login and try to send an email with uid=0 ');
return false;
}
$this->address($email , $name, $userID );
$status=$this->sendMail($report );
return $status;
}
public function sendMail($report=true){
if(!$this->_validMailer ) return false;
$message=WMessage::get();
if(empty($this->Subject) || (empty($this->Body) && empty($this->AltBody))){
$this->ErrorInfo='There is no Subject or Body in this email';
if($report)$message->userW('1230529128EPES');
return false;
}
if($this->processTags){
$tag=WClass::get('output.process');
$tag->setParameters($this->_parameters );
$replace=array( &$this->Subject, &$this->Body, &$this->AltBody );
$tag->replaceTags($replace, $this->_user );
if(!empty($this->_emailAddAddress[0]->email))$subcriberID=$this->_emailAddAddress[0]->email;
if(!empty($this->_emailAddAddress[0]->uid))$subcriberID .='|'.$this->_emailAddAddress[0]->uid;
if(!empty($subcriberID))$this->addCustomHeader( "X-SubscriberID: ".base64_encode($subcriberID ));
}
if(!empty($this->AltBody)){
$this->AltBody=preg_replace('#(href|src|action)[ ]*=[ ]*\"(?!(https?://|\#|mailto:))(?:\.\./|\./|/)?#','$1="'.JOOBI_SITE,$this->AltBody );
}
$this->Body=preg_replace('#(href|src|action)[ ]*=[ ]*\"(?!(https?://|\#|mailto:))(?:\.\./|\./|/)?#','$1="'.JOOBI_SITE,$this->Body );
if($this->ContentType=='text/html'){
if($this->sendMultiplePart){
if(empty($this->AltBody)){
$this->AltBody=$this->getTextBody($this->Body);
}else{
$this->AltBody=$this->getTextBody($this->AltBody);
}}else{
$this->AltBody='';
}}else{
$this->Body=$this->getTextBody($this->Body );
}
if(!empty($this->_donotsend)) return $this;
if(empty($this->_emailReplyTo) && empty($this->ReplyTo)){
$this->replyTo($this->_mailerInfoO->replyemail, $this->_mailerInfoO->replyname );
}
if(!empty($this->embedimages) && $this->ContentType=='text/html'){
$embedResult=$this->_embedImages();
if(!$embedResult && $report && $this->checkUserCanSeeMailMessage()){
$myerror=$this->ErrorInfo;
$message->userW($myerror );
}}
ob_start();
$result=$this->Send();
$warnings=ob_get_clean();
if(!empty($warnings) && $report && $this->checkUserCanSeeMailMessage()){
$message->userW($warnings );
}
if(!$result)$this->_debugError();
$RECIPIENT=implode(',',$this->_getReceivers());
$SUBJECT=$this->Subject;
$logFailed=WPref::load('PEMAIL_NODE_STATISTICS_LOGFAIL');
if($logFailed && !empty($this->_parameters->mgid)){
$message->log('ERROR [MGID '.$this->_parameters->mgid.'][LGID '.$this->_parameters->lgid.'] '.$SUBJECT.' to '.$RECIPIENT.' | '.$this->ErrorInfo, 'email-failed-sending');
}
if($report){
if($this->checkUserCanSeeMailMessage()){
if(!$result){
$this->_debugError();
if(!empty($this->_errorInfo2)){
$ERROR=$this->_errorInfo2;
$message->userW($ERROR );
}
$error=$this->ErrorInfo;
$message->userE('1343261978OICB');
$myEMAIL=' "'.$SUBJECT.'" to '.$RECIPIENT.' | '.$error;
$message->userE($myEMAIL );
}else{
$message->userS('1418159454QZWX',array('$SUBJECT'=>$SUBJECT,'$RECIPIENT'=>$RECIPIENT));
}
}else{
if(!$result){
$message->userE('1343261978OICB');
}else{
$message->userS('1343261978OICC');
}}}
$this->clearAllData();
return $result;
}
public function addSender($email,$name=''){
if(!empty($email)){
$this->From=trim($email);
}if(!empty($name) && $this->_mailerInfoO->addnames)$this->FromName=trim($name );
}
  public function address($email,$name='',$uid=0){
  $this->_addEmailAddress($email, $name, 'AddAddress',$uid );
  }
  public function replyTo($email,$name='',$uid=0){
  $this->_addEmailAddress($email, $name, 'AddReplyTo',$uid );
  }
  public function CC($email,$name='',$uid=0){
$this->_addEmailAddress($email, $name, 'AddCC',$uid );
  }
  public function BCC($email,$name='',$uid=0){
  $this->_addEmailAddress($email, $name, 'addBCC',$uid );
  }
  public function getBody(){
return (!empty($this->Body)?$this->Body : $this->_sentBody );
  }
  public function getSubject(){
return (!empty($this->Subject)?$this->Subject : $this->_sentSubject );
  }
  public function getText(){
return (!empty($this->AltBody)?$this->AltBody : $this->_sentAltBody );
  }
public function createFromTrans(&$mailingTransM,$html=true,$trackOpenRate=false,$uid=0,$mgid=0){
$this->IsHTML($html );
$this->Subject=$mailingTransM->name;
$mailingTransM->ctext=trim($mailingTransM->ctext);
if($html || empty($mailingTransM->ctext)){
$this->Body=$this->_processLinks($mailingTransM, true);
$this->AltBody=$this->_processLinks($mailingTransM, false);
if(!empty($uid) && $trackOpenRate){
$posBody=strpos($this->Body, '</body>');
if(!empty($this->_trackingParams)){
$tracking=$this->_trackingParams;
}else{
$tracking=new stdClass;
}
if(!empty($mgid))$tracking->mgid=$mgid;
$tracking->uid=$uid;
$nt=WNetcom::get('simple');
$nt->send('','email','statistics',$tracking );
$imgTrack=$nt->getImageLink();
if($posBody !==false){
$this->Body=str_replace('</body>',$imgTrack.'</body>',$this->Body );
}else{
$this->Body .=$imgTrack;
}}
}else{
$this->Body=$this->_processLinks($mailingTransM, false);
}
if(!empty($mailingTransM->rmail)){
$this->replyTo($mailingTransM->rmail, $mailingTransM->rname);
}
if(!empty($mailingTransM->smail)){
$this->addSender($mailingTransM->smail, $mailingTransM->sname );
}
$this->addParameter('mgid',$mailingTransM->mgid );
$this->addParameter('lgid',$mailingTransM->lgid );
}
public function clearAllData(){
$this->ClearReplyTos();
$this->ClearAllRecipients();
$this->ClearAttachments();
$this->_sentSubject=$this->Subject;
$this->_sentBody=$this->Body;
$this->_sentAltBody=$this->AltBody;
$this->Subject='';
$this->Body='';
$this->AltBody='';
$this->ErrorInfo='';
$this->_errorInfo2='';
}
public function addParameter($name,$value=null){
if(empty($name)){
return;
}
if(!isset($this->_parameters))$this->_parameters=new stdClass;
if(is_array($name) || is_object($name)){
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
public function getTextBody($text){
$_replace=array('#< */? *br */? *>#i','#< */ *p *>#i','#< */ *tr *>#i');
$_paternlink='/<[ ]*a[ ]*href[ ]*=[ ]*"([^"]*)"[^>]*>([^<]*)<[ ]*\/[ ]*a[ ]*>/i';
return trim(@html_entity_decode(strip_tags(preg_replace($_replace, "\n", preg_replace($_paternlink,'${2} (${1} )',$text))),ENT_QUOTES,'UTF-8'));
}
public function keepAlive($alive=true){
if($alive){
$this->SMTPKeepAlive=true;
}else{
$this->SmtpClose();
}
}
public function returnObject($donotsend=true){
$this->_donotsend=$donotsend;
}
public function checkUserCanSeeMailMessage(){
static $haveAccess=array();
 if(!empty($this->_user->uid))$uid=$this->_user->uid;
 else {
 $uid=0;
 }
$loggedUID=WUser::get('uid');
if(isset($haveAccess[$loggedUID])) return $haveAccess[$loggedUID];
if(!empty($uid ) && $loggedUID==$uid)$haveAccess[$loggedUID]=true;
else {
$roleHelper=WRole::get();
if( WRole::hasRole('sadmin'))$haveAccess[$loggedUID]=true;
else $haveAccess[$loggedUID]=false;
}
return $haveAccess[$loggedUID];
}
public function setTracking($track,$trackingParams=null){
$this->_trackLinks=$track;
$this->_trackingParams=$trackingParams;
}
private function _processLinks($mailingO,$html){
static $redirectAvailable=null;
if(!isset($redirectAvailable))$redirectAvailable=WExtension::exist('redirect.node');
if(!$this->_trackLinks || !$redirectAvailable){
if($html){
return $mailingO->chtml;
}else{
return $mailingO->ctext;
}}
$redirectTrackC=WClass::get('redirect.track', null, 'class', false);
if(!empty($redirectTrackC)){
return $redirectTrackC->replaceLinkInMail($mailingO, $html, $this->_trackingParams );
}
return false;
}
private function _embedImages(){
preg_match_all("/(src|background)=\"(.*)\"/Ui", $this->Body, $images);
$result=true;
if(!empty($images[2])){
$mimetypes=array(
'bmp'=>  'image/bmp',
  'gif'=>  'image/gif',
  'jpeg' =>  'image/jpeg',
  'jpg'=>  'image/jpeg',
  'jpe'=>  'image/jpeg',
  'png'=>  'image/png',
  'tiff' =>  'image/tiff',
  'tif'=>  'image/tiff');
$allimages=array();
  foreach($images[2] as $i=> $url){
  if(isset($allimages[$url])) continue;
  $allimages[$url]=1;
    $path=str_replace(array(JOOBI_SITE,'/'),array(JOOBI_DS_ROOT,DS),$url);
$filename=basename($url);
$md5=md5($filename);
$cid='cid:'.$md5;
$positionDot=strrpos($filename, '.');
$ext=substr($filename, $positionDot+1 );
if(!isset($mimetypes[$ext])) continue;
$mimeType=$mimetypes[$ext];
if($this->AddEmbeddedImage($path, $md5, $filename, 'base64',$mimeType )){
$this->Body=preg_replace("/".$images[1][$i]."=\"".preg_quote($url, '/')."\"/Ui", $images[1][$i]."=\"".$cid."\"", $this->Body);
}else{
$result=false;
}
  }
}
return $result;
}
private function _getReceivers(){
$receivers=array();
foreach($this->_emailAddAddress as $to){
if(empty($to->name)){$receivers[$to->email]=$to->email;
}else{
$receivers[$to->email]=$to->name.' ('.$to->email.')';
}}
return $receivers;
}
  private function _addEmailAddress($email,$name='',$function,$uid=0){
  if(empty($this->_mailerInfoO->addnames))$name='';
  $obj=new stdClass;
  $obj->email=trim($email );
  $obj->name=$name;
  $obj->uid=$uid;
  $propertyName='_email'.$function;
  $this->$propertyName=array_merge($this->$propertyName, array($obj));
  $this->$function($email, $name );
  }
private function _debugError(){
$usersEmail=WClass::get('users.email');
if(!$usersEmail->validateEmail($this->From )){
$this->_errorInfo2 .=$this->From.' | '.WText::t('1230529129CVIJ');
}
if(!$usersEmail->validateEmail($this->Sender)){
$this->_errorInfo2 .=$this->Sender.' | '.WText::t('1230529129CVII');
}
$emailToCheck=array('_emailReplyTo','_emailTo','_emailAddCC','_emailAddBCC');
foreach($emailToCheck as $oneEmailToCheck){
if(!empty($this->$oneEmailToCheck)){
foreach($this->$oneEmailToCheck as $mySpecialSender){
if(!$usersEmail->validateEmail($mySpecialSender->email )){
$this->_errorInfo2 .=$mySpecialSender->email.' | '.WText::t('1230529129CVIK');
}}}
}
return false;
}
}