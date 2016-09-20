<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class WListing_Corecheckbox extends WListings_default{
public function createHeader(){
if(empty($this->element->align))$this->element->align='center';
if(empty($this->element->width))$this->element->width='30px';
return false;
}
function create(){
if(!empty($this->element->autoselectpremium ) && $this->getValue('premium')){
$this->checked=1;
}
$this->content='<input type="checkbox" id="em'.$this->line.'" value="'.$this->value.'" name="'.$this->name.'[]" ';
if($this->checked)$this->content .='checked="checked" ';
$this->content .='/>';
return true;
}
public function advanceSearch(){
return false;
}
public function searchQuery(&$model,$element,$searchedTerms=null,$operator=null){
}
}
