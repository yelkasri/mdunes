<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class WListing_Coredatetime extends WListings_default{
protected $dateFormat='datetime';
function create(){
if(empty($this->value) || $this->value==4200000003 || substr($this->value, 0, 10)=='0000-00-00'){
return true;
}
if(isset($this->element->formatdate) && ($this->element->formatdate > 0 )){
if(!is_numeric($this->value)){
$this->value=WApplication::stringToTime($this->value );
}
switch($this->element->formatdate){
case 73:
$this->value=WTools::durationToString($this->value, true);
break;
case 77:
$this->value=WTools::durationToString($this->value );
break;
case 79:
$mainDateCountDown=WClass::get('main.date');
$this->value=$mainDateCountDown->countDown($this->value );
break;
default :
if(!isset($this->noTimeZone)){
static $timezone=null;
if(!isset($timezone)){
$timezone=WUser::timezone();
}$this->value=$this->value + $timezone;
}
$tzExact=true;
if( in_array($this->element->formatdate, array( 2, 5, 6, 8, 9 ))){
$tzExact=WUser::get('_tzExact');
}
$date2Use=WTools::dateFormat($this->element->formatdate );
$this->value=WApplication::date($date2Use, $this->value, ! $tzExact );
break;
}
}
$this->content .=(string)$this->value;
return true;
}
public function advanceSearch(){
WExtension::includes('main.calendar');
$lid=$this->element->lid.'_s';
$text=WText::t('1206732366OVMC');
$this->content=$this->_renderDateFormHTML($lid, $text );
$this->content .='<br />';
$lid=$this->element->lid.'_e';
$text=WText::t('1206732366OVMF');
$this->content .=$this->_renderDateFormHTML($lid, $text );
return true;
}
private function _renderDateFormHTML($lid,$text){
$formats=array();
$formats['dateonly']=array('php'=> "Y-m-d", 'js'=> "yyyy-mm-dd", 'default'=> "0000-00-00", 'hour'=>'false');
$formats['datetime']=array('php'=> "Y-m-d H:i", 'js'=> "yyyy-mm-dd hh:ii", 'default'=> "0000-00-00 00:00", 'hour'=>'true');
$name='advsearch['.self::$complexMapA[$lid] .']';
$defaultValue=WGlobals::getUserState( self::$complexSearchIdA[$lid] , self::$complexMapA[$lid], '','array','advsearch');
$idLabel=Output_Doc_Document::$advSearchHTMLElementIdsA[$lid];
$html=$text.': <input type="datetime" name="'.$name .'" id="'.$idLabel.'" value="'.$defaultValue.'" />';
$html .='<input type="button" value="" class="calendarDash" onclick="displayCalendar(document.getElementById(\''.$idLabel.'\'),\''.$formats[$this->dateFormat]['js'].'\',this,'.$formats[$this->dateFormat]['hour'].',\''.JOOBI_URL_INC .'main/calendar/images/\')">';
if(!empty($this->element->resetbutton)){
$altText=WText::t('1206732365OQJK');
$replace='document.getElementById(\''.$this->idLabel.'\').value=\''.$formats[$this->dateFormat]['default'].'\'';
$html .=' <a class="calendarDashReset" href="#" alt="'.WGlobals::filter($altText, 'string'). '" title="'.$altText.'" onclick="'.$replace.'; return false;"><img src="'. JOOBI_URL_JOOBI_IMAGES.'toolbar/16/cancel.png" border="0"/></a>';
}
return $html;
}
public function searchQuery(&$model,$element,$searchedTerms=null,$operator=null){
$lid=$element->lid.'_s';
Output_Doc_Document::$advSearchHTMLElementIdsA[$lid]='srchwz_'.$lid;
$this->createComplexIds($lid, $element->map.'_'.$element->sid.'_s');
if(!empty($searchedTerms)){
$defaultValue=$searchedTerms;
}else{
$defaultValue=WGlobals::getUserState( self::$complexSearchIdA[$lid] , self::$complexMapA[$lid], '','array','advsearch');
}
if(!empty($defaultValue) && is_numeric($defaultValue)){
$valueToUse=WApplication::stringToTime($defaultValue );
$model->where($element->map, '>=',$valueToUse, $element->asi );
}
$lid=$element->lid.'_e';
Output_Doc_Document::$advSearchHTMLElementIdsA[$lid]='srchwz_'.$lid;
$this->createComplexIds($lid, $element->map.'_'.$element->sid.'_e');
if(!empty($searchedTerms)){
$defaultValue=$searchedTerms;
}else{
$defaultValue=WGlobals::getUserState( self::$complexSearchIdA[$lid] , self::$complexMapA[$lid], '','array','advsearch');
}
if(!empty($defaultValue) && is_numeric($defaultValue)){
$valueToUse=WApplication::stringToTime($defaultValue );
$model->where($element->map, '<=',$valueToUse, $element->asi );
}
}
}
