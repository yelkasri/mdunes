<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class WForm_CoreSelect extends WForms_default {
protected $pickListStyle=0;
protected $picklistTypeSingle=true;
protected $formTypeShow=false;
public function create(){
if(!empty($this->element->readonly)) return $this->show();
if(!$this->_setup()) return false;
if($this->picklistTypeSingle){
return $this->_createSinglePicklist();
}else{
return $this->_createMultiplePicklist();
}
}
function show(){
if(!$this->_setup()) return false;
$this->formTypeShow=true;
if($this->picklistTypeSingle){
return $this->_showSinglePicklist();
}else{
return $this->_showMultiplePicklist();
}
}
private function _createSinglePicklist(){
$allowsOthers=WView::picklist($this->_did, '', null, 'allowothers');
if(!empty($this->element->exepicklist) || !empty($allowsOthers)){
if(!empty($allowsOthers) && empty($this->element->exepicklist)){
$taskExcecution2Use=WGlobals::get('task');
if( in_array($taskExcecution2Use, array('edit','add','new','save','apply')))$taskExcecution2Use='apply';
}else{
$taskExcecution2Use=(!empty($this->element->exepicklisttask))?$this->element->exepicklisttask : WGlobals::get('task');}$paramsArray=array();
$paramsArray['validation']=true;
$joobiRun='return '.WPage::actionJavaScript($taskExcecution2Use, $this->formName, $paramsArray );
}else{
$joobiRun='';
}
$viewParams=new stdClass;
$viewParams->yid=$this->yid;
$viewParams->sid=$this->modelID;
WGlobals::set('pikclsitValue_'.$this->_did, $this->value, 'global');
$dropdownPL=WView::picklist($this->_did, $joobiRun, $viewParams );
$dropdownPL->formName=$this->formName;
if(!empty($this->element->disabled )){
$dropdownPL->disabled=true;
}
if(empty($dropdownPL->_didLists[0])){
$message=WMessage::get();
$message->codeE('The picklist with ID :'.$this->_did.' is not available.  Check the form element with ID :'.$this->element->fid.' to solve the problem.  It can be either picklist is not publish or the Level does not match.');
return false;
}
$dropdownPL->params=$this->element;
$dropdownPL->name=$this->map;
if(empty($this->pickListStyle)){
if(!empty($dropdownPL->_didLists[0]->outype))$this->pickListStyle=$dropdownPL->_didLists[0]->outype;
}else{
$dropdownPL->setPickListType($this->pickListStyle );
}
if($this->element->editItem){
if( substr($this->element->map, 0, 2 )=='m['){
if(!empty($this->value )){
$convertValue=WTools::preference2Array($this->value );
$defaults=array($this->_did=> $convertValue );
}else{
$defaults=array();
}}else{
$defaults=(is_array($this->value))?$this->value : array($this->_did=> $this->value );
}
$dropdownPL->defaults=$defaults;
}else{
$dropdownPL->defaults=WController::getFormValue($this->element->map, $this->element->sid );
}
$dropdownPL->params->classes=(isset($this->element->classes ))?$this->element->classes : 'simpleselect';
$this->content=$dropdownPL->display();
$hasOtherInputBox=$dropdownPL->getOtherInputBox();
$getOtherSpecificValue=$dropdownPL->getOtherSpecificValue();
if(!empty($dropdownPL->_didLists[0]->parent)){
$chldsMap=JOOBI_VAR_DATA.'['.$this->element->sid.'][x][zwother_chlds]['.WView::picklist($dropdownPL->_didLists[0]->parent, '', null, 'did'). ']';
$this->content .='<input type="hidden" value="'.$this->element->map.'" name="'.$chldsMap.'" id="zwother_chlds_'.$this->idLabel.'">';
}
if(!empty($hasOtherInputBox) && ($hasOtherInputBox==$this->value || $getOtherSpecificValue )){
WGlobals::set('pikclsithasOtherInputBox_'.$this->_did, $hasOtherInputBox, 'global');
$OtherValue=($hasOtherInputBox !=$this->value?$this->value : '');
$map=str_replace('][','][x][zwother_',$this->map );
$hiddendftMap=str_replace('][','][x][zwother_dft_',$this->map );
$hiddenMap=str_replace('][','][x][zwother][',$this->map );
$prtsMap=str_replace('][','][x][zwother_prts]['.$this->element->map.']',$this->map );
$prtsMap=JOOBI_VAR_DATA.'['.$this->element->sid.'][x][zwother_prts]['.$this->element->map.']';
$parentAlreadyAdded=true;
WText::load('output.node');
$plhod=WText::t('1417074723POOW');
$this->content .='<input type="text" value="'.$OtherValue.'" size="30" placeholder="'.$plhod.'" class="inputbox" name="'.$map.'" id="zwother_'.$this->idLabel.'">';
$this->content .='<input type="hidden" value="'.$this->element->map.'" name="'.$hiddenMap.'" id="zwother_hdn_'.$this->idLabel.'">';
$this->content .='<input type="hidden" value="'.$hasOtherInputBox.'" name="'.$hiddendftMap.'" id="zwother_hdndft_'.$this->idLabel.'">';
$this->content .='<input type="hidden" value="'.$this->_did.'" name="'.$prtsMap.'" id="zwother_prts_'.$this->idLabel.'">';
}
if(!empty($dropdownPL->_didLists[0]->isparent )){
$currentMap=JOOBI_VAR_DATA.'['.$this->element->sid.'][x][zwother_crt]['.$this->element->map.']';
$this->content .='<input type="hidden" value="'.$this->value.'" name="'.$currentMap.'" id="zwother_crt_'.$this->idLabel.'">';
if(empty($parentAlreadyAdded)){
$prtsMap=JOOBI_VAR_DATA.'['.$this->element->sid.'][x][zwother_prts]['.$this->element->map.']';
$this->content .='<input type="hidden" value="'.$this->_did.'" name="'.$prtsMap.'" id="zwother_prts_'.$this->idLabel.'">';
}
}
$this->content=trim($this->content );
if(empty($this->content)) return false;
return true;
}
private function _createMultiplePicklist(){
$dropdownPL=WView::picklist($this->_did );
$dropdownPL->params=$this->element ;
$dropdownPL->name=$this->map;
if(!empty($this->element->disabled )){
$dropdownPL->disabled=true;
}
if(empty($this->pickListStyle)){
if(!empty($dropdownPL->_didLists[0]->outype))$this->pickListStyle=$dropdownPL->_didLists[0]->outype;
}else{
$dropdownPL->setPickListType($this->pickListStyle );
}
$sid=$dropdownPL->params->sid;
if($this->element->editItem){
if(!empty($this->value) && is_string($this->value) && ( strpos($this->value, '|_|') !==false || strpos($this->value, ',') !==false || strpos($this->value, '|') !==false)){
$value=WTools::preference2Array($this->value );
}else{
$possibleMap=array('p[','c[','u[','m[');
if(!$this->newEntry && substr($this->element->map ,0 ,2 ) !='x['){
if( in_array( substr($this->element->map, 0, 2 ), $possibleMap )){
$value=WTools::preference2Array($this->value );
}else{
if(!isset($dropdownPL->_didLists[0]->mgdft)){
$pkey=WModel::get($this->modelID, 'pkey');
if(!empty($sid)){
$extModel=WModel::get($sid, 'object');
$extModel->whereE($pkey,  $this->eid );
$extModel->setLimit( 500 );
$value=$extModel->load('lra',$this->element->map );
}
}
}
}else{
if(!empty($this->value) && is_string($this->value) && in_array( substr($this->element->map ,0 ,2 ), $possibleMap )){
$value=WTools::preference2Array($this->value );
}
}
}
if(!isset($value))$value=array();
$dropdownPL->defaults=array($dropdownPL->_didLists[0]->did=> $value );
}
$this->content=$dropdownPL->display();
if(empty($this->content)) return false;
if(empty($this->element->disabled)){
$key='mlt-s_extra['.$sid.']['.WGlobals::count('mlt_s') .']';
$formObject=WView::form($this->formName );
$formObject->hidden($key, $this->element->map );
}
return true;
}
private function _showSinglePicklist(){
$dropdownPL=WView::picklist($this->_did );
$dropdownPL->params=$this->element;
$dropdownPL->name=$this->map;
if(empty($dropdownPL->_didLists)){
$this->codeE('The picklist for the field '. $dropdownPL->name.' could not be loaded!');
return false;
}$this->pickListStyle=$dropdownPL->_didLists[0]->outype;
$defaults=(is_array($this->value))?array($this->_did=> $this->value ) : $this->value;
$dropdownPL->defaults=$defaults;
$returnedObj=$dropdownPL->displayOne();
$this->content=$returnedObj->content;
if($returnedObj->status===false) return false;
if(!empty($returnedObj->value) && !is_array($returnedObj->value)){
if( strpos($this->map, '][x][')===false){
$formObject=WView::form($this->formName );
$formObject->hidden($this->map, $returnedObj->value, false, true);
}}
$this->content=$this->linkOnAdvSearch($this->content );
$this->content=$this->addStyling($this->content );
return true;
}
private function _showMultiplePicklist(){
$dropdownPL=WView::picklist($this->_did );
$dropdownPL->params=$this->element;
$dropdownPL->name=$this->map;
if(empty($this->pickListStyle)){
if(!empty($dropdownPL->_didLists[0]->outype))$this->pickListStyle=$dropdownPL->_didLists[0]->outype;
}else{
$dropdownPL->setPickListType($this->pickListStyle );
}
if(!empty($this->value) && is_string($this->value) && ( strpos($this->value, '|_|') !==false || strpos($this->value, ',') !==false || strpos($this->value, '|') !==false)){
$values=WTools::preference2Array($this->value );
}else{
if( substr($this->idLabel,0,2)=='p_'){
$values=WTools::preference2Array($this->value );
}elseif(!empty($dropdownPL->params->sid)){
$extModel=WModel::get($dropdownPL->params->sid, 'object');
if(!empty($extModel)){
foreach($extModel->getPKs() as $pkey){
if($pkey!=$this->element->map){
$extModel->whereE($pkey,  WGlobals::getEID());
}
}
$extModel->select($this->element->map );
$extModel->setLimit( 10000 );
$values=$extModel->load('lra');
}
}
}
if(!empty($values)){
$dropdownPL->defaults=$values;
$returnedObj=$dropdownPL->displayOne();
$this->content=$returnedObj->content;
if($returnedObj->status===false) return false;
if(!empty($returnedObj->value) && !is_array($returnedObj->value)){
$formObject=WView::form($this->formName );
$formObject->hidden($this->map, $returnedObj->value );
}
$this->content=$this->linkOnAdvSearch($this->content );
}
return true;
}
private function _setup(){
if(!isset($this->element->map)){
return false;
}
if(!isset($this->element->selectstyle ))$this->element->selectstyle=0;
if(!empty($this->element->did)){
$this->_did=$this->element->did;
$this->pickListStyle=WView::picklist($this->_did, '', null, 'outype');
if( in_array($this->pickListStyle, array( 1, 3, 7, 8 ))){
$this->picklistTypeSingle=false;
}else{
$this->picklistTypeSingle=true;
}
}elseif(!empty($this->element->selectdid)){
$this->_did=$this->element->selectdid;
if(empty($this->element->selectype) || $this->element->selectype !=5){
if(empty($this->element->selectstyle) || ($this->element->selectstyle !=23 )){
$this->pickListStyle=6;
}else{
$this->pickListStyle=2;
}
}else{
$this->picklistTypeSingle=false;
switch($this->element->selectstyle){
case 25:
$this->pickListStyle=7;
break;
case 23:
$this->pickListStyle=3;
break;
case 1:
case 22:
default;
$this->pickListStyle=1;
break;
}
}
}else{
return false;
}
return true;
}
}