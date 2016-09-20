<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class WForm_Coretypes extends WForms_default {
function create(){
$nodeName=WExtension::get($this->wid, 'folder');
$fileName=(isset($this->element->filef ))?$nodeName.'.'.$this->element->filef : $this->map;
$pos=strrpos($fileName, '.');
if($pos===false){
$dir=WExtension::get($this->nodeID, 'folder').'.'. $fileName;
}else{
$dir=$fileName;
}
$showMess=false;
$types=WType::get($dir, $showMess );
if( is_object($types) && !empty($types)){
  $list=$types->getList(true);
  if(!empty($list )){
$drop=array();
foreach($list as $key=> $val){
$drop[]=WSelect::option($key, $val );
}  }
  $HTMLDrop=new WSelect();
  $this->content=$HTMLDrop->create($drop, $this->map, null, 'value','text',$this->value );
}
return true;
}
function show(){
$nodeName=WExtension::get($this->wid, 'folder');
$fileName=(isset($this->element->filef ))?$nodeName.'.'.$this->element->filef : $this->map;
$pos=strrpos($fileName, ".");
if($pos===false){
$dir=WExtension::get($this->nodeID, 'folder').'.'. $fileName;
}else{
$dir=$fileName;
}
$types=WType::get($dir, false);
if(!empty($types))$this->content=$types->getName($this->value );
return true;
}
}