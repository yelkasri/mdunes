<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class WRender_Listradio_blueprint extends Theme_Render_class {
public function render($data){
static $count=1;
if(empty($data->listA)) return '';
if(empty($data->tagID ))$data->tagID=$data->tagName;
$optionGroup=false;
if($data->colnb > 0){
$columnCount=true;
}else{
$columnCount=false;
}
if(isset($this->classes))$data->tagAttributes .=' class="'.$this->classes.'"';
if(isset($this->style))$data->tagAttributes .=' style="'.$this->style.'"';
if(isset($this->align))$data->tagAttributes .=' align="'.$this->align.'"';
$typeSlect='checked="checked"';
if($data->radioType){
$typeHTML='checkbox';
$nameHTML=$data->tagName.'[]';
}else{
$typeHTML='radio';
$nameHTML=$data->tagName;
}
$icol=1;
$html='';
if($data->radioStyle=='checkBox' && $columnCount){
$html .='<div class="col-sm-10">';
$html .='<div class="checkBoxAlign">';
$isCheckBox=true;
}else{
$isCheckBox=false;
}
if(!empty($data->requiredCheked)){
$myText=WGlobals::get('requireCheckedText','','global');
$onClick='onclick="'.WView::checkTerms( count($data->listA), $myText ).'"';
}else{
$onClick='';
}
foreach($data->listA as $key=> $value){
$tagIDVal=$data->tagID.'_'.$key;
if($data->arrayType){
$listKey=$key;
$listValue=$value;
}else{
$valKey=$data->propertyKey;
$listKey=$value->$valKey;
$valVal=$data->propertyText;
$listValue=$value->$valVal;
}
if( substr($listValue, 0, 2 )=='--'){
if($optionGroup){
$html .='</fieldset>'.$this->crlf ;
}
$listValue=trim( substr($listValue, 2, strlen($listValue)-2 ));
$html .='<fieldset id="'.$data->tagID.'" class="'.$classes.'"><legend>'.$listValue.'</legend>';
$optionGroup=true;
}else{
$extra='';
$selected=false;
if(is_array($data->selected )){
if( in_array($listKey, array_values($data->selected))){
$selected=true;
}
}else{
if($listKey==$data->selected){
$selected=true;
}
}
if($selected)$extra .=$typeSlect;
if($data->disable){
$extra .=' disabled="disabled"';
}
if(!empty($value->color)){
switch($value->color){
case 'green':
$color2Use='success';
break;
case 'red':
$color2Use='danger';
break;
case 'yellow':
$color2Use='info';
break;
case 'orange':
$color2Use='warning';
break;
default:
$color2Use='primary';
break;
}
}else{
$color2Use='primary';
}
if(!$isCheckBox){
$color2Use='default';
if($selected)$classLabel='btn active btn-'.$color2Use;
else $classLabel='btn btn-default';
}
$html .='<label';
if(!empty($classLabel))$html .=' class="'.$classLabel.'"';
$html .=' for="'.$tagIDVal.'">';
$html .='<input type="'.$typeHTML.'" name="'.$nameHTML.'" id="'.$tagIDVal.'" value="'.$listKey.'" '.$data->tagAttributes.' '.$extra . $onClick.'/>';
$html .=$listValue;
$html .='</label>'.WGet::$rLine;
$html .=WGet::$rLine;
}
if($data->radioStyle=='checkBox' || !$data->radioType){
if($columnCount){
if($data->colnb !=$icol && intval($icol/$data->colnb)===$icol/$data->colnb){
if($isCheckBox){
$html .='</div><div class="checkBoxAlign">';
}else{
$html .='</div><div class="radioAlign">';
}
}
$icol++;
}
}else{
if($columnCount){
if( intval($icol/$data->colnb)===$icol/$data->colnb)$html .='<br />';
$icol++;
}
}
}
if($isCheckBox)$html .='</div></div>';
$html .=WGet::$rLine;
$html=($optionGroup )?'</fieldset>'.$html : $html;
switch($data->radioStyle){
case 'checkBox':
break;
case 'checkBoxMultipleSelect':
if($data->colnb < 4)$data->colnb=4;
$heightBigBox=$data->colnb * 26;
$html='<div class="radioAlign">'.$html.'</div>';
$html='<div class="checkBoxMultipleSelect" style="height:'.$heightBigBox.'px;">'.$html.'</div>';
break;
case 'radioButton':
default:
$extraDisable=($data->disable?' disabled' : '');
$html='<div class="btn-group btn-toggle'.$extraDisable.'" data-toggle="buttons">'.$html.'</div>';
break;
}
return $html;
}
}
