<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Email_Queueparams_object extends WClasses {
public $parameters=null;
public $frequency=null;
private $_queueObject=null;
private $_queueEncoded='';
public function setMailParams($parameters){
$this->parameters=$parameters;
}
public function getMailParams(){
return $this->_queueObject->parameters;
}
public function setMailFrequency($frequency){
$this->frequency=$frequency;
}
public function getMailFrequency(){
if(!empty($this->_queueObject->frequency)) return $this->_queueObject->frequency;
else return 0;
}
public function setSQLParams($params){
$this->_queueEncoded=$params;
}
public function encodeQueue(){
 if(!empty($this->parameters)){
 if(!isset($this->_queueObject))$this->_queueObject=new stdClass;
 $this->_queueObject->parameters=$this->parameters;
 }
 if(!empty($this->frequency )){
 if(!isset($this->_queueObject))$this->_queueObject=new stdClass;
 $this->_queueObject->frequency=$this->frequency;
 }
 if(!empty($this->_queueObject)){
 if( version_compare( PHP_VERSION, '5.2') < 0){
 $this->_queueEncoded=serialize($this->_queueObject );
 }else{
 $this->_queueEncoded=json_encode($this->_queueObject );
 } }else{
 $this->_queueEncoded='';
 }
 return $this->_queueEncoded;
}
public function decodeQueue(){
 if(!empty($this->_queueEncoded)){
 if( version_compare( PHP_VERSION, '5.2') < 0){
 $this->_queueObject=unserialize($this->_queueEncoded );
 }else{
 $this->_queueObject=json_decode($this->_queueEncoded );
 } }
}
}