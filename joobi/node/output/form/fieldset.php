<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class WForm_Corefieldset extends WForms_default {
function create(){
return true;
}
function show(){
return true;
}
function start(&$frame,$params=null){
if(!empty($params->parent)) return parent::start($frame, $params );$frame->startPane($params );
}
public function addElementToField(&$frame,$params=null,$HTML=null){
$frame->_data=$params;
if(empty($HTML)) return;
if('edit'==WPref::load('PMAIN_NODE_DIRECT_MODIFY')){
$outputDirectEditC=WClass::get('output.directedit');
$editButton=$outputDirectEditC->editView('form',$this->yid, $this->element->fid, 'form-layout');
if(!empty($editButton))$params->text=$editButton . $params->text;
}elseif('translate'==WPref::load('PMAIN_NODE_DIRECT_MODIFY')){
$outputDirectEditC=WClass::get('output.directedit');
$editButton=$outputDirectEditC->translateView('form',$this->yid, $this->element->fid, $params->text );
if(!empty($editButton))$params->text=$editButton . $params->text;
}
if(!empty($this->element->style))$style=' style="'.rtrim($this->element->style, ';').';"';
else $style='';
$data=WPage::newBluePrint('panel');
$data->id=$this->element->namekey;
if(!empty($this->element->faicon))$data->faicon=$this->element->faicon;
if(!empty($this->element->color))$data->color=$this->element->color;
$data->header=$params->text;
$data->body=$HTML;
$data->style=$style;
$fieldsetHMTL=WPage::renderBluePrint('panel',$data );
$frame->cell($fieldsetHMTL, 'Fieldset');
$frame->line($this->element );
}
}