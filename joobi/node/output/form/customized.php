<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class WForm_Corecustomized extends WForms_default {
private $_externalClass='';
function create(){
if(!empty($this->element->filef)){
if(!empty($this->element->currentClassName)){
$fileName=$this->element->currentClassName;
if( class_exists($fileName)){
$this->_externalClass=new $fileName;
}
}else{
$mwid=WView::get($this->yid, 'wid');$nodeName=WExtension::get($mwid, 'folder');
$fileName=(isset($this->element->filef ))?$nodeName.'.'.$this->element->filef : $this->map;
$elementName=$nodeName.'.form.'.$this->element->filef;
WView::includeElement($elementName, null, true);
$clNAme=ucfirst($nodeName). '_'.$this->element->filef.'_form';
$this->_externalClass=new $clNAme;
}
}
if(empty($this->_externalClass) || !is_object($this->_externalClass)) return false;
$funtionName=(isset($this->show)?'show' : 'create');
if( method_exists($this->_externalClass, $funtionName )){
$allProperties=get_object_vars($this );
foreach($allProperties as $key=> $properties){
if($key[0]!='_'){
if(isset($this->$key))$this->_externalClass->$key=$this->$key;
}}
$this->_externalClass->content='';
$contentHTML=$this->_externalClass->$funtionName();
if($contentHTML===true){
$this->content=$this->_externalClass->content;
}elseif($contentHTML===false){
return false;
}}else{
return false;
}
if(!empty($this->_externalClass->elementClassPosition))$this->elementClassPosition=$this->_externalClass->elementClassPosition;
return true;
}
function show(){
$this->show=true;
return $this->create();
}
}