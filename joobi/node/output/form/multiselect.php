<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class WForm_CoreMultiselect extends WForms_default {
public function create(){
if(!empty($this->element->readonly)) return $this->show();
if(!$this->_setup()) return false;
$dropdownPL=WView::picklist($this->_did );
$dropdownPL->params=$this->element;
$dropdownPL->name=$this->map;
$outype=$dropdownPL->_didLists[0]->outype;
$sid=$dropdownPL->params->sid;
$possibleMap=array('p_','c_','u_');
if(!$this->newEntry && substr($this->idLabel,0,2) !='x_'){
if( in_array( substr($this->idLabel,0,2), $possibleMap )){
$value=explode(',' , $this->value );
}else{
if(!isset($dropdownPL->_didLists[0]->mgdft)){
if(!empty($sid)){
$extModel=WModel::get($sid, 'object');
if($extModel->multiplePK()){
$pkey=WModel::get($this->modelID, 'pkey');
$extModel->whereE($pkey, $this->eid );
}else{
$extModel->whereE($extModel->getPK(), $this->eid );
}
$extModel->setLimit( 500 );
$value=$extModel->load('lra',$this->element->map );
}
}
}
}else{
if(!empty($this->value) && is_string($this->value) && in_array( substr($this->idLabel,0,2), $possibleMap )){
$value=explode('|_|' , $this->value );
}}
if(!isset($value))$value=array();
$dropdownPL->defaults=array($dropdownPL->_didLists[0]->did=> $value );
$this->content=$dropdownPL->display();
if(empty($this->content)) return false;
if(empty($this->element->disabled)){
$key='mlt-s_extra['.$sid.']['.WGlobals::count('mlt_s') .']';
$formObject=WView::form($this->formName );
$formObject->hidden($key, $this->element->map);
}
return true;
}
public function show(){
if(!$this->_setup()) return false;
if($this->_did > 0){
$dropdownPL=WView::picklist($this->_did );
$dropdownPL->params=$this->element;
$dropdownPL->name=$this->map;
$outype=$dropdownPL->_didLists[0]->outype;
if( substr($this->idLabel,0,2)=='p_'){
$values=explode( "," , $this->value );
}elseif(!empty($dropdownPL->params->sid)){
$extModel=WModel::get($dropdownPL->params->sid, 'object');
if(!empty($extModel)){
foreach($extModel->getPKs() as $pkey){
if($pkey !=$this->element->map){
$extModel->whereE($pkey,  WGlobals::getEID());
}}
$extModel->select($this->element->map );
$extModel->setLimit( 10000 );
$values=$extModel->load('lra');
}
}
if(!empty($values)){
$dropdownPL->defaults=$values ;
$returnedObj=$dropdownPL->displayOne();
$this->content=$returnedObj->content;
if($returnedObj->status===false) return false;
if(!empty($returnedObj->value) && !is_array($returnedObj->value)){
$formObject=WView::form($this->formName );
$formObject->hidden($this->map, $returnedObj->value );
}
}
}return true;
}
private function _setup(){
if($this->element->did > 0){
$this->_did=$this->element->did;
}else{
return false;
}return true;
}
}