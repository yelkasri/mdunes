<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
 class Email_Scheduled_class extends WClasses {
 public function processScheduledMail($user,$mgid,$sendDate=0,$priority=null,$parameters=null,$frequency=null,$report=true){
if(empty($user)){
return false;
}
$uidA=array();
if(is_array($user)){
foreach($user as $oneUser){
$uid=$this->_checkReceivedUser($oneUser );
if(false===$uid ) continue;
$uidA[]=$uid;
}
}else{
$uid=$this->_checkReceivedUser($user );
if(false===$uid ) return false;
$uidA[]=$uid;
}
if(empty($uidA)) return false;
 $emailQueueparamsO=WObject::get('email.queueparams');
 $emailQueueparamsO->setMailParams($parameters );
 $emailQueueparamsO->setMailFrequency($frequency );
 $params=$emailQueueparamsO->encodeQueue();
 if(empty($priority) || !is_numeric($priority))$priority=100;
 if(!is_numeric($mgid)){
 $emailHelperC=WClass::get('email.helper');
 $mgid=$emailHelperC->loadMGID($mgid );
 }
 if(empty($sendDate))$sendDate=time();
 $queueDataA=array();
 foreach($uidA as $oneUID){
 $queueDataA[]=array($oneUID, $mgid, $sendDate, $priority, 1, 1, $params );
 }
 $emailQueueM=WModel::get('email.queue');
 $emailQueueM->setIgnore(true);
$status=$emailQueueM->insertMany( array('uid','mgid','senddate','priority','publish','status','params') , $queueDataA );
return $status;
 }
 private function _checkReceivedUser($user){
if( is_numeric($user ) || is_string($user)){
$uid=$this->_validateUserInfo($user );
if(false===$uid ) return false;
}elseif( is_object($user)){
if(!empty($user->uid)){
$uid=$this->_validateUserInfo($user->uid );
if(false===$uid ) return false;
}elseif(!empty($user->email)){
$uid=$this->_validateUserInfo($user->email );
if(false===$uid ) return false;
}else{
return false;
}
}else{
return false;
}
return $uid;
 }
 private function _validateUserInfo($user){
 $uid=WUser::get('uid',$user );
if(empty($uid)){
$this->adminE('The mail could not be sent because the user is unknown.');
return false;
}
return $uid;
 }
}
