<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class Email_Statistics_class extends WClasses {
private $_status=null;
private $_user=null;
private $_mailingO=null;
public function recordSentMail($user,$mailingO,$status){
if(empty($user->uid)) return false;
if(empty($mailingO->mgid)) return false;
$this->_status=$status;
$this->_user=$user;
$this->_mailingO=$mailingO;
$this->_generalStatsSent();
$recordDetails=WPref::load('PEMAIL_NODE_STATISTICS_PERUSER');
if($recordDetails){
$this->_detailedStatsSent();
}
}
public function recordOpenMail($data){
if(empty($data->mgid) || empty($data->uid)) return false;
$emailStatisticsUserM=WModel::get('email.statisticsuser');
$emailStatisticsUserM->whereE('uid',$data->uid );
$emailStatisticsUserM->whereE('mgid',$data->mgid );
$emailStatisticsUserM->setVal('readdate', time());
$emailStatisticsUserM->setVal('read', 1 );
$emailStatisticsUserM->update();
}
public function recordLinkHits($data){
if(empty($data->mgid) || empty($data->uid)) return false;
$emailStatisticsUserM=WModel::get('email.statisticsuser');
$emailStatisticsUserM->whereE('uid',$data->uid );
$emailStatisticsUserM->whereE('mgid',$data->mgid );
$emailStatisticsUserM->setVal('read', 1 );
$emailStatisticsUserM->updatePlus('hitlinks', 1 );
$emailStatisticsUserM->update();
}
private function _detailedStatsSent(){
$emailStatisticsUserM=WModel::get('email.statisticsuser');
$emailStatisticsUserM->setVal('uid',$this->_user->uid );
$emailStatisticsUserM->setVal('mgid',$this->_mailingO->mgid );
if($this->_status){
if(!empty($this->_mailingO->html ))$emailStatisticsUserM->setVal('htmlsent', 1 );
else $emailStatisticsUserM->setVal('textsent', 1 );
} else $emailStatisticsUserM->setVal('failed', 1 );
if(!empty($this->_mailingO->sms))$emailStatisticsUserM->setVal('smssent', 1 );
$emailStatisticsUserM->setVal('created', time());
$emailStatisticsUserM->insertIgnore();
$total=$emailStatisticsUserM->affectedRows();
if(empty($total )){
$this->_updateDetailedStatsSent();
}
}
private function _updateDetailedStatsSent(){
$emailStatisticsUserM=WModel::get('email.statisticsuser');
$emailStatisticsUserM->whereE('uid',$this->_user->uid );
$emailStatisticsUserM->whereE('mgid',$this->_mailingO->mgid );
if($this->_status){
if(!empty($this->_mailingO->html ))$emailStatisticsUserM->updatePlus('htmlsent', 1 );
else $emailStatisticsUserM->updatePlus('textsent', 1 );
} else $emailStatisticsUserM->updatePlus('failed', 1 );
if(!empty($this->_mailingO->sms))$emailStatisticsUserM->updatePlus('smssent', 1 );
$emailStatisticsUserM->update();
}
private function _generalStatsSent(){
$emailStatisticsM=WModel::get('email.statistics');
$emailStatisticsM->whereE('mgid',$this->_mailingO->mgid );
$emailStatisticsM->updatePlus('total', 1 );
if($this->_status)$emailStatisticsM->updatePlus('sent', 1 );
else $emailStatisticsM->updatePlus('failed', 1 );
if(!empty($this->_mailingO->sms))$emailStatisticsM->updatePlus('smssent', 1 );
if(!empty($this->_mailingO->html ))$emailStatisticsM->updatePlus('htmlsent', 1 );
else $emailStatisticsM->updatePlus('textsent', 1 );
$emailStatisticsM->update();
$total=$emailStatisticsM->affectedRows();
if(empty($total )){
$this->_insertGeneralStatsSent();
}
}
private function _insertGeneralStatsSent(){
$emailStatisticsM=WModel::get('email.statistics');
$emailStatisticsM->setVal('mgid',$this->_mailingO->mgid );
$emailStatisticsM->setVal('total', 1 );
if($this->_status)$emailStatisticsM->setVal('sent', 1 );
else $emailStatisticsM->setVal('failed', 1 );
if(!empty($this->_mailingO->sms))$emailStatisticsM->setVal('smssent', 1 );
if(!empty($this->_mailingO->html ))$emailStatisticsM->setVal('htmlsent', 1 );
else $emailStatisticsM->setVal('textsent', 1 );
$emailStatisticsM->insertIgnore();
}
}
