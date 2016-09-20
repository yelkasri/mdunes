<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class WListing_CoreTextlink extends WListings_default {
protected $_useStyle=true;
function create(){
if(empty($this->element->lien)){
if('http'==substr($this->value, 0, 4)){
$this->element->textlink=$this->value;
$this->element->lien=$this->value;
}else{
return true;
}
}
if(!empty($this->element->filef)){
$mwid=WView::get($this->yid, 'wid'); $nodeName=WExtension::get($mwid, 'folder');
$fileName=(isset($this->element->filef )?$nodeName.'.'.$this->element->filef : $this->map );
$extnalFucntion=WClass::get($fileName, null, 'listing');
if( method_exists($extnalFucntion, 'textLink')){
$extnalFucntion->element=&$this->element;
$extnalFucntion->data=$this->data;
$extnalFucntion->mapList=$this->mapList;
$this->element->textlink=$extnalFucntion->textLink();
}
}
if(!isset($this->element->textlink))$this->element->textlink=$this->element->name;
if($this->searchOn && isset($this->mywordssearched )){
if(is_array($this->mywordssearched)){
$this->element->textlink=preg_replace('#('.str_replace('#','\#', implode('|',$this->mywordssearched)). ')#i','<span class="search-highlight">$0</span>',$this->element->textlink );
}}
$myValue=(!empty($this->value )?$this->value : '0');
if(!empty($this->element->textlinkvalue ) && empty($this->value)) return false;
if(!empty($this->element->dsict) && $this->element->dsict==2){
$link=$this->element->textlink;
$link .=' <span class="badge">'.$myValue.'</span>';
}else{
$link=$this->element->textlink;
$pos=strrpos($link, "(value)" );
if($pos !==false){
$link=str_replace( "(value)", ' <span class="badge">'.$myValue.'</span>',$link );
}}
$this->content=$link;
if($this->_useStyle && isset($this->element->style))$this->content='<span style="'. $this->element->style .'">'.$this->content.'</span>';
return true;
}
}