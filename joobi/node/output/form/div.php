<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class WForm_Corediv extends WForms_default {
function create(){
return true;
}
function show(){
return true;
}
function start(&$frame,$params=null){
if(!empty($params->parent)) return parent::start($frame, $params );
$frame->startPane($params );
}
public function addElementToField(&$frame,$params=null,$HTML=null){
$frame->_data=$params;
if(empty($HTML)) return;
if('edit'==WPref::load('PMAIN_NODE_DIRECT_MODIFY')){
$outputDirectEditC=WClass::get('output.directedit');
$editButton=$outputDirectEditC->editView('form',$this->yid, $this->element->fid, 'form-layout');
if(!empty($editButton))$params->text=$editButton . $params->text;
}elseif('translate'==WPref::load('PMAIN_NODE_DIRECT_MODIFY')){
}
$fieldsetHMTL='<div';
if(!empty($this->element->classes ))$fieldsetHMTL .=' class="'.$this->element->classes.'">';
$fieldsetHMTL .=$HTML.'</div>';
$frame->add($fieldsetHMTL );
$frame->line($this->element );
$frame->body();
}
}