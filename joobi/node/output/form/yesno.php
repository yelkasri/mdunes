<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class WForm_Coreyesno extends WForms_default {
protected $defaultValues=array( 1, 0 );
protected $defaultImages=array();
protected $defaultLabel=array();
protected $colorA=array('success','danger');
function create(){
static $text=array();
$sign=get_class($this );
if(!isset($text[$sign])){
$yes=WText::t('1206732372QTKI');
$no=WText::t('1206732372QTKJ');
$aLabel=(!empty($this->defaultLabel))?$this->defaultLabel : array($yes, $no );
$defaultImages=(!empty($this->defaultImages))?$this->defaultImages :  array('yes','cancel');
$size=2 ;
for($index=0; $index < $size; $index++){
$label=$aLabel[$index];
$alt=$label;
$opt=$label;
$extraObj=new stdClass;
$extraObj->color=$this->colorA[$index];
$pub[]=WSelect::option($this->defaultValues[$index], $opt, 'value','text',$extraObj );
}
$text[$sign]=$pub;
}
$dropdownName=$this->map;
$dropdownTADID=WView::generateID('yesno',$this->element->fid );
$HTMLRadio=new WRadio();
$extraInfo='';
if(isset($this->element->disabled )){
$disabled=true;
}else{
$disabled=false;
}
if(!empty($this->element->exepicklist)){
$taskExcecution2Use=(!empty($this->element->exepicklisttask))?$this->element->exepicklisttask : 'listing';
 $paramsArray['validation']=true;
$tagAttribs='return '.WPage::actionJavaScript($taskExcecution2Use, $this->formName, $paramsArray );
 $tagAttribs=' onclick="'.$tagAttribs.'"';
}else{
$tagAttribs='';
}
$outputHTML=$HTMLRadio->create($text[$sign], $dropdownName, $tagAttribs, 'value','text',$this->value, $dropdownTADID, $disabled );
$this->content=$outputHTML . $extraInfo;
$this->element->forceTitle=true;
return true;
}
function show(){
static $text=array();
$formObject=WView::form($this->formName );
$formObject->hidden($this->map, $this->value );
$sign=get_class($this );
$index=(int)($this->value . $sign );
if(!isset($text[$index])){
$yes=WText::t('1206732372QTKI');
$no=WText::t('1206732372QTKJ');
$aLabel=(!empty($this->defaultLabel))?$this->defaultLabel : array($no, $yes );
$defaultImages=(!empty($this->defaultImages))?$this->defaultImages :  array('cancel','yes');
$label=$aLabel[$index];
$usedColor=!empty($this->colorA[$index])?$this->colorA[$index] : 'black';
$text[$index]='<span class="label label-'.$usedColor.'">'.$label.'</span>';
}
$this->content=$text[$index];
return true;
}
}
