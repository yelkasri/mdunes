<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Output_Doc_Document {
public $htmlObj=null; 
public $cellValue=''; public $complexMap=''; public $rowNumber=0;public $rowOffset=0;public $numberRows=0;protected $displayCategoryRoot=false; public $allDataRowsA=array();protected $onlyOnceOrdering=true;
public $paramsMapName='';
public static $advSearchHTMLElementIdsA=array();
protected $listingHTML=null;
public static function loadListingElement(&$listing){
if(!isset($listing->typeName)) return false;
$className='WListing_'.$listing->typeName;
WView::includeElement($listing->typeNode.'.listing.'.$listing->typeName );
if(!empty($listing->pnamekey) && empty($listing->filef)){
$explodePtA=explode('.',$listing->pnamekey );
if( count($explodePtA ) > 1){
$listing->filef=$explodePtA[1];
$listing->currentClassName=ucfirst( WExtension::get($listing->nodeID, 'folder')). '_'.ucfirst($explodePtA[1] ) .'_listing';
}}
if(!empty($listing->currentClassName) && !empty($explodePtA)){
$parentClassName=ucfirst($explodePtA[0] ). '_'.ucfirst($explodePtA[1] ) .'_listing';
if(!class_exists($parentClassName )) WView::includeElement($explodePtA[0].'.listing.'.$explodePtA[1], null, true, true);
if(!class_exists($listing->currentClassName )){
eval('class '.$listing->currentClassName.' extends '.$parentClassName.' {}');
}}
$colum=new $className;
$colum->element=&$listing;
$colum->nodeID=(!empty($listing->wid)?$listing->wid : 0 );
return $colum;
}
public function callListingElement(&$listing,&$row,$function='make'){
static $formFile=array();
$colum=Output_Doc_Document::loadListingElement($listing );
if(empty($colum)){
WMessage::log('Could not load the listing','error-load-listing');
WMessage::log($listing, 'error-load-listing');
return false;
}
$colum->data=&$row;
if(empty($this->paramsMapName)){
$colum->mapList=$this->htmlObj->mapListA;
}else{
$allObjectProperty=get_object_vars($row );
$colum->mapList=$this->htmlObj->mapListA;
foreach($allObjectProperty as $oneKey=> $oneProperty){
if(!isset($colum->mapList[$oneKey]))$colum->mapList[$oneKey]=$oneKey;
}}
$colum->searchOn=$this->htmlObj->searchOnB;if($this->htmlObj->searchOnB){
$colum->mywordssearched=(isset($this->htmlObj->_mywordssearched))?$this->htmlObj->_mywordssearched : '';
$colum->mywordsReplaced=(isset($this->htmlObj->_mywordsReplaced))?$this->htmlObj->_mywordsReplaced : '';
}$colum->value=$this->cellValue;
$colum->name=$this->complexMap;
$colum->line=$this->rowNumber + (isset($this->rowOffset)?$this->rowOffset : 0 );
$colum->modelID=$this->htmlObj->sid;
$colum->nodeID=$this->htmlObj->wid;
$colum->formName=(!empty($this->htmlObj->formName))?$this->htmlObj->formName : WGlobals::get('parentFormid','','global');
$colum->controller=$this->htmlObj->controller;
$colum->task=$this->htmlObj->task;
$colum->pkeyMap=$this->htmlObj->pKeyMap;
$colum->map=$listing->map;
$colum->yid=$this->htmlObj->yid;
if($listing->typeName=='order' || ($listing->map=='ordering' && $listing->typeName=='customized')){
if(isset($this->htmlObj->orderingMap))$colum->orderingMap=$this->htmlObj->orderingMap;
if(isset($this->htmlObj->orderingByGroup))$colum->orderingByGroup=$this->htmlObj->orderingByGroup;
if($this->onlyOnceOrdering){
$colum->myReferenceIdTable=array_keys($this->allDataRowsA );
$this->onlyOnceOrdering=false;
}$colum->listData=$this->allDataRowsA;
$colum->categoryRoot=$this->displayCategoryRoot;
$colum->i=$this->rowNumber;
$colum->nb=$this->numberRows;
if(isset($this->childOrderParent))$colum->childOrderParent=$this->childOrderParent;
$colum->currentOrder=$this->htmlObj->currentOrder;
$colum->myParent=$this->htmlObj->myParentA;
$colum->limitstart=$this->htmlObj->limitStart;
$colum->total=$this->htmlObj->totalItems;
}else{
}
if(isset($listing->rtag)){
static $tagC=null;
if(!isset($tagC))$tagC=WClass::get('output.process');
$tagC->replaceTags($colum->value );
}
$html=$colum->$function();
if(!empty($listing->mb_showtitle ) && in_array( JOOBI_APP_DEVICE_TYPE, array('ph','tb'))){
$html='<p style="clear:both;">'.$listing->name.' '.$html.'</p>';
}
$this->cellValue=$colum->value;
return $html;
}
public function createPicklist(&$htmlObj,$notPicklist=array()){
static $storePicklistA=array();
$this->htmlObj=&$htmlObj;
if(!isset($storePicklistA[$this->htmlObj->yid] )){
$droplist=WModel::get('library.viewpicklist','object');
$droplist->rememberQuery(true);
$droplist->makeLJ('library.picklist','did');
$droplist->whereOn('level','<=', WGlobals::getCandy(), 1, null );
$droplist->whereOn('publish','=', 1, 1, null );
$droplist->whereE('yid',$this->htmlObj->yid );
if(!empty($notPicklist))$droplist->whereIn('namekey',$notPicklist, 1, true);
$droplist->orderBy('ordering');
$droplist->setLimit( 500 );
$storePicklistA[$this->htmlObj->yid]=$droplist->load('ol','did');
if(empty($storePicklistA[$this->htmlObj->yid]))$storePicklistA[$this->htmlObj->yid]=false;
}
$this->htmlObj->droplist=$storePicklistA[$this->htmlObj->yid];
if(empty($this->htmlObj->droplist)) return;
if( WGet::isDebug()){
$namekey='PickLlist_'.WGlobals::filter($this->htmlObj->formName, 'jsnamekey'). '_external';
}else{
$namekey='WZY_'.WGlobals::count('f');
}
if( WPref::load('PLIBRARY_NODE_AJAXPAGE')){
$paramsO=WObject::newObject('output.jsaction');
$paramsO->form=$this->htmlObj->formName;
$paramsO->namekey=$namekey;
$valueA=array('limitstart'.$this->htmlObj->yid=> 0 );
if(!empty($this->htmlObj->nestedView)){
$valueA['vWjx']=$this->htmlObj->yid;
$valueA['fRmjx']=$this->htmlObj->formName;
}
$joobiRun=WPage::jsAction($this->htmlObj->_defaultTask, $paramsO, $valueA );
}else{
$joobiRun='return '.WPage::actionJavaScript($this->htmlObj->_defaultTask, $this->htmlObj->formName, array('limitstart'=>'limitstart'.$this->htmlObj->yid,'cfil'=>true), false, $namekey );
}
$paramsPK=new stdClass;
$paramsPK->listing=true;
$dropdownPL=WView::picklist($this->htmlObj->droplist, $joobiRun, $paramsPK );
$this->htmlObj->_defaultPickList=$dropdownPL->getMaps($this->htmlObj->yid, $this->htmlObj->formName );
$this->htmlObj->headListFooter=$dropdownPL->make();
if(!empty($this->htmlObj->headListFooter)) foreach($this->htmlObj->headListFooter as $oneHDropKey=> $oneHDropVal ) if(empty($oneHDropVal)) unset($this->htmlObj->headListFooter[$oneHDropKey]);
$this->htmlObj->queryInfoWhere=$dropdownPL->getExtraQuery();
$this->htmlObj->picklistDefaultA=$dropdownPL->champs;
$this->htmlObj->champsA=array_merge($this->htmlObj->champsA , $this->htmlObj->picklistDefaultA  );
}
public function createReportFilter(&$htmlObj){
$controller=new stdClass;
$controller->wid=WExtension::get('reports.node','wid');
$controller->level=50;
$controller->nestedView=true;
$reportHeaderView=WGlobals::get('reportHeaderType','main_report_header','global');
if(!empty($htmlObj->reportnosetinterval)){
$reportHeaderView .='_no_internval';
}
$viewC=WView::getHTML($reportHeaderView, $controller );
if(empty($viewC)) return false;
$htmlObj->reportFilterContent=$viewC->make();
WLoadFile('output.class.report' , JOOBI_DS_NODE );
$modelDummy=null;
Output_Report_class::reportQuery($htmlObj->task, $modelDummy, true, '','','','',$htmlObj->firstFormName );
}
public function initializeView($object=null){
}
}
class WListings_standard extends WElement {
var $name=null;var $value=null;var $map=null;var $line=null;var $modelID=null;
var $controller=null;var $task=null;var $formName=null;
var $id=null;var $classes=null;var $style=null;var $align=null;
var $element=null;var $listing=null;var $mapList=null;
var $pkeyMap=null;
var $searchOn=null;var $mywordssearched=null;var $mywordsReplaced=null;
var $content='';
var $crlf=''; var $checked=false; 
protected static $complexMapA=array();
protected static $complexSearchIdA=array();
protected function getValue($columnName,$modelName=null){
return WView::retreiveOneValue($this->data, $columnName, $modelName, $this->mapList );
}
function eid(){
$model=WModel::get($this->modelID, 'object');
if($model->multiplePK()){
$primKeyVal=array();
foreach($model->getPKs() as $primKey){
$specialMap=$primKey.'_'. $model->getModelID();
if(!isset($this->data->$specialMap)) continue;
$primKeyVal[]=$this->data->$specialMap;
}}else{
$pKey=$this->pkeyMap;
if(!isset($this->data->$pKey)) return '';
$primKeyVal=$this->data->$pKey;
}
return $primKeyVal;
}
function elementJS($extras=false,$myParams=null){
if(!isset($this->valueTo))$this->valueTo='';
if(empty($extras)){
$extras=new stdClass;
$extras->em='em'.$this->line;
$extras->zval=$this->valueTo;
}
static $securedStrings=array();
if(!isset($this->element->sid))$this->element->sid=0;
if(!isset($securedStrings[ $this->element->sid.'_'.$this->element->map ] )){
$form=WView::form($this->formName );
$form->securityFields[$this->element->sid.'_'.$this->element->map]=array('sid'=> $this->element->sid, 'property'=> $this->element->map );
$securedStrings[$this->element->sid.'_'.$this->element->map]=true;
}
$paramA=array();
$paramA['zsid']=$this->element->sid;
$paramA['zmap']=$this->element->map;
$paramA['zsc']=WTools::secureMe($paramA );
$paramA['lstg']=true;
$JSparamsA=(!empty($myParams->jsButton))?array_merge($paramA, $myParams->jsButton ) : $paramA;
$premium=(!empty($myParams->extra)?'.'.$myParams->extra: ''); 
$task=(!empty($this->element->task)?$this->element->task : 'toggle');
$joobiRun=WPage::actionJavaScript($task, $this->formName, $JSparamsA , $extras );
return $joobiRun;
}
protected function addStyling($string){
if(isset($this->element->style) || isset($this->element->align)  || isset($this->element->classes)){
$html='<span';
if(!empty($this->element->classes))$html .=' class="'.$this->element->classes.'" ';
if(!empty($this->element->align))$html .=' align="'.$this->element->align.'" ';
if(!empty($this->element->style))$html .=' style="'.$this->element->style.'" ';
$html .='>';
$html .=$string.'</span>';
return $html;
}else{
return $string;
}
}
public function createHeader(){
if($this->element->name=='ID'){
if(empty($this->element->width))$this->element->width='3%';
if(empty($this->element->align))$this->element->align='center';
}
return false;
}
public function create(){
$this->content=$this->value;
return true;
}
public function advanceSearch(){
$name='advsearch['.self::$complexMapA[$this->element->lid] .']';
$defaultValue=WGlobals::getUserState( self::$complexSearchIdA[$this->element->lid], self::$complexMapA[$this->element->lid], '','array','advsearch');
$this->content .='<input id="srchwz_'.$this->element->lid.'" class="inputbox" type="text" value="'.$defaultValue.'" name="'.$name.'"/>';
return true;
}
public function advanceSearchLinks($memory,$sessionKey,$controller,$task){
static $count=0;
$searchObjectO=new stdClass;
$searchObjectO->name=$this->element->name;
$searchObjectO->typeName=$this->element->typeName;
$searchObjectO->typeNode=$this->element->typeNode;
$searchObjectO->modelID=$this->element->modelID;
$searchObjectO->column=$this->element->column;
if(!empty($this->element->combined))$searchObjectO->combined=$this->element->combined;
WGlobals::setSession($memory, $this->element->lid, $searchObjectO );
$link=WView::getURI();
$lid=$this->element->lid;
$defaultValue=WGlobals::getUserState($sessionKey . $lid, $lid, '','',$memory );
$nameINput=$memory.'['.$lid.']';
$html='';
if(!empty($defaultValue)){
$resetLink='controller='.$controller;
if(!empty($task))$resetLink .='&task='.$task;
$resetLink .='&'.$memory.'Reset['.$lid.']=1';
$resetLink .='&'.$memory.'['.$lid.']=';
$html .='<a href="'.WPage::link($resetLink ). '">'.WText::t('1383923815RCSI'). '</a>';
$html .='<br />';
}$myClassName=(!empty($this->element->classes )?$this->element->classes : 'advanceSearch');
$myClassName .=' form-control';
$html .='<div class="input-group advSearchElement">
<input id="'.$lid.'" class="'.$myClassName.'" type="text" name="'.$nameINput.'" value="'.$defaultValue.'">';
$html .='<span class="input-group-btn"><button class="btn btn-default" type="submit">'.WText::t('1206732365OQJJ'). '</button></span>';
$html .='</div>';
$count++;
$formHTML=new WForm('advanceSearchText');
$formHTML->hidden('controller',$controller );
if(!empty($task))$formHTML->hidden('task','home');
$formHTML->addContent($html );
$this->content=$formHTML->make();
return true;
}
public function searchQuery(&$model,$element,$searchedTerms=null,$operator=null){
$this->createComplexIds($element->lid, $element->map.'_'.$element->sid );
Output_Doc_Document::$advSearchHTMLElementIdsA[$element->lid]='srchwz_'.$element->lid;
if(!empty($searchedTerms)){
$defaultValue=$searchedTerms;
}else{
$defaultValue=WGlobals::getUserState( self::$complexSearchIdA[$element->lid] , self::$complexMapA[$element->lid], '','array','advsearch');
}
if(empty($defaultValue) || !is_string($defaultValue)) return true;
$outputHelperC=WClass::get('output.helper');
$defaultValueUsed=$outputHelperC->convertSearchTerms($defaultValue );
$model->whereSearch($element->map, $defaultValueUsed, $element->asi, null, $operator );
}
protected function createComplexIds($lid,$map){
self::$complexMapA[$lid]=$map;
self::$complexSearchIdA[$lid]='wiev-'.$lid.'-adv_srch_val';
}
public function total(){
return $this->make();
}
}
class WListings_default extends WListings_standard{
}