<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class Email_Queue_class extends WClasses {
private $_queueToDeleteA=array();
private $_buildReportA=array();
private $_noMoreEmails=false;
public function processQueue($report=false,$mgid=0){
$emailQueueM=WModel::get('email.queue');
if(!empty($mgid))$emailQueueM->whereE('mgid',$mgid );
$emailQueueM->where('senddate','<', time());
$emailQueueM->whereE('publish', 1 );
$emailQueueM->whereE('status', 1 );
$emailQueueM->orderBy('priority','ASC');
$emailQueueM->orderBy('senddate','ASC');
$max=WPref::load('PEMAIL_NODE_QUEUE_MAX_EMAIL');
if($max < 1)$max=60;
$emailQueueM->setLimit($max );
$allQueueA=$emailQueueM->load('ol');
if(empty($allQueueA )){
if($report)$this->userN('1455702137KJRR');
return $this->_finishedProcessingQueue();
}
WTools::increasePerformance();
@ini_set('default_socket_timeout', 10 );
@ignore_user_abort(true);
$this->_queueToDeleteA=array();
$mail=WMail::get();
$emailQueueparamsO=WObject::get('email.queueparams');
foreach($allQueueA as $oneQueue){
if( 6==$oneQueue->type){
$this->_endNewsletterSending($oneQueue->qid, $oneQueue->mgid );
continue;
}
if(!empty($oneQueue->params)){
 $emailQueueparamsO->setSQLParams($oneQueue->params );
 $emailQueueparamsO->decodeQueue();
 $params=$emailQueueparamsO->getMailParams();
 $freqeuncyO=$emailQueueparamsO->getMailFrequency();
 if(!empty($params))$mail->setParameters($params );
}
 $status=$mail->sendNow($oneQueue->uid, $oneQueue->mgid, $report );
 $reportA=$mail->getReportA();
 if($status){
 if(!empty($reportA['success'])){
 if(!isset($this->_buildReportA['success']))$this->_buildReportA['success']=array();
 $this->_buildReportA['success']=array_merge($this->_buildReportA['success'], $reportA['success'] );
 }
 if(!empty($freqeuncyO)){
 $this->_manageFrequency($oneQueue, $freqeuncyO );
 }else{
 $this->_queueToDeleteA[]=$oneQueue->qid;
 }
 }else{
 if(!empty($reportA['failed']))$this->_buildReportA['failed']=array_merge($this->_buildReportA['failed'], $reportA['failed'] );
 }
}
$this->_deleteQueue();
return $this->_finishedProcessingQueue();
}
public function getReportA(){
return $this->_buildReportA;
}
public function isFinished(){
return $this->_noMoreEmails;
}
private function _finishedProcessingQueue(){
$this->_noMoreEmails=true;
return true;
}
private function _endNewsletterSending($qid,$mgid){
if(!WExtension::exist('mailing.node')) return false;
$mailingM=WModel::get('mailing');
$mailingM->whereE('mgid',$mgid );
$mailingM->setVal('status', 50 );
$mailingM->update();
$emailQueueM=WModel::get('email.queue');
$emailQueueM->noValidate();
$emailQueueM->delete($qid );
$mailingQueueC=WClass::get('mailing.notification');
$mailingQueueC->endSending($mgid );
}
private function _manageFrequency($oneQueue,$frequencyO){
if(empty($frequencyO) || empty($oneQueue)) return false;
if(!empty($frequencyO->period)){
if(!empty($frequencyO->endDate)){
if(( time() + $frequencyO->period ) > $frequencyO->endDate){
$this->_queueToDeleteA[]=$oneQueue->qid;
return false;
}
}
$newSendDate=$oneQueue->senddate + $frequencyO->period;
$emailQueueM=WModel::get('email.queue');
$emailQueueM->whereE('qid',$oneQueue->qid );
$emailQueueM->setVal('senddate',$newSendDate );
$emailQueueM->update();
}
}
private function _deleteQueue(){
if(empty($this->_queueToDeleteA )) return false;
$emailQueueM=WModel::get('email.queue');
$keep=WPref::load('PEMAIL_NODE_KEEP_EMAIL');
if($keep){
$emailQueueM->whereIn('qid',$this->_queueToDeleteA );
$emailQueueM->setVal('status', 9 );
$emailQueueM->update();
}else{
$emailQueueM->noValidate();
return $emailQueueM->delete($this->_queueToDeleteA );
}
}
}
