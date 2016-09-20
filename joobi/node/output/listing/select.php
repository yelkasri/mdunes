<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class WListing_Coreselect extends WListings_default{
protected $elementA=array();
protected $pickListStyle=0;
protected $picklistTypeSingle=true;
public $yid=0;
function create(){
static $dropdownPL=array();
if(!is_string($this->pkeyMap)) return false;
if(!empty($this->element->did)){
$this->_did=$this->element->did;
}else{
return false;
}
$paramsPK=new stdClass;
$paramsPK->listing=true;
$dropdownPL[$this->element->map]=WView::picklist($this->_did, '',$paramsPK );
$pkField=explode('_',$this->pkeyMap );
$dropdownPL[$this->element->map]->params=$this->listing;
$dropdownPL[$this->element->map]->name='dyna_'.$this->name;
$dropdownPL[$this->element->map]->name2Use='dyna_'.$this->name.'['.$this->getValue($pkField[0], $pkField[1] ). ']';
$outype=$dropdownPL[$this->element->map]->_didLists[0]->outype;
if($outype=='1' || $outype=='3'){
$message=WMessage::get();
$message->codeW('You cannot have a multiselect dropdown type on a listing element.');
$this->content=$this->value;
}else{
$extras="{'em':'em". $this->line."','zval':this.value}";
$script=$this->elementJS($extras );
$dropdownPL[$this->element->map]->_onChange=$script;
$defaults=(is_array($this->value))?array($this->_did=> $this->value ) : $this->value;
$dropdownPL[$this->element->map]->defaults=$defaults ;
$HTML2Display=$dropdownPL[$this->element->map]->display();
$allVAluesA=$dropdownPL[$this->element->map]->getValues();
if( count($allVAluesA ) > 1){
$this->content=$HTML2Display;
}else{
$dropText=$dropdownPL[$this->element->map]->getPicklistProperties('text');
if(isset($dropText ))$this->content=$dropText;
}
}
return true;
}
public function advanceSearch(){
$lid=$this->element->lid;
$viewParams=new stdClass;
$viewParams->yid=$this->element->yid;
$viewParams->sid=$this->modelID;
$dropdownPL=WView::picklist($this->element->did, '',$viewParams );
$allListA=$dropdownPL->load();
if(empty($allListA)) return false;
$oneDropA=array();
$hasNone=false;
foreach($allListA as $oneKey=> $oneVal){
if(isset($oneVal->value) && isset($oneVal->text))$oneDropA[]=WSelect::option($oneVal->value, $oneVal->text );
if(empty($oneVal->value))$hasNone=true;
}
if(!$hasNone){
$defaultObj=new stdClass;
$defaultObj->value=0;
$defaultObj->text=WText::t('1206732410ICCJ');
array_unshift($oneDropA, $defaultObj );
}
$HTMLDrop=new WSelect();
$mapField='advsearch['.self::$complexMapA[$lid] .']';
$defaultValue=WGlobals::getUserState( self::$complexSearchIdA[$lid] , self::$complexMapA[$lid], '','array','advsearch');
$HTMLDrop->classes='simpleselect';
$this->content=$HTMLDrop->create($oneDropA, $mapField, null, 'value','text',$defaultValue, Output_Doc_Document::$advSearchHTMLElementIdsA[$lid] );
return true;
}
public function searchQuery(&$model,$element,$searchedTerms=null,$operator=null){
$lid=$this->element->lid;
$this->createComplexIds($lid, $element->map.'_'.$element->sid );
Output_Doc_Document::$advSearchHTMLElementIdsA[$lid]='srchwz_'.$lid;
if(!empty($searchedTerms)){
$defaultValue=$searchedTerms;
}else{
$defaultValue=WGlobals::getUserState( self::$complexSearchIdA[$lid] , self::$complexMapA[$lid], '','array','advsearch');
}
if(!empty($defaultValue)){
$model->whereSearch($element->map, $defaultValue, $element->asi, '=',$operator );
}
}
public function advanceSearchLinks($memory,$sessionKey,$controller,$task,$pickListType=''){
if(!empty($pickListType)){
return $this->_advanceSearchLinks($memory, $sessionKey, $controller, $task, $pickListType );
}
if(empty($this->element) && !empty($this->elementA)){
$this->element=new stdClass;
foreach($this->elementA as $keyE=> $oneE)$this->element->$keyE=$oneE;
}
$searchObjectO=new stdClass;
$searchObjectO->name=$this->element->name;
$searchObjectO->typeName=$this->element->typeName;
$searchObjectO->typeNode=$this->element->typeNode;
$searchObjectO->modelID=$this->element->modelID;
$searchObjectO->column=$this->element->column;
WGlobals::setSession($memory, $this->element->lid, $searchObjectO );
$lidMin=$this->element->lid;
$defaultValue=WGlobals::get($lidMin );
$this->value=WGlobals::getUserState($sessionKey . $lidMin, $lidMin, $defaultValue, '',$memory );
if(!$this->_setup()) return false;
if($this->picklistTypeSingle){
return $this->_createSinglePicklist();
}else{
return $this->_createMultiplePicklist();
}
}
private function _advanceSearchLinks($memory,$sessionKey,$controller,$task,$pickListType=''){
$explodeOPIDA=explode('_',$this->element->column );
$lid=$this->element->lid;
$this->createComplexIds($lid, $this->element->map );
$viewParams=new stdClass;
$dropdownPL=WView::picklist($this->element->selectdid );
$optionA=$dropdownPL->getValues();
if(empty($optionA)) return false;
$allListA=array();
foreach($optionA as $myKey=> $oneOption){
$allListA[$myKey]=$oneOption;
}
$searchObjectO=new stdClass;
$searchObjectO->name=$this->element->name;
$searchObjectO->typeName=$this->element->typeName;
$searchObjectO->typeNode=$this->element->typeNode;
$searchObjectO->modelID=$this->element->modelID;
$searchObjectO->column=$this->element->column;
WGlobals::setSession($memory, $this->element->lid, $searchObjectO );
$HTML='';
$oneDropA=array();
$hasNone=false;
foreach($allListA as $oneKey=> $oneVal){
$oneDropA[]=WSelect::option($oneKey, $oneVal );
if(empty($oneKey))$hasNone=true;
}
if(!$hasNone){
$defaultObj=new stdClass;
$defaultObj->value=0;
$defaultObj->text=WText::t('1206732365OQJK');
array_unshift($oneDropA, $defaultObj );
}
Output_Doc_Document::$advSearchHTMLElementIdsA[$lid]='srchwz_'.$lid;
if('picklist' !=$pickListType){
$HTMLDrop=new WList();
$HTMLDrop->classes='filterValue';
$defaultValue=WGlobals::getUserState($sessionKey . $lid, $lid, '','array',$memory );
$baseLink='controller='.$controller;if(!empty($task))$baseLink .='&task='.$task;
$eid=WGlobals::getEID();
if(!empty($eid))$baseLink .='&eid='.$eid;
$multipleValue=true;
if($multipleValue){
$addValue=(!empty($defaultValue)? '|'.ltrim($defaultValue). '|' : '');
$addValue=str_replace('||','|',$addValue );
$baseLink .='&'.$memory.'['.$lid.']='.$addValue;
$defaultValue=explode('|',$defaultValue );
}else{
$baseLink .='&'.$memory.'['.$lid.']=';
}
$this->content=$HTMLDrop->create($oneDropA, $baseLink, $defaultValue, '','value','text');
}else{
$HTMLDrop=new WSelect();
$HTMLDrop->classes='filterValue';
$defaultValue=WGlobals::getUserState($sessionKey . $lid, $lid, '','array',$memory );
$baseLink='controller='.$controller;if(!empty($task))$baseLink .='&task='.$task;
$eid=WGlobals::getEID();
if(!empty($eid))$baseLink .='&eid='.$eid;
$multipleValue=true;
if($multipleValue){
$addValue=(!empty($defaultValue)? '|'.ltrim($defaultValue). '|' : '');
$addValue=str_replace('||','|',$addValue );
$baseLink .='&'.$memory.'['.$lid.']='.$addValue;
$defaultValue=explode('|',$defaultValue );
}else{
$baseLink .='&'.$memory.'['.$lid.']=';
}
if(empty($task))$task='home';
$JSparams=array();
$JSparams['controller']=$controller;
$formObj=WView::form($lid );
$onChange='return '.WPage::actionJavaScript($task, $lid, $JSparams );$name=$memory.'['.$lid.']';
$content=$HTMLDrop->create($oneDropA, $name, null, 'value','text',$defaultValue, null, false, false, $onChange );
$formObj->hidden('controller',$controller );
if(!empty($task))$formObj->hidden('task',$task );
$formObj->addContent($content );
$this->content=$formObj->make();
}
return true;
}
private function _createSinglePicklist(){
if(!empty($this->element->exepicklist)){
$taskExcecution2Use=(!empty($this->element->exepicklisttask))?$this->element->exepicklisttask : WGlobals::get('task');$paramsArray['validation']=true;
$joobiRun='return '.WPage::actionJavaScript($taskExcecution2Use, $this->formName, $paramsArray );
}else{
$joobiRun='';
}
$viewParams=new stdClass;
$viewParams->yid=$this->yid;
$viewParams->sid=$this->modelID;
$dropdownPL=WView::picklist($this->_did, $joobiRun, $viewParams );
$dropdownPL->formName=$this->formName;
if(empty($dropdownPL->_didLists[0])){
$message=WMessage::get();
$message->codeE('The picklist with ID :'.$this->_did.' is not available.  Check the form element with ID :'.$this->element->fid.' to solve the problem. It can be either picklist is not publish or the Level does not match.');
return false;
}
$dropdownPL->params=$this->element;
$dropdownPL->name=$this->map;
if(!empty($this->element->task))$dropdownPL->task=$this->element->task;
$extras="{'em':'em". $this->line."','zval':this.value}";
$script=$this->elementJS($extras );
$dropdownPL->_onChange=$script;
$defaults=(is_array($this->value))?array($this->_did=> $this->value ) : $this->value;
$dropdownPL->defaults=$defaults;
if(empty($this->pickListStyle)){
if(!empty($dropdownPL->_didLists[0]->outype))$this->pickListStyle=$dropdownPL->_didLists[0]->outype;
}else{
$dropdownPL->setPickListType($this->pickListStyle );
}
if(!empty($this->element->editItem)){
$defaults=(is_array($this->value))?$this->value : array($this->_did=> $this->value );
$dropdownPL->defaults=$defaults;
}
$dropdownPL->params->classes=(isset($this->element->classes ))?$this->element->classes : 'simpleselect';
$this->content=$dropdownPL->display();
$this->content=trim($this->content );
if(empty($this->content)) return false;
return true;
}
private function _createMultiplePicklist(){
$dropdownPL=WView::picklist($this->_did );
$dropdownPL->params=$this->element ;
$dropdownPL->name=$this->map;
if(empty($this->pickListStyle)){
if(!empty($dropdownPL->_didLists[0]->outype))$this->pickListStyle=$dropdownPL->_didLists[0]->outype;
}else{
$dropdownPL->setPickListType($this->pickListStyle );
}
$sid=$dropdownPL->params->sid;
if($this->element->editItem){
if( is_string($this->value) && strpos($this->value, '|') !==false){
$valueTmp=trim($this->value, '|');
$value=explode('|',$valueTmp );
}else{
$possibleMap=array('p_','c_','u_');
if(!$this->newEntry && substr($this->idLabel,0,2) !='x_'){
if( in_array( substr($this->idLabel,0,2), $possibleMap )){
$value=explode(',' , $this->value );
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
if(!empty($this->value) && is_string($this->value) && in_array( substr($this->idLabel,0,2), $possibleMap )){
$value=explode('|' , $this->value );
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
private function _setup(){
if(!isset($this->element->map)){
return false;
}
if(!empty($this->element->did)){
$this->_did=$this->element->did;
}elseif(!empty($this->element->selectdid)){$this->_did=$this->element->selectdid;
if(empty($this->pickListStyle)){
if(empty($this->element->selectype) || $this->element->selectype !=5){
if(empty($this->element->selectstyle) || ($this->element->selectstyle !=23 )){
$this->pickListStyle=6;
}else{
$this->pickListStyle=2;
}
}else{
$this->picklistTypeSingle=false;
if(empty($this->element->selectstyle)){
$this->pickListStyle=1;
}elseif($this->element->selectstyle==21){
$this->pickListStyle=1;
}elseif($this->element->selectstyle !=22){
$this->pickListStyle=8;
}elseif($this->element->selectstyle !=23){
$this->pickListStyle=3;
}elseif($this->element->selectstyle !=25){
$this->pickListStyle=7;
}else{
$this->pickListStyle=1;
}
}}
}else{
return false;
}
return true;
}
}
