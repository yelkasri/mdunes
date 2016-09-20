<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class WPicklist_main extends  WObj {
public $defaults=null; public $params=null; 
public $disabled=false;
var $name=null; private $_dropListsA=array(); var $_picklistLists=null;
var $formName='adminForm';
var $champs=array(); var $_onChange=null; 
var $translate=false; var $_default=null;
var $_idName='value';
var $_captionName='text';
var $_initialValue=null;
var $_htmlType=null;
var $_arrayType=false;
private $_oneDrop=null; 
private static $_picklistsValuesA=null;
private static $_picklistsValuesColorA=null;
private static $_picklistsValuesIdentiferA=array();
var $_multipleDropDown=false;
var $_extraWhereValues=null; private $_noJS='';
private $_hasOtherInputBox=false;private $_hasOtherSpecificValue=false;
private $_renderHTML=true;
var $_didLists=array();
function __construct($dIds,$onChange=false,$listing=false,$params=null){
$this->_didLists=array();
$this->_onChange=$onChange;
$this->_multipleDropDown=$listing;
if(!empty($dIds)){
$this->_getSQL($dIds, true);
if(isset($this->_didLists[0]->name))$this->name=$this->_didLists[0]->name;
if(isset($params) && !is_array($dIds)){$keyID=( is_string($dIds) && !empty($this->_didLists[0]))?$this->_didLists[0]->did: $dIds;
if(isset($params->default))$this->defaults[$keyID]=$params->default;
if(isset($params->outputType))$this->_didLists[0]->outype=$params->outputType;
if(isset($params->nbColumn))$this->_didLists[0]->colnb=$params->nbColumn;
if(isset($params->map))$this->name=$params->map;
if(isset($params->idlabel)){
if(!isset($this->params))$this->params=new stdClass;
$this->params->idLabel=$params->idlabel;
}
if(!empty($params->yid)){
if(!isset($this->_didLists[0]))$this->_didLists[0]=new stdClass;
$this->_didLists[0]->yid=$params->yid;
}if(!empty($params->sid) && empty($this->_didLists[0]->sid))$this->_didLists[0]->sid=$params->sid;
}
}elseif(!empty($params)){
$this->_didLists[]=$params->didLists;
$this->_oneDrop=$params->onedrop;
if(isset($params->didLists->map))$this->name=$params->didLists->map;
$this->_arrayType=true;
}else{
return false;
}
}
public function setPickListType($pickListStyle=0){
if(empty($pickListStyle))$pickListStyle=6;
$this->_didLists[0]->outype=$pickListStyle;
}
public function load(){
$this->_renderHTML=false;
$this->create();
return $this->_oneDrop;
}
public function getList(){
return $this->getValues();
}
public function getValues($considerParent=false){
static $myPiclist=array();
if(empty($this->_didLists)) return false;
$key=(!empty($this->_didLists[0]->namekey)?$this->_didLists[0]->namekey : $this->_didLists[0]->sid.'|'.$this->_didLists[0]->map ). '|'.$considerParent;
if(!$considerParent && isset($this->_didLists[0]->parent ))$this->_didLists[0]->parent='';
if(!isset($myPiclist[$key])){
$this->create();
if(empty($this->_oneDrop) || ! is_array($this->_oneDrop))$value=null;
else $value=current($this->_oneDrop );
if( is_object($value)){
$returnedA=array();
if(!empty($this->_oneDrop)){
foreach($this->_oneDrop as $oneVal){
$valueName=$this->_idName;
$textName=$this->_captionName;
$returnedA[$oneVal->$valueName]=$oneVal->$textName;
}}$myPiclist[$key]=$returnedA;
}else{
$myPiclist[$key]=$this->_oneDrop;
}
}if(!empty($myPiclist[$key])) return $myPiclist[$key];
else return false;
}
public function inNames($string){
$list=$this->getValues();
if(!empty($list)){
return in_array($string, $list );
}else{
return false;
}}
public function inValues($search){
$list=$this->getValues();
if(!empty($list)){
return in_array($search, array_keys($list));
}else{
return false;
}
}
public function inIdentifiers($identifer){
$list=$this->getValues();
if(empty(self::$_picklistsValuesIdentiferA[$this->_didLists[0]->did])) return false;
$value=$this->_didLists[0]->namekey.'_'.$identifer;
return in_array($value, self::$_picklistsValuesIdentiferA[$this->_didLists[0]->did] );
}
public function getValueFromIdentifier($identifier,$addIdenfier=true){
$this->getValues();
if(!isset($this->_didLists[0])) return null;
if($addIdenfier){
if(!empty($this->_didLists[0]->namekey))$value=$this->_didLists[0]->namekey.'_'.$identifier ;
else $value=null;
}else{
$value=$identifier;
}
if(isset(self::$_picklistsValuesIdentiferA[$this->_didLists[0]->did])){
$flipA=array_flip( self::$_picklistsValuesIdentiferA[$this->_didLists[0]->did] );
if(isset($flipA[$value] )) return $flipA[$value];
}
return null;
}
public function getIdentifier($value,$removeParentIdentifier=true){
$this->getValues();
if(isset( self::$_picklistsValuesIdentiferA[$this->_didLists[0]->did] )){
$idnA=self::$_picklistsValuesIdentiferA[$this->_didLists[0]->did];
if(!isset($idnA[$value])) return null;
if($removeParentIdentifier){
$ln=strlen($this->_didLists[0]->namekey );
return substr($idnA[$value], $ln+1 );
}else{
return $idnA[$value];
}
}
return null;
}
public function getColor($i){
$this->getValues();
if(isset( self::$_picklistsValuesColorA[$this->_didLists[0]->did] )){
$colorA=self::$_picklistsValuesColorA[$this->_didLists[0]->did];
if(isset($colorA[$i])){
return $colorA[$i];
}else{
return false;
}}else{
return false;
}
}
public function getPicklistProperties($property){
if(empty($this->_didLists[0])) return null;
if('data'==$property ) return $this->_didLists[0];
if(!empty($this->_didLists[0]->$property )) return $this->_didLists[0]->$property;
return null;
}
public function getName($i){
$myArray=$this->getValues();
if(isset($myArray[$i])){
return $myArray[$i];
}else{
return null;
}
}
public function getMaps($context='filter_state',$formName='adminForm'){
$this->formName=$formName;
$picklistVals=array();
if(isset($this->_didLists) && !empty ($this->_didLists)){
$map=array();
foreach($this->_didLists as $dropList){
if(isset($dropList->did) && isset($dropList->map)){
$this->defaults[$dropList->did]=WGlobals::getUserState('v-'.$context.'-'.$dropList->did.'-pick3list-'.$dropList->map, $dropList->map, null );
$picklistVals[$dropList->map]=$this->defaults[$dropList->did];
}}
}
return $picklistVals;
}
function create(){
static $IDcount=1;
$exist=false;
if(!empty($this->_didLists[0])){
$this->_dropListsA=array();
$dropValueIds=array();
foreach($this->_didLists as $dropdown){
if(!isset($dropdown->did) || !isset($dropdown->type)) continue;
if( 0==$dropdown->type || 1==$dropdown->type){if(!empty($dropdown->inherit->did)){
$dropValueIds[]=$dropdown->inherit->did;
}else{
$dropValueIds[]=$dropdown->did;
}
$exist=true;
}
}
if(!empty($this->params->sid))$dropdown->elementSID=$this->params->sid;
if($exist){
$createInput=$this->_getDropValues($dropValueIds );
if($createInput==='makeInputBox') return $this->_createInputBox();
elseif(!$createInput ) return false;
}
foreach($this->_didLists as $myDID=> $dropdown){
if(!isset($dropdown->did)) continue;
if(!empty($dropdown->params )){
WTools::getParams($dropdown);
}
$dId=$dropdown->did;
$pciklistStatusFct=false;
switch($dropdown->type){
case '0' : case '1' :
$pciklistStatusFct=$this->_makeTableType($dropdown );
break;
case '2' : $pciklistStatusFct=$this->_makeQueryType($dropdown );
break;
case '3' : $pciklistStatusFct=$this->_makeExternalFile($dropdown );
break;
case '4' : $pciklistStatusFct=$this->_makeFromTypeFile($dropdown);
break;
case '9' : $pciklistStatusFct=true;
break;
default:
break;
}
if($pciklistStatusFct===false) continue;
if($this->_arrayType){
if(isset($dropdown->first_all) && $dropdown->first_all){
$valu=(!empty($dropdown->first_value))?$dropdown->first_value : 0; $textx=(!empty($dropdown->first_caption))?$dropdown->first_caption : WText::t('1219769904NDHQ'); 
$this->_oneDrop=array_reverse($this->_oneDrop, true);
$this->_oneDrop[$valu]=$textx;
$this->_oneDrop=array_reverse($this->_oneDrop, true);
}
}else{
if(!empty($dropdown->first_all)){
$toponeDrop=array();
$valu=(isset($dropdown->first_value)?$dropdown->first_value : 0 ); $textx=(isset($dropdown->first_caption)?$dropdown->first_caption : WText::t('1416059367SCLE')); $toponeDrop[]=WSelect::option($valu, $textx, $this->_idName, $this->_captionName );
if(!isset($this->_oneDrop))$this->_oneDrop=array();
$this->_oneDrop=array_merge($toponeDrop, $this->_oneDrop );
}
}
if(empty($this->_initialValue)){
if(is_array($this->defaults)){if(isset($this->defaults[$dropdown->did] )){
$this->_initialValue=$this->defaults[$dropdown->did];
}
}else{
$this->_initialValue=$this->defaults;
}
}else{
if(is_array($this->defaults) && isset($this->defaults[$dropdown->did] )){
if(is_array($this->defaults[$dropdown->did])){
$currentval=current($this->defaults[$dropdown->did]);
if((count($this->defaults[$dropdown->did]) > 1 || !empty($currentval))){
$this->_initialValue=$this->defaults[$dropdown->did];
}}else{
$this->_initialValue=$this->defaults[$dropdown->did];
}
}elseif(!isset($this->defaults[$dropdown->did] ) && !empty ($this->defaults)){
$this->_initialValue=$this->defaults;
}elseif($this->defaults==0)$this->_initialValue=0; }
if(!$this->_multipleDropDown && !empty($this->name))$dropdown->map=$this->name;
$myChamps=new stdClass;
$myChamps->sid=$dropdown->sid;
$myChamps->map=$dropdown->map;
$myChamps->operation=((!empty($dropdown->opr))?html_entity_decode($dropdown->opr) : '='); $myChamps->model2=(isset($dropdown->model2))?$dropdown->model2 :null;
$myChamps->bkbefore=(isset($dropdown->bkbefore))?$dropdown->bkbefore :0;
$myChamps->bkafter=(isset($dropdown->bkafter))?$dropdown->bkafter :0;
$myChamps->operator=(isset($dropdown->operator))?$dropdown->operator :0;
$this->champs[]=$myChamps;
$size=sizeof($this->_oneDrop);
$sizeMax=(!empty($dropdown->xsize))?$dropdown->xsize : 10;
$size=($size < $sizeMax)?$size : $sizeMax;
$this->_classes=((isset($this->params->classes))?$this->params->classes : 'inputbox');
$listSize=($dropdown->outype==1 )?'size="'.$size.'"' : '';
if($dropdown->outype==1 && ( !isset($dropdown->multiple) || $dropdown->multiple==1 )){
$listSize .=' MULTIPLE ';
$dropdownMap=$dropdown->map.'[]';
}else{
$dropdownMap=$dropdown->map;
}
if(!empty($this->_didLists[0]->isparent) && empty($this->_onChange)){
$paramsArray=array();
$paramsArray['validation']=true;
$task=WGlobals::get('task');
if( in_array($task, array('edit','add','new','save','apply')))$task='apply';
$this->_onChange='return '.WPage::actionJavaScript($task, $this->formName, $paramsArray );
}
if($this->_onChange){
if($dropdown->outype==2 || $dropdown->outype==3 || $dropdown->outype==5 || $dropdown->outype==11){
$onChange=' onClick';
}else{
$onChange=' onChange';
}$this->_htmlType=$listSize . $onChange.'="'.$this->_onChange.'"';
}else{
$this->_htmlType=$listSize;
}
if(isset($this->params->disabled) && $this->params->disabled)$this->_htmlType .=' disabled '; 
if(empty($dropdownMap)){
if(!empty($dropdown->sid) && !empty($dropdown->map))$dropdownName=JOOBI_VAR_DATA.'['.$dropdown->sid.']['.$dropdown->map.']';
else $dropdownName='map'.$dropdown->did;
}else{
$dropdownName=$dropdownMap;
}
if(!empty($this->name2Use))$dropdownName=$this->name2Use;
$IDcount++;
if(!empty($this->params->fid)){
$dropdownTagId=WView::retreiveID('form',$this->params->fid );
}else{
$dropdownTagId=WView::generateID('pickist',$dropdown->did.'_'.$IDcount );
}
$dropdownTagId=WGlobals::filter($dropdownTagId, 'jsnamekey');
if($this->_renderHTML)$this->_makeHTML($dropdown, $dropdownName, $dropdownTagId, $myDID );
}
}else{
return false;
}
return true;
}
private function _createInputBox(){
WLoadFile('output.class.forms');
WPage::renderBluePrint('form');
WView::includeElement('form.text');
$field=new WForm_text;
$field->element=$this->params;
$did=(!empty($this->_didLists[0]->did)?$this->_didLists[0]->did : 0 );
$field->value=(!empty($this->defaults[$did])?$this->defaults[$did] : '');
$field->map=$this->name;
$htmlStatus=$field->create();
if($htmlStatus)$this->_dropListsA[]=$field->content;
return true;
}
private function _makeHTML($dropdown,$dropdownName,$dropdownTagId,$myDID){
switch($dropdown->outype){
case '0' : case '6' : case '1' : $HTMLDrop=new WSelect();
$HTMLDrop->outype=$dropdown->outype;
if(!empty($this->_classes))$HTMLDrop->classes=$this->_classes;
$this->_Hdrop=$HTMLDrop->create($this->_oneDrop, $dropdownName, $this->_htmlType, $this->_idName, $this->_captionName, $this->_initialValue, $dropdownTagId, $this->translate, $this->_arrayType, false, $this->disabled );
break;
case '2' : 
$colnb=(!empty($dropdown->colnb))?$dropdown->colnb : 0;
$HTMLDrop=new WRadio();
if(!empty($this->_classes))$HTMLDrop->classes=$this->_classes;
$HTMLDrop->radioStyle='radioButton';
$this->_Hdrop=$HTMLDrop->create($this->_oneDrop, $dropdownName, $this->_htmlType, $this->_idName, $this->_captionName, $this->_initialValue, $dropdownTagId, $this->disabled, $this->_arrayType, false, $colnb );
break;
case '3' : case '8' :
$colnb=(!empty($dropdown->colnb))?$dropdown->colnb : 0;
$HTMLDrop=new WRadio();
$HTMLDrop->radioStyle='checkBox';
$reqChecked=(!empty($this->requireChecked)?true : false);
if(!empty($this->_classes))$HTMLDrop->classes=$this->_classes;
$this->_Hdrop=$HTMLDrop->create($this->_oneDrop, $dropdownName, $this->_htmlType, $this->_idName, $this->_captionName, $this->_initialValue, $dropdownTagId, $this->disabled, $this->_arrayType, true, $colnb, $reqChecked );
break;
case '7' : $HTMLDrop=new WRadio();
if(!empty($this->_classes))$HTMLDrop->classes=$this->_classes;
$HTMLDrop->radioStyle='checkBoxMultipleSelect';
$colnb=(!empty($dropdown->xsize))?$dropdown->xsize : 4;
$this->_Hdrop=$HTMLDrop->create($this->_oneDrop, $dropdownName, $this->_htmlType, $this->_idName, $this->_captionName, $this->_initialValue, $dropdownTagId, $this->disabled, $this->_arrayType, true, $colnb );
break;
case '5' : $colnb=(isset($dropdown->colnb))?$dropdown->colnb : 0;
$this->_Hdrop=$this->_linkType($this->_oneDrop, $dropdownName, $this->_onChange, $this->_idName, $this->_captionName, $this->_initialValue, $dropdownTagId, $this->_arrayType, $colnb);
break;
case '11' : $colnb=(isset($dropdown->colnb))?$dropdown->colnb : 0;
$this->_Hdrop=$this->_linkType($this->_oneDrop, $dropdownName, $this->_onChange, $this->_idName, $this->_captionName, $this->_initialValue, $dropdownTagId, $this->_arrayType, $colnb, true);
break;
default :
$HTMLDrop=new WSelect();
if(!empty($this->_classes))$HTMLDrop->classes=$this->_classes;
$this->_Hdrop=$HTMLDrop->create($this->_oneDrop, $dropdownName, $this->_onChange, $this->_idName, $this->_captionName, $this->_initialValue, $dropdownTagId, false, $this->_arrayType);
break;
}
$title=(!empty($this->params->title))?$dropdown->name : '';
$this->_dropListsA[]=$title . $this->_Hdrop;
}
function make(){
if(!$this->create()) return false;
$this->_dropListsA[]=$this->_noJS;
return $this->_dropListsA;
}
function display(){
if(!$this->create()) return false;
if(!empty($this->_noJS))$this->_dropListsA[]=$this->_noJS;
if(!empty($this->_dropListsA) && is_array($this->_dropListsA)){
return ' '.implode(' ',$this->_dropListsA );
}else{
return '';
}
}
public function displayOne($myValue=null){
$dropdown=$this->_didLists[0];
$returnedObj=new stdClass;
$returnedObj->status=true;
$returnedObj->content='';
$returnedObj->value='';
$returnedObj->color='';
if(empty($dropdown->type)){
$returnedObj->status=false;
return $returnedObj;
}
if(!isset($this->defaults))$this->defaults=$myValue;
switch ($dropdown->type){
case '0' : case '1' :
$returnedObj=$this->_makeTableTypeOne($dropdown );
break;
case '2' : $value=$this->_makeQueryType($dropdown, true);
$returnedObj->content=$value;
$returnedObj->value=$this->defaults;
$message=WMessage::get();
$message->codeE('Code not implemented yet 82.');
$returnedObj->status=true;
break;
case '3' : 
if(isset($dropdown->wid)){
$pcs=explode('.',$dropdown->external );
if(count($pcs)==2){
$dropdown->external=$pcs[1];
}
$nodeName=WExtension::get($dropdown->wid, 'folder'); $nodeName.='.';
$dropdown->external=$nodeName . $dropdown->external;
}
if(!empty($dropdown->external)){
$dropFunction=$this->_createExternalPicklistInstance($dropdown, true);
if($dropFunction===false)$returnedObj->status=false;
$picklistValuesZ=(isset($dropFunction->picklistValues)?$dropFunction->picklistValues : array());
if( sizeof($picklistValuesZ) > 1){
$allValueA=$this->getList();
if(empty($dropFunction->defaultValue)){
$hasValue=isset($allValueA[0]);
if($hasValue)$findValue=$allValueA[0];
else {
$hasValue=isset($allValueA[(string)$dropFunction->defaultValue]);
if($hasValue)$findValue=$allValueA[(string)$dropFunction->defaultValue];
}}else{
if(is_array($dropFunction->defaultValue))$dropFunction->defaultValue=array_shift($dropFunction->defaultValue);
$hasValue=isset($allValueA[$dropFunction->defaultValue]);
if($hasValue)$findValue=$allValueA[$dropFunction->defaultValue];
}
if($hasValue){
$returnedObj->content=$findValue;
$returnedObj->value=$dropFunction->defaultValue;
if(isset($dropFunction->picklistValues[$returnedObj->value]->color)){
$returnedObj->color=$dropFunction->picklistValues[$returnedObj->value]->color;
}$returnedObj->status=true;
return $returnedObj;
}else{
$returnedObj->status=false;
return $returnedObj;
}
}
if(empty($dropFunction->picklistValues)){
if(empty($dropFunction->picklistValues))$dropFunction=new stdClass;
$dropFunction->picklistValues=array();
$dropFunction->picklistValues[0]=new stdClass;
$dropFunction->picklistValues[0]->text='';
$dropFunction->picklistValues[0]->value='';
}
if(isset($myValue)){
foreach($dropFunction->picklistValues as $oneEntry){
if($oneEntry->value==$myValue){
$returnedObj->content=$oneEntry->text;
$returnedObj->value=$myValue;
}}
}else{
$returnedObj->content=$dropFunction->picklistValues[0]->text;
$returnedObj->value=$dropFunction->picklistValues[0]->value;
}
}else{
$returnedObj->status=false;
}
break;
case '4' : 
if(isset($dropdown->external)){
if(isset($dropdown->wid)){
$pcs=explode('.',$dropdown->external);
if(count($pcs)==2){
$dropdown->external=$pcs[1];
}$nodeName=WExtension::get($dropdown->wid, 'folder');
$nodeName.='.';
$types=WType::get($nodeName . $dropdown->external );
} else $types=WType::get($dropdown->external);
if(!empty($types)){
$value=$types->getTranslatedName($this->defaults );
$returnedObj->content=$value;
$returnedObj->value=$this->defaults;
$returnedObj->status=true;
return $returnedObj;
}}else{
$returnedObj->status=false;
}
break;
default:
$returnedObj->status=false;
break;
}
return $returnedObj;
}
public function getExtraQuery(){
return $this->_extraWhereValues;
}
public function getOtherInputBox(){
return $this->_hasOtherInputBox;
}
public function getOtherSpecificValue(){
return $this->_hasOtherSpecificValue;
}
private function _linkType($lists,$tag_name,$tag_attribs=null,$propKey='value',$propText='text',$selected='0',$idtag=null,$arrayType=false,$colnb=0,$addULLI=false){
if($addULLI){
$html='<ul class="linkList">';
}else{
$html='<div class="linkList">';
}
$html .='<input type="hidden" id="'.$idtag.'" value="'.$selected.'" name="'.$tag_name.'">';
$cnt=1;
$col=1;
$total=count($lists);
foreach($lists as $key=> $list){
if($arrayType){
$listKey=$key;
$listValue=$list;
}else{
$listKey=$list->$propKey;
$listValue=$list->$propText;
}
$onclick='document.getElementById(\''.$idtag.'\').value=document.getElementById(\''.$idtag . $cnt.'\').value; '.$tag_attribs;
if($addULLI){
if($cnt==1)$html .='<li class="fistItem">';
elseif($total==$cnt)$html .='<li class="lastItem">';
else $html .='<li>';
}
$html .='<a href="#" onclick="'.$onclick.'">';
if($listKey==$selected)$html .='<b>';
$html .=$listValue;
if($listKey==$selected)$html .='</b>';
$html .='</a>';
if($addULLI)$html .='</li>';
$html .='<input id="'.$idtag . $cnt.'" type="hidden" value="'.$listKey.'">';
if($colnb > 1 && $col==$colnb){
$html .='<br />';
$col=0;
}
$cnt++;
$col++;
}
if($addULLI){
$html .='</ul>';
}else{
$html .='</div>';
}
return $html;
}
private function _makeTableType($dropdown){
$this->_initialValue=null;
$did=(!empty($dropdown->inherit->did)?$dropdown->inherit->did : $dropdown->did );
if(isset( self::$_picklistsValuesA[$did] )){
$this->_idName='id';
$this->_captionName='name';
$this->_default=$dropdown->map;
$this->_oneDrop=self::$_picklistsValuesA[$did];
}
$this->_arrayType=true;
return true;
}
private function _makeTableTypeOne($dropdown){
$this->_initialValue=null;
if(is_array($this->defaults)){
if(empty($this->defaults[0])){
$hasValuesB=false;
}else{
$hasValuesB=true;
}}else{
$hasValuesB=true;
}$color='';
$result=null;
if(!empty($this->defaults) && $hasValuesB){
$didValue=(!empty($dropdown->inherit->did)?$dropdown->inherit->did : $dropdown->did );
$picklistM=WModel::get('library.picklistvalues','object');
$picklistM->rememberQuery(true);
$picklistM->makeLJ('library.picklistvaluestrans','vid');
$picklistM->whereLanguage(1);
$picklistM->select('name', 1 );
$picklistM->select('value');
$picklistM->select('color');
$picklistM->whereE('did',$didValue );
$picklistM->orderBy('ordering');
$allValuesA=$picklistM->load('ol');
if(!empty($allValuesA)){
foreach($allValuesA as $OValue){
if(is_array($this->defaults) && in_array($OValue->value, $this->defaults)){
$result[]=$OValue->name;
}elseif($OValue->value==$this->defaults){
$result=$OValue->name;
$color=$OValue->color;
break;
}
}
}
}
$this->_idName='id';
$this->_captionName='name';
$this->_default=$dropdown->map;
if(isset($result) && !is_array($this->defaults) && !is_object($this->defaults)){
$this->_oneDrop=array($this->defaults=> $result );
}else{
$this->_oneDrop=array();
}$this->_arrayType=true;
$returnedObj=new stdClass;
if((is_array($this->defaults) || is_array($result))){
if(empty($result))$returnedObj->content='';
else $returnedObj->content=implode('<br />',$result );
}else{
if(empty($result) && !empty($this->defaults)){
$returnedObj->content=( is_string($this->defaults)?$this->defaults : '');
}else{
$returnedObj->content=$result;
}}
$returnedObj->value=$this->defaults;
$returnedObj->status=$hasValuesB;
$returnedObj->color=$color;
return $returnedObj;
}
private function _makeQueryType($droplist,$onlyOneValue=false){
static $sqlA=array();
static $queryResult=array();
$this->_initialValue=null;
if(isset($droplist->ref_sid) && isset($droplist->prop_id) && isset($droplist->prop_caption)){
$idO=$droplist->prop_id;
$captionO=$droplist->prop_caption;
$pickey=$droplist->ref_sid;
if(!isset($sqlA[$pickey]))$sqlA[$pickey]=WModel::get($droplist->ref_sid, 'object');
if(empty($sqlA[$pickey])){
$message=WMessage::get();
$message->codeE('The picklist could not be loaded check the log file picklist-error.log');
WMessage::log('The following picklist could not be load because the ref_model is not know: ','picklist-error');
WMessage::log($droplist , 'picklist-error');
return false;
}
$j=1;
$whereAs=0;
$selectAs=0;
if(isset($droplist->level1) && isset($droplist->sid1) AND isset($droplist->join_cond1)){
$cond0=(isset($droplist->join_cond0))?$droplist->join_cond0 : $sqlA[$pickey]->_pkey;
$sqlA[$pickey]->makeLJ($droplist->sid1, $cond0, $droplist->join_cond1);
$whereAs=(isset($droplist->where_as1)?$droplist->where_as1 : 1 );
$selectAs=(isset($droplist->caption_as)?$droplist->caption_as : 0 );
}
$where_champ='wmap'.$j;
$resultIndex=$pickey.'-';
while (isset($droplist->$where_champ))
: $where_value='wval'.$j;
$where_in='win'.$j;
if(isset($droplist->$where_champ )){
$valCond=(!isset($droplist-> $where_value))?'' : $droplist-> $where_value;
$sqlA[$pickey]->whereE($droplist->$where_champ, $valCond, $whereAs);
$resultIndex .=$droplist->$where_champ .'-'. $valCond .'-'. $whereAs .'-';
}$j++;
$where_champ='wmap'.$j;
endwhile;
$resultIndex .=(string)$onlyOneValue;
if(!isset($queryResult[$resultIndex])){
if($onlyOneValue){
$sqlA[$pickey]->rememberQuery();
$sqlA[$pickey]->whereE($idO, $this->defaults );
$sqlA[$pickey]->select($captionO, $selectAs );
$queryResult[$resultIndex]=$sqlA[$pickey]->load('lr');
if($queryResult[$resultIndex] !==false) return $queryResult[$resultIndex];
}else{
$sqlA[$pickey]->select($idO, 0 );$sqlA[$pickey]->select($captionO, $selectAs );$sqlA[$pickey]->setLimit( 500 );
$queryResult[$resultIndex]=$sqlA[$pickey]->load('ol');
if($queryResult[$resultIndex] !==false)$this->_oneDrop=$queryResult[$resultIndex];
if(empty($queryResult[$resultIndex])) return false;
}
}
$this->_idName=$idO;
$this->_captionName=$captionO;
$this->_arrayType=false;
return true;
}
return false;
}
private function _makeExternalFile(&$droplist){
if(!isset($this->_oneDrop))$this->_oneDrop=array();
$this->_arrayType=false;
$droplist->prop_id=null;
$droplist->prop_caption=null;
$this->_initialValue=null;
if(!empty($droplist->external)){
$HTML=$this->_createExternalPicklistInstance($droplist, false);
if($HTML===false) return false;
$this->_idName=(!empty($droplist->prop_id))?$droplist->prop_id : 'value';
$this->_captionName=(!empty($droplist->prop_caption))?$droplist->prop_caption : 'text';
if(isset($this->params->onChange)){
$this->_onChange=$this->params->onChange;
}
if(isset($droplist->onChange)){
$this->_onChange=$droplist->onChange;
}
if(isset($droplist->disabled))$this->params->disabled=$droplist->disabled;
if(isset($droplist->classes))$this->params->classes=$droplist->classes;
if(empty($this->defaults))$this->defaults='';
return true;
}
return false;
}
private function _makeFromTypeFile($droplist){
$this->_idName=(isset($droplist->prop_id)?$droplist->prop_id : 'value');
$this->_captionName=(isset($droplist->prop_caption))?$droplist->prop_caption : 'text';
$this->_oneDrop=array();
$this->_initialValue=null;
if(isset($droplist->external)){
if(isset($droplist->wid)){
$pcs=explode('.',$droplist->external );
if( count($pcs)==2){
$droplist->external=$pcs[1];
}$nodeName=WExtension::get($droplist->wid, 'folder');
$nodeName .='.';
$genericTypeT=WType::get($nodeName.$droplist->external );
} else $genericTypeT=WType::get($droplist->external );
if(!empty ($genericTypeT)){
$this->_oneDrop=$genericTypeT->getList(true);
if(isset($genericTypeT->defaults))$this->defaults=$genericTypeT->defaults;
}
$this->_arrayType=true;
return true;
}
return false;
}
private function _createExternalPicklistInstance(&$droplist,$onlyOne=false){
$this->_oneDrop=array();
if(empty($droplist->core)){
$baseLocation=JOOBI_DS_USER.'custom'.DS;
}else{
$baseLocation=JOOBI_DS_NODE;
}$location=$droplist->external;
if(!empty($droplist->wid)){
$pcs=explode('.',$droplist->external);
if(count($pcs)==2){
$droplist->external=$pcs[1];
}$nodeName=WExtension::get($droplist->wid, 'folder'); $nodeName .='.';
$location=$nodeName.$droplist->external;
}
$myPos=strpos($location, '.');
$myFuntion=substr($location, 0 , $myPos );
$myFile=substr($location, $myPos+1 );
$myFile=$droplist->external;
WLoadFile($myFuntion.'.picklist.'.$myFile , $baseLocation );$className=ucfirst($myFuntion ). '_'.ucfirst($myFile ). '_picklist';
if(!empty($className) && class_exists($className )){
$dropFunction=new $className();
$dropFunction->picklistValues=&$this->_oneDrop;
$dropFunction->_params=&$droplist;
$dropFunction->defaultValue=&$this->defaults;
if(isset($droplist->prop_id))$dropFunction->propertyID=$droplist->prop_id;
if(isset($droplist->prop_caption))$dropFunction->propertyCaption=$droplist->prop_caption;
if(isset($droplist->wval1))$dropFunction->wval1=$droplist->wval1;
if(isset($droplist->wmap1))$dropFunction->wmap1=$droplist->wmap1;
$dropFunction->onlyOne=$onlyOne;
$dropFunction->did=$droplist->did;
$dropFunction->name=$this->name;
$dropFunction->sid=$droplist->sid;
$dropFunction->formName=$this->formName;
}else{
$message=WMessage::get();
$message->codeE('Class not found in the picklist file!  Name:'.$className, array(), 0 );
$tmp=null;
return $tmp;
}
if(!method_exists($dropFunction, 'create')){
$function=$location;
$did=$this->_didLists[0]->did;
$mess=WMessage::get();
$mess->codeW('The picklist list : create()  does not exists in the function : '.$function.'<br /> Check the parameters and settings of the drop down with did='.$did);
return false;
}
$pciklistStatus=$dropFunction->create();
if($pciklistStatus===false){
return false;
}
if(!empty($dropFunction->defaultValue))$dropFunction->defaults=$dropFunction->defaultValue;
if(isset($dropFunction->classes))$this->params->classes=$dropFunction->classes;
if(isset($dropFunction->outype))$droplist->outype=$dropFunction->outype;
if(isset($dropFunction->onChange))$droplist->onChange=$dropFunction->onChange;
if(isset($dropFunction->bkbefore))$droplist->bkbefore=$dropFunction->bkbefore;
if(isset($dropFunction->bkafter))$droplist->bkafter=$dropFunction->bkafter;
if(isset($dropFunction->operator))$droplist->operator=$dropFunction->operator;
if(isset($dropFunction->extraWhereValues))$this->_extraWhereValues=$dropFunction->extraWhereValues;
return $dropFunction;
}
private function _getSQL($dIds,$short=false){
$caching=WPref::load('PLIBRARY_NODE_CACHING');
$getModel=true;
if($caching > 5){
$keyCache=serialize($dIds );
$cache=WCache::get();
$myCachedPicklist=$cache->get($keyCache, 'Picklist');
if(!empty($myCachedPicklist))$getModel=false;
}
if($getModel){
$didList1=array();
$didList2=array();
if(!is_array($dIds)){
$this->_didLists=$this->_runQuery($dIds, $short );}else{
foreach($dIds as $orderDID){
if(!empty($orderDID->did)){
$didList1[]=$orderDID->did;}}
$myDIDList=array();
if(!empty($didList1)){
$results=$this->_runQuery($didList1, '',$short );
if(!empty($results)){
foreach($results as $result){
$myDIDList[$result->did]=$result;
}
}}if(!empty($didList2)){
$results=$this->_runQuery($didList2, '',$short );
if(!empty($results)){
foreach($results as $result){
$myDIDList[$result->did]=$result;
}
}}
foreach($dIds as $orderDID){
if(!empty($orderDID->did)){
if(isset($myDIDList[$orderDID->did]))$this->_didLists[]=$myDIDList[$orderDID->did];}}
}
if($caching > 5)$cache->set($keyCache, $this->_didLists, 'Picklist');
}else{
$this->_didLists=$myCachedPicklist;
}
$roleC=WRole::get();
$myRoleA=$roleC->getUserRoles();
if(!empty($this->_didLists)){
foreach($this->_didLists as $myKey=> $oneResult){
if(!empty($oneResult->params)) WTools::getParams($oneResult );
if(!empty($oneResult->inherit)){
$myInherit=$oneResult->inherit;
$this->_didLists[$myKey]->inherit=WModel::getElementData('library.picklist',$myInherit );
if(empty($this->_didLists[$myKey]->inherit)){
$this->_didLists[$myKey]->inherit=WView::picklist($myInherit, null, null, 'data');
}
if(!empty($this->_didLists[$myKey]->inherit->type))$this->_didLists[$myKey]->type=$this->_didLists[$myKey]->inherit->type;
if(!empty($this->_didLists[$myKey]->inherit->external))$this->_didLists[$myKey]->external=$this->_didLists[$myKey]->inherit->external;
}
if(!in_array($oneResult->rolid, $myRoleA )) unset($this->_didLists[$myKey] );
}
}
}
private function _runQuery($dIds,$short=false){static $alreadyLoaded=array();
if(is_numeric($dIds)){
}elseif( is_string($dIds)){
if($dIds[0]=="#"){
$reload=WViews::checkExistFileForInserting($dIds, 'picklist');
}else{
$picklistExpendedA=explode('_',  $dIds );
$newdIds='#'.$picklistExpendedA[0].'#'.$dIds;
$reload=WViews::checkExistFileForInserting($newdIds, 'picklist');
}
}elseif(is_array($dIds)){
foreach($dIds as $key=> $value)
{
if(is_string($dIds) && $dIds[0]=="#")
{
  $reload=WViews::checkExistFileForInserting($dIds, 'picklist');
}}
}
$key=serialize($dIds );
$alreadyLoaded[$key]=$this->_getSQLFromDB($dIds, $short );
if(!empty($alreadyLoaded[$key])){
$picklist2ReloadA=array();
$reloadNow=false;
foreach($alreadyLoaded[$key] as $onePicklIst){
if(!empty($onePicklIst->reload)){
$picklist2ReloadA[]=$onePicklIst->did;
$reloadNow=true;
}
}
if(!empty($picklist2ReloadA)){
WViews::checkExistFileForInserting($picklist2ReloadA, 'picklist');
$alreadyLoaded[$key]=$this->_getSQLFromDB($dIds, $short );
$controllerM=WModel::get('library.picklist','object');
$controllerM->whereIn('did',$picklist2ReloadA );
$controllerM->setVal('reload', 0 );
$controllerM->update();
}
}
return $alreadyLoaded[$key];
}
private function _getSQLFromDB($dIds,$short=false){
$picklistM=WModel::get('library.picklist','object');
if(!empty($dIds)){
if( is_numeric($dIds)){
$picklistM->whereE('did',$dIds );
}elseif( is_string($dIds)){
$picklistM->whereE('namekey',$dIds );
}else{
$picklistM->whereIn('did',$dIds );
$sort=true;
}
}
if($short){
$picklistM->select( array('did','rolid','core','namekey','sid','ref_sid','type','outype','external','allowothers','first_value','first_caption','params','map','parent','isparent','first_all','lib_ext','wid','reload','colorstyle'));
}
$picklistM->whereE('publish', 1 );
$namekey=WGlobals::get('extensionKEY', null, 'global');
if(empty($namekey)){
$extID=WGlobals::get('extensionID', null, 'global');
if(!empty($extID))$namekey=WExtension::get($extID, 'namekey');
}
$picklistM->where('level','<=', WGlobals::getCandy());
$picklistM->setLimit( 500 );
$alreadyLoaded=$picklistM->load('ol');
if(empty($alreadyLoaded)){
return false;
}
return $alreadyLoaded;
}
private function _getDropValues($values,$published=true){
static $picklistsValuesA=array();
$this->_hasOtherInputBox=false;
if(is_array($values))$sampleDID=current($values);
else $sampleDID=0;
if(empty($this->_didLists)) return;
if(!empty($this->_didLists[0]->parent)){$parentDid=WView::picklist($this->_didLists[0]->parent, '', null, 'did');
$parent=WGlobals::get('pikclsitValue_'.$parentDid, '','global');
$onlyInput=WGlobals::get('pikclsithasOtherInputBox_'.$parentDid, false, 'global');
if($onlyInput && empty($parent)){
return 'makeInputBox';
}}
$key=serialize($values);
if(!isset($picklistsValuesA[$key])){
static $picklistValuesM=null;
if(!isset($picklistValuesM))$picklistValuesM=WModel::get('library.picklistvalues');
$picklistValuesM->makeLJ('library.picklistvaluestrans','vid');
$picklistValuesM->makeLJ('library.picklist','did','did', 0, 2 );
$picklistValuesM->select('name', 1 );
$picklistValuesM->select('namekey', 2, 'picklist');
$picklistValuesM->whereLanguage(1);
$picklistValuesM->rememberQuery(true);
$picklistValuesM->whereIn('did',$values );
if(!empty($parentDid))$picklistValuesM->whereE('parent',$parent );
$picklistValuesM->checkAccess();
if($published)$picklistValuesM->whereE('publish', 1 );
$picklistValuesM->orderBy('did','ASC');
$picklistValuesM->orderBy('ordering','ASC');
$picklistValuesM->setLimit( 5000 );
$picklistsValuesA[$key]=$picklistValuesM->load('ol',array('did','premium','value','namekey','color','inputbox'));
}
if(empty($picklistsValuesA[$key])){
return 'makeInputBox';
return false;
}
$defaultsValueA=array();
$currentDefaultValueSetA=array();
$valueExitA=array();
$toRemovedA=array();
foreach($picklistsValuesA[$key] as $pickvalue){
if(!isset($toRemovedA[$pickvalue->picklist]))$toRemovedA[$pickvalue->picklist]=WView::getRemovedElements($pickvalue->picklist );
if(!empty($toRemovedA[$pickvalue->picklist] )){
if( in_array($pickvalue->namekey, $toRemovedA[$pickvalue->picklist] )) continue;
}
if(!empty($pickvalue->inputbox)){
$this->_hasOtherInputBox=$pickvalue->value;
}
self::$_picklistsValuesA[$pickvalue->did][$pickvalue->value]=$pickvalue->name;
self::$_picklistsValuesColorA[$pickvalue->did][$pickvalue->value]=$pickvalue->color;
self::$_picklistsValuesIdentiferA[$pickvalue->did][$pickvalue->value]=$pickvalue->namekey;
if(empty($this->_initialValue )){
if($pickvalue->premium==1){
if(!isset($this->defaults[$pickvalue->did]))$this->defaults[$pickvalue->did]=$pickvalue->value;
else $defaultsValueA[$pickvalue->did]=$pickvalue->value;
}
if(!isset($currentDefaultValueSetA[$pickvalue->did])){
$currentDefaultValueSetA[$pickvalue->did]=(isset($this->defaults[$pickvalue->did])?$this->defaults[$pickvalue->did] : null );
}
if(is_array($currentDefaultValueSetA[$pickvalue->did])){
if( in_array($pickvalue->value, $currentDefaultValueSetA[$pickvalue->did] ) || (isset($currentDefaultValueSetA[$pickvalue->did][0]) && empty($currentDefaultValueSetA[$pickvalue->did][0]))){
$valueExitA[$pickvalue->did]=true;
}
}else{
if($currentDefaultValueSetA[$pickvalue->did]==$pickvalue->value){
$valueExitA[$pickvalue->did]=true;
}}
}
}
if(!empty($currentDefaultValueSetA[$sampleDID]) && empty($valueExitA)){
if($this->_hasOtherInputBox !=$currentDefaultValueSetA[$sampleDID]){
$this->defaults[$sampleDID]=$this->_hasOtherInputBox;
$this->_hasOtherSpecificValue=true;
}}else{
foreach($picklistsValuesA[$key] as $pickvalue){
if(empty($valueExitA[$pickvalue->did]) && isset($defaultsValueA[$pickvalue->did]))$this->defaults[$pickvalue->did]=$defaultsValueA[$pickvalue->did];
}
}
return true;
}
}
class WPicklist extends WObj {
var $picklistValues=array();var $_params=null;var $defaultValue=null;
var $onlyOne=false; 
var $propertyID='value';var $propertyCaption='text';
protected function onlyOneValue(){
return $this->onlyOne;
}
protected function getParamsValue($property=null){
if(empty($property)){
return $this->_params;
}else{
if(isset($this->_params->$property)){
return $this->_params->$property;
}else{
return null;
}}}
protected function getDefault(){
return $this->defaultValue;
}
protected function getValue($columnName,$modelName=null){
$data=null;
WView::storeData($data );
if(!empty($data)) return WView::retreiveOneValue($data, $columnName, $modelName );
else return false;
}
protected function setDefault($values,$overwrite=false){
if(empty($this->defaultValue[$this->_params->did] ) || $overwrite===true){
if(!is_array($this->defaultValue))$this->defaultValue=array();
if(isset($this->_params) && $this->_params->did)$this->defaultValue[$this->_params->did]=$values;
}
}
protected function addTitle($caption,$value=null){
static $count=1;
if(!isset($value)){
$value='xh'.$count;
$count++;
}
$this->addElement($value , '--'. $caption );
}
public function addElement($value,$caption,$extraProperty=null){
$this->picklistValues[]=WSelect::option($value, $caption, $this->propertyID, $this->propertyCaption, $extraProperty );
}
}