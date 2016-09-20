<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Email_CoreYesnouser_form extends WForms_default {
function create(){
$aValue=array( 0, 2, 1 );
$aLabel=array( WText::t('1206732372QTKJ'), WText::t('1206732372QTKI'),WText::t('1231383373PREE'));
$aImg=array('cancel','yes','preferences');
foreach($aValue as $index=> $value){
$opt=$aLabel[$index]. WView::getLegend($aImg[$index], $aLabel[$index], 'standard',  $index );
$pub[]=WSelect::option($aValue[$index], $opt);
}
$text=$pub;
$dropdownName=$this->map;
 $dropdownTADID=substr($this->map, 6, strlen($this->map));
$dropdownTADID=str_replace( array('.',']','['), '_',$dropdownTADID );
$extraInfo='';
if(isset($this->element->disabled )){
$disabled=true;
if( is_string($this->element->disabled)){
$extraInfo='<blink><b><font color="orange"> '.$this->element->disabled.' '.WText::t('1209056511OBNE'). '</font></b></blink>';
}
}else{
$disabled=false;
}
$HTMLRadio=new WRadio();
$outputHTML=$HTMLRadio->create($text, $dropdownName, '','value','text' , $this->value, $dropdownTADID, $disabled );
$this->content=$outputHTML . $extraInfo;
return true;
}
}