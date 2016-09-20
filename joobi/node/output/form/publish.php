<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class WForm_Corepublish extends WForms_default {
protected $colorA=array('danger','success','info','default','warning');
function create(){
static $text=array();
if(!empty($this->element->infonly)) return $this->show();
$start=1;
$end=3;
if(!isset($text[$this->newEntry])){
$yes=WText::t('1206732372QTKI');
$publish=WText::t('1206732372QTKN');
$unpublish=WText::t('1206732372QTKO');
$no=WText::t('1206732372QTKJ');
$archive=WText::t('1206732375LZCH');
$trash=WText::t('1206732375LZCI');
$schedule=WText::t('1206732372QTKP');
$aValue=array( 2, 1, 0, 1, -2 );
$aLabel=array($schedule, $yes, $no, $archive, $trash );
$aLabelLegend=array($schedule, $publish, $unpublish, $archive, $trash );
$aImg=array('pending','publish','unpublish','archive','disabled');
for($index=$start; $index < $end; $index++){
$label=$aLabel[$index];
$labelLegend=$aLabelLegend[$index];
$opt=$label;
$extraObj=new stdClass;
$extraObj->color=$this->colorA[$index];
$pub[]=WSelect::option($aValue[$index], $opt, 'value','text',$extraObj );
}
$text[$this->newEntry]=$pub;
}
$HTMLRadio=new WRadio();
if(isset($this->element->disabled )){
$disabled=true;
if( is_string($this->element->disabled)){
$extraInfo='<blink><b><font color="orange"> '.$this->element->disabled.' '.WText::t('1209056511OBNE'). '</font></b></blink>';
}}else{
$disabled=false;
$extraInfo='';
}
 $dropdownTADID=substr($this->map, 6, strlen($this->map));
$dropdownTADID=str_replace( array('.',']','['), '_',$dropdownTADID );
$this->content=$HTMLRadio->create($text[$this->newEntry], $this->map, '','value','text',$this->value, $dropdownTADID, $disabled );
return true;
}
function show(){
static $text=array();
$formObject=WView::form($this->formName );
$formObject->hidden($this->map, $this->value );
$index=$this->value;
if(!isset($text[$index])){
$aValue=array( 0, 1, -1, -2, 2 );
$yes=WText::t('1206732372QTKI');
$no=WText::t('1206732372QTKJ');
$archive=WText::t('1206732375LZCH');
$trash=WText::t('1206732375LZCI');
$schedule=WText::t('1206732375LZCJ');
$aLabel=array($no, $yes,$archive,$trash, $schedule );
$aImg=array('unpublish','publish','archive','disabled','pending');
if(empty($aLabel[$index])) return false;
$label=$aLabel[$index];
$usedColor=!empty($this->colorA[$index])?$this->colorA[$index] : 'black';
$text[$index]='<span class="label label-'.$usedColor.'">'.$label.'</span>';
}
$this->content=$text[$index];
return true;
}
}