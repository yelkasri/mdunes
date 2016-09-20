<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class WListing_Coreradio extends WListings_default{
public function createHeader(){
if(empty($this->element->align))$this->element->align='center';
if(empty($this->element->width))$this->element->width='30px';
return false;
}
function create(){
if(!empty($this->element->autoselectpremium ) && $this->getValue('premium')){
$this->checked=1;
WGlobals::set('radioCheckNoNeedListConfirm', true);
}
if(!empty($this->element->lien)){
$iconO=WPage::newBluePrint('icon');
$iconO->icon=(empty($this->checked)?'circle-o' : 'circle');$iconO->color=(empty($this->checked)?'muted' : 'primary');$iconO->size='large';
$this->content=WPage::renderBluePrint('icon',$iconO );
}else{
$this->content='<input type="radio" id="em'.$this->line.'" value="'.$this->value.'" name="'.$this->name.'[]" ';
if($this->checked)$this->content .='checked="checked" ';
$this->content .='/>';
}
return true;
}
public function advanceSearch(){
return false;
}
public function searchQuery(&$model,$element,$searchedTerms=null,$operator=null){
}
}
