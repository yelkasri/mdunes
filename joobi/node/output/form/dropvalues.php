<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
WView::includeElement('form.select');
class WForm_Coredropvalues extends WForm_select {
function create(){
$this->value=$this->_checkForValues();
return parent::create();
}
function show(){
return parent::show();
$formObject=WView::form($this->formName );
$formObject->hidden($this->map, $this->value );
$values=$this->_checkForValues();
$wz_join=WGlobals::get('jdropval',array(), 'global');
$notKnown=array();
$this->content='<ul>';
foreach($values as $val){
if(isset($wz_join[$this->element->did][$this->map][$val])){
$this->content .='<li>'.$wz_join[$this->element->did][$this->map][$val].'</li>';
}else{
$notKnown[]=$val;
}}
if(empty($notKnown)){
$this->content .='</ul>';
}else{
$sql=WModel::get('picklist','object');
$sql->whereE('did',$this->element->did );
$sql->whereE('publish', true);
$Ddown=$sql->load('o');
$sql=WModel::get('library.picklistvalues','object');
$sql->makeLJ('library.picklistvaluestrans','vid');
$sql->select('name', 1);
$sql->whereLanguage(1);
$sql->select('value');
$sql->whereE('did',$this->element->did );
$sql->whereE('publish', true);
$sql->whereIn('value',$notKnown );
$drops=$sql->load('ol');
if($drops===false) return false;
foreach($drops as $drop){
$wz_join[$this->element->did][$this->map][$drop->value]=$drop->name;
$this->content .='<li>'.$drop->name.'</li>';
}
WGlobals::set('jdropval',$wz_join, 'global');
$this->content .='</ul>';
}return true;
}
private function _checkForValues(){
$separators=array( 1=> '|_|');
$kay=false;
foreach($separators as $key=> $sep){
if(!strpos($this->value,$sep)===false)$kay=$key;
}
if($kay){
$values=explode($separators[$kay],$this->value);
}else{
$values[0]=$this->value;
}
return $values;
}
}
