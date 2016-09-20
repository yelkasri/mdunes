<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Output_Forms_class extends WView {
var $_newEntity=null;
var $multiplePK=false;
private $_parent=false; var $_area=false;var $formObj=null;var $getModel=true;var $_hasTabs=false;
var $js=null;
private $_setTitleA=array();private static $_memoryHiddenA=array();
protected $securityCheck=array();
protected $securityCheckParams=array();protected $_checkType=false;
function __construct($layout){
$formDirection=WPage::renderBluePrint('form');
if(!empty($formDirection)){
if('basic'==$formDirection){
$this->formDirection='';
}else{
$this->formDirection='form-'.$formDirection;
}}else{
$this->formDirection='form-horizontal';
}
parent::__construct($layout );
}
function create(){
static $comehere=array();
static $alreadyHAveA=array();
$this->myEID=WGlobals::getEID();
$this->member=WUser::get();
$leftJOIN=false;
if(!empty($this->autosave )) WGlobals::set('autosave',$this->autosave ); 
$this->editItem=($this->type>150 && $this->type<200 )?false : true;
if($this->editItem ) WPage::keepAlive();
if($this->elements){
$newElementsA=array();
foreach($this->elements as $keyE=> $oneE){
WTools::getParams($oneE );
if(!empty($oneE->requirednode)){
$nodeExist=WExtension::exist($oneE->requirednode );
if(empty($nodeExist)){
$this->removeElements($oneE->namekey, true, 'output_forms_class');
continue;
}}
if( JOOBI_APP_DEVICE_SIZE){
if(!empty($oneE->xsvisible)){
if( JOOBI_APP_DEVICE_SIZE < $oneE->xsvisible){
continue;
}}if(!empty($oneE->xshidden)){
if( JOOBI_APP_DEVICE_SIZE < $oneE->xshidden){
continue;
}}}
if( JOOBI_APP_DEVICE_TYPE){
if(empty($oneE->devicevisible))$oneE->devicevisible='';
if(empty($oneE->devicehidden))$oneE->devicehidden='';
if(!WView::checkDevice($oneE->devicevisible, $oneE->devicehidden )){
continue;
}}
$newElementsA[]=$oneE;
}
$this->elements=$newElementsA;
if($this->sid > 0){
$this->_model=WModel::get($this->sid );
if(!isset($this->_model)){
$hello=null;
return $hello;
}
if($this->_model->multiplePK()){
$pKeys=$this->_model->getPKs();
$this->multiplePK=true;
foreach($pKeys as $prim){
$valPrim=WGlobals::get($prim );
if(!empty($valPrim)){
$this->_eid[$prim]=$valPrim;
}else{
$this->_eid=null;
break;
}
}}elseif(!isset($this->_eid)){
if(!$this->nestedView){
WGlobals::set('formModelID',$this->sid, 'global');
$this->_eid=WGlobals::getEID();
}else{
$myPK=$this->_model->getPK();
$this->_eid=WForm::getPrev($myPK );
}}
if( is_object($this->_model)){
$this->_pkey=$this->_model->getPK();
$sid=$this->sid=$this->_model->getModelID();
}
}else{
$this->_eid=0;
$this->getModel=false;
}
$flagPredefined=false; $complexForm=false;
if(isset($this->_data )){
$this->_newEntity=false;
$justName=Output_Forms_class::getItem($this->sid, $this->_eid, $this->_data );
$areaRef=$this->elements[0]->area;
$myObjAttrib=get_object_vars($this->_data );
$myObjAttrib5=array_keys($myObjAttrib);
foreach($this->elements as $multiform){
$typeSplitA=explode('.',$multiform->type );
if(empty($typeSplitA[1])){
$multiform->typeNode='output';
$multiform->typeName=$typeSplitA[0];
}else{
$multiform->typeNode=$typeSplitA[0];
$multiform->typeName=$typeSplitA[1];
}
$complexForm=true;
if(!$this->_parent && $multiform->parent > 0)$this->_parent=true;
if(!$this->_area && $areaRef !=$multiform->area)$this->_area=true;
if($this->_parent && $this->_area ) break;
$myMap=$multiform->map;
if( in_array($myMap, $myObjAttrib5 )){
$new_map=$myMap.'_'.$multiform->sid;
$this->_data->$new_map=$this->_data->$myMap;
unset($this->_data->$myMap );
}}
if(!empty($sid)){
$pKey=$this->_pkey.'_'.$sid;
$this->_data->$pKey=$this->_eid;
}
}elseif($this->_eid > 0){
$this->_newEntity=false;
if($this->type==1 && WGlobals::getCandy() > 20 
&& ( !isset($this->_model->_ckout) || (isset($this->_model->_ckout) && ! $this->_model->_ckout)))$this->_checkoutItem();
$flagParams=false;$areaRef=$this->elements[0]->area;
if(!empty($areaRef ))$this->_area=true;
if($this->_model->multiplePK()){
foreach($this->_model->getPKs() as $prim){
$this->_model->whereE($prim, $this->_eid[$prim] );
}}else{
$pkey=$this->_model->getPK();
$this->_model->whereE($pkey, $this->_eid );
$this->_model->select($pkey, 0 , $pkey.'_'.$sid );
}
foreach($this->elements as $multiform){
$typeSplitA=explode('.',$multiform->type );
if(empty($typeSplitA[1])){
$multiform->typeNode='output';
$multiform->typeName=$typeSplitA[0];
}else{
$multiform->typeNode=$typeSplitA[0];
$multiform->typeName=$typeSplitA[1];
}
if(!isset($as[ $multiform->sid ]) && $multiform->sid !=$sid && $multiform->sid !=0 && $multiform->typeName !='multiselect'){
$this->_model->getAs($multiform->sid );
$complexForm=true;
}
}
if(!empty($this->private)){
$membersSID=WModel::get('users','sid');
if(!empty($membersSID)){
$membersAS=$this->_model->getAs($membersSID);
$memberUIDMap='uid_'.$membersSID;
$this->_model->select('uid',$membersAS, $memberUIDMap );
}}
if(!empty($this->filters)){
$fitlersClass=WClass::get('output.filters');
$fitlersClass->addFilterToView($this, $this->_model );
}
$this->_model->getLeftJoin();
$FileinfoA=$this->_check4FileInfo();
$securityCheckIni=array();
foreach($this->elements as $frmKey=> $multiform){
if(!empty($this->dynamicForm)){
if($multiform->sid==0)$multiform->sid=$this->sid;
if($multiform->map=='maptorep'){
$multiform->map=strtolower( WGlobals::get('map'));
}}
if(!empty($multiform->checktype))$this->_checkType=true;
$map=$multiform->map;
if(!$this->_area && $areaRef !=$multiform->area)$this->_area=true;
if(!$this->_parent && $multiform->parent > 0)$this->_parent=true;
if($map=='params')$flagParams=true;
if($map=='predefined')$flagPredefined=true;
if(!empty($multiform->params)){
WTools::getParams($multiform, 'params');
}
if(isset($multiform->onlynew) && $multiform->onlynew==1){
$multiform->required=0;
$this->elements[$frmKey]->required=0;
}
if(isset($multiform->private) && $multiform->private){
if($this->myEID !=$this->member->uid ) continue;
}
$addmeIn=true;
switch( substr($map, 0, 2)){
case 'p[':if(empty($multiform->sid))$multiform->sid=$this->sid;
if(!empty($multiform->map) && $multiform->sid !=0 && $multiform->typeName !='textonly' && (!isset($multiform->onlynew) || $multiform->onlynew==2))$securityCheckIni[$multiform->sid]['p'][$multiform->map]=true;
$flagParams=true;
break;
case 'j[':if(empty($multiform->sid))$multiform->sid=$this->sid;
if(!empty($multiform->map) && $multiform->sid !=0 && $multiform->typeName !='textonly' && (!isset($multiform->onlynew) || $multiform->onlynew==2))$securityCheckIni[$multiform->sid]['j'][$multiform->map]=true;
$flagPredefined=true;
break;
case 'x[':
case 'f[':continue;
break;
case 'c[':case 'u[':if(!empty($multiform->map) && $multiform->sid !=0 && $multiform->typeName !='textonly' && (!isset($multiform->onlynew) || $multiform->onlynew==2))$securityCheckIni[$multiform->sid]['c'][$multiform->map]=true;
$addmeIn=false;
case 'm[':$map=substr($map, 2, strlen($map)-3 );
default:
if(!empty($multiform->fdid)){
if( in_array($multiform->typeName, array('file','multinput','media','image'))){
$FileinfoA[]=$multiform->map.'_'.$multiform->sid;
}}
if(!empty($multiform->map )
&& $multiform->sid !=0
&& $addmeIn
&& !in_array($multiform->map .'_'.$multiform->sid, $FileinfoA ) && $multiform->typeName !='textonly'
&& (empty($multiform->readonly) || $multiform->typeName=='select')&& substr($multiform->map, 0, 2 ) !='m['){
$securityCheckIni[$multiform->sid][$multiform->map]=true;
}
if($multiform->sid>0 && $multiform->typeName !='multiselect'){
if($map=='name' && ! isset($this->_namapsid)){
$this->_namapsid=$map.'_'.$multiform->sid;
}if($map=='description' && !isset($this->_pageDescriptionColumn)){
$this->_pageDescriptionColumn=$map.'_'.$multiform->sid;
}
$distinct=(isset($multiform->distc))?$multiform->distc : 0;
if(!empty($multiform->sid))$this->_model->select($map, $this->_model->getAs($multiform->sid), $map.'_'.$multiform->sid, $distinct );
}break;
}
}
$this->securityCheck=array();
foreach($securityCheckIni as $secureKey=> $secureValue){
ksort($secureValue);
$this->securityCheck[$secureKey]=array_keys($secureValue);
if(!empty($securityCheckIni[$secureKey]['p']))$this->securityCheckParams['p'][$secureKey]=$securityCheckIni[$secureKey]['p'];
}
if($flagParams){
$this->_model->select('params', 0, 'params');
}
if($flagPredefined){
$this->_model->select('predefined', 0, 'predefined');
}
if( 151==$this->type && WExtension::exist('design.node')){
$designModelfieldsC=WClass::get('design.modelfields', null, 'class', false);
if(!empty($designModelfieldsC))$allColumnsA=$designModelfieldsC->getAllFields($this->sid );
if(!empty($allColumnsA)){
foreach($allColumnsA as $oneC){
$this->_model->select($oneC, 0 );
}}$modelNK=WModel::get($this->sid, 'namekey');
$modelTR=WModel::getID($modelNK.'trans');
if(!empty($modelTR) && isset($this->_model->_as_cd[$modelTR] )){
$designModelfieldsC=WClass::get('design.modelfields', null, 'class', false);
if(!empty($designModelfieldsC))$allColumnsA=$designModelfieldsC->getAllFields($modelTR );
if(!empty($allColumnsA)){
foreach($allColumnsA as $oneC){
$this->_model->select($oneC, $this->_model->_as_cd[$modelTR] );
}}}}
$this->_data=$this->_model->load('o');
if(!empty($this->private)){
$logUID=WUser::get('uid');
if(!isset($this->_data->$memberUIDMap) || $this->_data->$memberUIDMap !=$logUID){
$message=WMessage::get();
$message->userW('1441751282FYFI');
return false;
}
}
if($flagParams){
WTools::getParams($this->_data, 'params');
}
if($flagPredefined){
WTools::getJSON($this->_data, 'predefined');
}
if(empty($this->_data)){
$this->_newEntity=true;
}
$justName=Output_Forms_class::getItem($this->sid, $this->_eid, $this->_data );
}else{
$this->_newEntity=true;
$select=array();
if(empty($this->_data))$this->_data=new stdClass;
if($this->getModel){
$this->_model->getAs($sid);
}
$areaRef=$this->elements[0]->area;
if(!empty($areaRef ))$this->_area=true;
$securityCheckIni=array();
$exclude=array('x','f','m');foreach($this->elements as $multiform){
$typeSplitA=explode('.',$multiform->type );
if(empty($typeSplitA[1])){
$multiform->typeNode='output';
$multiform->typeName=$typeSplitA[0];
}else{
$multiform->typeNode=$typeSplitA[0];
$multiform->typeName=$typeSplitA[1];
}
if(!empty($multiform->checktype))$this->_checkType=true;
if(!empty($multiform->map) && $multiform->sid !=0 && $multiform->typeName !='textonly'){
if(isset($multiform->private) && $multiform->private){
if($this->myEID !=$this->member->uid ) continue;
}if( substr($multiform->map, 1, 1)=='['){
$localX=strtolower( substr($multiform->map, 0, 1));
if(!empty($multiform->map) && $multiform->sid !=0 && $multiform->typeName !='textonly' && !in_array($localX , $exclude )){
$securityCheckIni[$multiform->sid][$localX][$multiform->map]=true;
}}else{
if(!in_array($multiform->typeName, array('file','multinput','media','image')) && empty($multiform->readonly))$securityCheckIni[$multiform->sid][$multiform->map]=true;
}
if($this->getModel)$this->_model->getAs($multiform->sid);
}}
$this->securityCheck=array();
foreach($securityCheckIni as $secureKey=> $secureValue){
ksort($secureValue);
$this->securityCheck[$secureKey]=array_keys($secureValue);
if(!empty($securityCheckIni[$secureKey]['p']))$this->securityCheckParams['p'][$secureKey]=$securityCheckIni[$secureKey]['p'];
}
if($this->getModel){
$this->_model->getLeftJoin();
$leftJOIN=true;
}
foreach($this->elements as $frmKey=> $multiform){
if($multiform->parent > 0)$this->_parent=true;
if(!$this->_area && $areaRef !=$multiform->area)$this->_area=true;
if(empty($multiform->map )) continue;
$map=$multiform->map;
$mapComplex=$multiform->map.'_'.$multiform->sid;
WTools::getParams($multiform );
if(isset($multiform->onlynew) && $multiform->onlynew==2){
$multiform->required=0;
$this->elements[$frmKey]->required=0;
}
$valueTable=array();
if(!empty($multiform->initial)){
if( substr($multiform->initial, 0, 8 )=='{widget:'){
$this->_calculateInitial($multiform );
}else{
$this->_data->$mapComplex=$multiform->initial;}
}elseif(!empty($multiform->ldsess)){
$maybe=WGlobals::get($map, null, 'session');
if(!empty($maybe )){
$this->_data->$map=$maybe;
}else{
if(!empty($map) && strrpos($map, '[')===false){
$select[]=$map;
}}
}elseif(!empty($multiform->ldmem)){
if(!empty($this->member->$map))$maybe=$this->member->$map;
if(!empty($maybe )){
$this->_data->$map=$maybe;
}else{
if(!empty($map) && strrpos($map, '[')===false){
$select[]=$map;
}}
}elseif(empty($multiform->noldprevious)){
$maybe=WForm::getPrev($map, $multiform->sid );
if(!empty($maybe )){
$this->_data->$mapComplex=$maybe;
}else{
if(!empty($map) && strrpos($map, '[')===false){
$select[]=$map;
}}
}
}
$justName=Output_Forms_class::getItem($this->sid, $this->_eid, $this->_data );
}
if(isset($this->formObj))$this->formObj->hidden( JOOBI_VAR_DATA.'[s][ftype]', ($complexForm?'1' : '0'));
}
if( WRoles::isNotAdmin('manager')){
$subExit=WExtension::exist('subscription.node');
if($subExit){
$subscriptionDesignrestrictionC=WClass::get('subscription.designrestriction');
if( method_exists($subscriptionDesignrestrictionC, 'checkViewRestriction')){
$restrictedElementsA=$subscriptionDesignrestrictionC->checkViewRestriction();
if(!empty($restrictedElementsA)){
$this->removeElements($restrictedElementsA );
$this->removeMenus($restrictedElementsA );
}}}
}
$this->_processPredefinedType();
if(!$this->prepareView()){
return false;
}
if(!empty($this->_checkType)){
$outputChecktypeC=WClass::get('output.checktype');
$outputChecktypeC->removeUnnecessaryElements($this );
}
$this->removeElementsProcess( self::$_removedElementsA, true, 'output_forms_class');
$this->changeElementsProcess();
WView::storeData($this->_data, true);
if($this->task !='preferences' && $this->nestedView===false){
$mytitleHeader='';
if($this->type >=51 || $this->type <=150){
if($this->_newEntity){
}else{
if($this->task=='edit')$mytitleHeader='[ '. WText::t('1206732361LXFE').' ] ';
if(isset($this->_namapsid)){
$maName=$this->_namapsid;
if(isset($this->_data->$maName))$mytitleHeader .=$this->_data->$maName;
}}}else{if(isset($this->_namapsid)){
$maName=$this->_namapsid;
if(isset($this->_data->$maName))$mytitleHeader=$this->_data->$maName;
}}
if(isset($this->_pageDescriptionColumn)){
$maName=$this->_pageDescriptionColumn;
if(isset($this->_data->$maName))$this->_pageDescription=$this->_data->$maName;
}WGlobals::set('titleheader',$mytitleHeader, '', false);}
$directEditIcon='';
if('edit'==WPref::load('PMAIN_NODE_DIRECT_MODIFY')){
$outputDirectEditC=WClass::get('output.directedit');
$directEditIcon=$outputDirectEditC->editView('view',$this->yid, $this->yid );
}elseif('translate'==WPref::load('PMAIN_NODE_DIRECT_MODIFY')){
$outputDirectEditC=WClass::get('output.directedit');
$directEditIcon=$outputDirectEditC->translateView('view',$this->yid, $this->yid, $this->name );
}WGlobals::set('directEditIcon',$directEditIcon, 'global');
if($this->subtype==17)$this->_area=true;
$boxHTML=array();
$HTML2=array();
if($this->_parent || $this->_area){
$elements=array();
$gotParent=false;
if($this->_area){
$startArea='';
foreach($this->elements as $myKeyAre=> $oneElemnet){
if(!empty($oneElemnet->hidden))$this->elements[$myKeyAre]->area='wzxhidden';elseif(empty($startArea) && !empty($oneElemnet->area))$startArea=$oneElemnet->area;}
$allElements=array();
$followArrayKey=array_keys($this->elements );
$iii=0;
foreach($this->elements as $index=> $value){
if(empty($value->area )){
if($value->hidden){
$value->area=$startArea;
$this->elements[$index]->area=$startArea;
}else{
if($this->subtype==17){
$this->elements[$index]->area=$value->map.'_'.$value->sid;
}continue;
}}
$pt=$value->parent;
if($pt > 0)$gotParent=true;
if(!empty($elements[$pt])){
$list=$elements[$pt];
}else{
$list=array();
}
array_push($list, $value );
if(!empty($followArrayKey[$iii+1]))$nextIndex=$followArrayKey[$iii+1];
else $nextIndex=0;
$iii++;
$elements[$pt]=$list;
if(empty($nextIndex) || (isset($this->elements[$nextIndex]->area) && $this->elements[$nextIndex]->area !=$startArea )){
$elements=array_reverse($elements, true);
$allElements[(string)$startArea]=$elements;
if(!empty($this->elements[$nextIndex]->area)){
$startArea=$this->elements[$nextIndex]->area;
}
unset($elements );
$elements=array();
unset($list);
$list=array();
$gotParent=false;
}
}
foreach($allElements as $area=> $areaElements){
$keys=array_keys($areaElements );
$this->_renderElements($HTML2, $areaElements, $areaElements, false, $keys );
if(!empty($HTML2[0])){
if(!empty($boxHTML[$area]))$boxHTML[$area] .=$HTML2[0]; else $boxHTML[$area]=$HTML2[0];
unset($HTML2 );
$HTML2=array();
$fakeElemt=null;
$fakeParams=null;
$frame=WForms_default::frame($fakeElemt, $fakeParams, true);
}else{
$boxHTML[$area]='';
}
}
}else{
foreach($this->elements as $value){
if($value->type=='tab')$this->_hasTabs=true;
$pt=$value->parent;
if($pt > 0)$gotParent=true;
$list=(isset($elements[$pt])?$elements[$pt] : array());
array_push($list, $value );
$elements[$pt]=$list;
}$elements=array_reverse($elements, true);
$keys=array_keys($elements );
$this->_renderElements($HTML2, $elements, $elements, false, $keys );
if(isset($HTML2[0])){
$HTML=$HTML2[0];
}else{
$HTML='';
$mess=WMessage::get();
$mess->codeW('No form elements could be displayed, check the order and parent of your fields error 1');
}
}
}else{
$this->_renderElements($HTML2, $this->elements, $this->elements, false);
if(isset($HTML2[0])){
$HTML=$HTML2[0];
}else{
$HTML='';
$message=WMessage::get();
$message->codeE('No forms elements could be displayed contact developer error 2: '.$this->namekey );
}
}
if(!$this->prepareTheme()) return false;
$fakeElemt=null;
$fakeParams=null;
if($this->_area || !empty($this->htmlfile)){
$viewThemeI=WPage::theme($this->namekey, 'html');if(!empty($viewThemeI)){
$viewThemeI->type=49;$viewThemeI->folder=$this->folder;
$viewThemeI->wid=$this->wid;
$viewThemeI->file=$this->namekey.'.php';
$viewThemeI->setData($this->_data, 'data');
if(!empty($this->_rowContent))$viewThemeI->setData($this->_rowContent, 'rowContent');
if(!empty($this->htmlfile))$viewThemeI->htmlfile=$this->htmlfile;
$HTML=$viewThemeI->display($boxHTML, $this->_setTitleA );
}else{
$HTML='';
}
}
if(isset($this->formObj)){
if(isset($this->_prefix))$this->formObj->hidden('joobiPfix' , $this->_prefix );
if(!empty($this->sid))$this->formObj->hidden( JOOBI_VAR_DATA.'[s][mid]' , $this->sid );
if(isset($this->_model)){
$mySID=$this->sid;
if(!$this->_model->multiplePK()){
$pkey=$this->_model->getPK();
$this->formObj->hidden( JOOBI_VAR_DATA.'[s][pkey]' , $pkey );
$temp_key=$pkey.'_'.$mySID;
$keyValue=(empty($pkey) || !isset($this->_data->$temp_key ))?0 :  $this->_data->$temp_key;
$this->formObj->hidden( JOOBI_VAR_DATA . "[$mySID][$pkey]" , $keyValue );
}
if( PLIBRARY_NODE_CKFRM && PLIBRARY_NODE_SECLEV > 1){
if(!isset($this->securityCheck[$mySID])){
$this->securityCheck[$mySID]=array();
}
if(isset($pkey) && !in_array($pkey, $this->securityCheck[$mySID])){
$this->securityCheck[$mySID][]=$pkey;
}
sort($this->securityCheck[$mySID]);
$listingSecurity=WGlobals::get('securityForm',array(), 'global');
if(!empty($listingSecurity)){
foreach($listingSecurity as $keyA=> $valA){
foreach($valA as $keyB=> $valB){
if(empty($this->securityCheck[$keyA]) || !in_array($valB, $this->securityCheck[$keyA] ))
$this->securityCheck[$keyA][]=$valB;
}sort($this->securityCheck[$keyA]);
}
}
if(!empty($this->securityCheck)){
foreach($this->securityCheck as $sidVal=> $valArray){
if(empty($this->securityCheckParams['p'][$sidVal])){
if(is_array($this->securityCheck[$sidVal]))$secKey=array_search('p',$this->securityCheck[$sidVal] );
if(isset($secKey) && $secKey !==false){
unset($this->securityCheck[$sidVal][$secKey] );
sort($this->securityCheck[$sidVal]);
}
}
if(empty($valArray)) unset($this->securityCheck[$sidVal] );
}}
ksort($this->securityCheck );
$eid=is_array($this->_eid)?$this->_eid : array((string)$this->_eid );
$this->securityCheck['eid']=empty($this->_eid)?'0' : serialize($eid);
$finalSecure=WTools::secureMe($this->securityCheck );
$this->formObj->hidden( JOOBI_VAR_DATA.'[s][cloud]' , $finalSecure );
}
}
$keyVal=(isset($keyVal))?$keyVal : '';
if(isset($this->_newEntity) && empty($keyVal))$this->formObj->hidden( JOOBI_VAR_DATA.'[s][new]', (int)$this->_newEntity );
$this->formObj->hidden('task_redirect',$this->_defaultTask );
if(!empty( self::$_memoryHiddenA )){
foreach( self::$_memoryHiddenA as $map7=> $val7){
$this->formObj->hidden($map7, $val7 );
}}
$addButtons=( WRoles::isNotAdmin('manager'))?((isset($this->nosave))?false : true) : false;
}
$this->content .=$HTML;
return parent::create();
}
private function _renderElements(&$HTML,$oneElements,$elements=null,$reset1=false,$keys=null){
static $formFile=array();
$first=false;
foreach($oneElements as $key=> $element){
if(is_array($element)){
$this->_renderElements($HTML, $element, $elements, $reset1 );
continue;
}
if($this->subtype==17){
$element->frame=115;}
if(empty($element->area)){
$element->area=$element->map.'_'.$element->sid;
}
if(empty($this->_setTitleA[$element->area])){
$this->_setTitleA[$element->area]=$element->name;
}
if(isset($element->private) && $element->private){
if($this->myEID !=$this->member->uid ) continue;
}$holaParams=$element;
$holaParams->id=WView::generateID('fid',$element->fid );
$holaParams->idText=str_replace('.','_',$element->namekey );
$map=(isset($element->map))?$element->map : '';
$value='';
if(!empty($map)){
WTools::getParams($element );
$wildPrefix=substr($map, 0, 2 );
if($wildPrefix=='c[' || $wildPrefix=='u['){
$Temp_map=substr($map, 2, strlen($map)-3);
$pos=strpos($Temp_map, ']');
$confPrefix=substr($Temp_map, 0, $pos) ;$confProperty=substr($Temp_map, $pos+2, strlen($map)-1-$pos);
$confPrefixUnder=str_replace('.','_',$confPrefix );
$configElement=strtoupper('P'.$confPrefixUnder.'_'.$confProperty);
if($wildPrefix=='c['){WPref::get($confPrefix );
$myPreference='P'.strtoupper( str_replace('.','_',$confPrefix ). '_'.$confProperty );
$value=WPref::load($myPreference );
}else{
$prefVa1=WPref::load($configElement );
$value=( null !==$prefVa1?$prefVa1 : (!empty($element->initial)?$element->initial : ''));
}$map=str_replace(']','',$map );
$map=str_replace('[','][',$map );
}elseif(!$this->_newEntity){
if($wildPrefix=='p[' || $wildPrefix=='j[' || $wildPrefix=='m['){
if(empty($element->sid))$element->sid=$this->sid;
$Temp_map=substr($map, 2, strlen($map) -3 );
if($wildPrefix=='m[')$Temp_map .='_'.$element->sid;
$map=str_replace(']','',$map);
$map=str_replace('[','][',$map);
$value=(isset($this->_data->$Temp_map)?$this->_data->$Temp_map : '');
}elseif($wildPrefix=='x['){
$Temp_map=substr($map, 2, strlen($map)-3 );
if(!empty($element->initial)){
$value=$this->_calculateInitial($element );
}elseif(isset($this->_data->$Temp_map)){
$value=$this->_data->$Temp_map;
}elseif(isset($element->ldeid) && $element->ldeid==1){
$value=WGlobals::getEID();
}elseif(empty($element->noldprevious)){
$value=WForm::getPrev($Temp_map );}else{
$Temp_map=substr($map, 2, -1 );
$value=(isset($this->_data->$Temp_map)?$this->_data->$Temp_map : '');
}
$map=str_replace(']','',$map);
$map=str_replace('[','][',$map);
}elseif($wildPrefix=='f['){$map=str_replace(']','',$map);
$map=str_replace('[','][',$map);
}else{
$temp_map=$map.'_'.$element->sid;
$value=(isset($this->_data->$temp_map))?$this->_data->$temp_map : '' ;
}
}else{
if($wildPrefix=='p[' || $wildPrefix=='j['){
$mapComplex=$map.'_'.$element->sid;
$map=str_replace(']','',$map);
$map=str_replace('[','][',$map);
$value=(isset($this->_data->$mapComplex)?$this->_data->$mapComplex: (isset($this->_data->$map)?$this->_data->$map : ''));
if(empty($element->sid))$element->sid=$this->sid;
}elseif($wildPrefix=='c[' || $wildPrefix=='u['){
$Temp_map=substr($map, 2, strlen($map)-3);
$pos=strpos($Temp_map, ']');
$confPrefix=substr($Temp_map, 0, $pos). '_' ;$confProperty=substr($Temp_map, $pos+2, strlen($map)-1-$pos);
$confPrefix=str_replace('.','_',$confPrefix );
$configElement=strtoupper('P'.$confPrefix.'_'.$confProperty);
if($wildPrefix=='c['){$value=WPref::load($configElement );
}else{$value=(isset($configElement )?$configElement : (!empty($element->initial)?$element->initial : ''));
}$map=str_replace(']','',$map);
$map=str_replace('[','][',$map);
}elseif($wildPrefix=='x[' || $wildPrefix=='m['){
if(!empty($element->initial)){
$value=$element->initial;
}elseif(!empty($element->ldeid)){
$value=WGlobals::getEID();
}elseif(empty($element->noldprevious)){
$value=WForm::getPrev($map );
}else{
$myRealMap=substr($map, 2, strlen($map )-3 );
$trk=WGlobals::get( JOOBI_VAR_DATA );
$valueFromRequest=(!empty($trk) && isset($trk['x'][$myRealMap]))?$trk['x'][$myRealMap] : null;
$value=(isset($this->_data->$map))?$this->_data->$map: ((!empty($valueFromRequest))?$valueFromRequest : ((!empty($element->initial))?$element->initial : ''));
}
$map=str_replace(']','',$map);
$map=str_replace('[','][',$map);
}elseif($wildPrefix=='f['){$map=str_replace(']','',$map);
$map=str_replace('[','][',$map);
}else{
if(empty($value)){
$mapComplex=$map.'_'.$element->sid;
$value=(isset($this->_data->$mapComplex))?$this->_data->$mapComplex: (isset($this->_data->$map)?$this->_data->$map : '');
}
}
}
if($this->getModel && $map==$this->_pkey)$keyVal=$value;
if($wildPrefix=='c[' || $wildPrefix=='u[' || ($wildPrefix=='x[' && empty($element->sid))){
$complexMap=JOOBI_VAR_DATA.'['.$map.']';
}else{
$complexMap=JOOBI_VAR_DATA.'['.$element->sid.']['.$map.']';
}
}else{
$complexMap=$value='';
}
if(isset($element->accflip)){
if(!in_array($element->rolid, WUser::roles())){
$element->hidden=true;
}
}
if(!$element->hidden){
$parent=$element->parent;
$classname='WForm_'.$element->typeName;
WView::includeElement($element->typeNode.'.form.'.$element->typeName );
if(!empty($element->pnamekey) && empty($element->filef)){
$explodePtA=explode('.',$element->pnamekey );
if( count($explodePtA ) > 1){
$element->filef=$explodePtA[1];
$element->currentClassName=ucfirst( WExtension::get($this->wid, 'folder')). '_'.ucfirst($explodePtA[1] ) .'_form';
}
}
if(!empty($element->currentClassName) && !empty($explodePtA)){
WLoadFile($explodePtA[0].'.form.'.$explodePtA[1] , JOOBI_DS_NODE );
$parentClassName=ucfirst($explodePtA[0] ). '_'.ucfirst($explodePtA[1] ) .'_form';
WView::includeElement($explodePtA[0].'.form.'.$explodePtA[1], null, true, true);
if(!class_exists($element->currentClassName )){
eval('class '.$element->currentClassName.' extends '.$parentClassName.' {}');
}}
if( class_exists($classname)){
$field=new $classname;
}else{
$message=WMessage::get();
$message->codeE('The following class does not exist '.$classname );
$message->codeE('Check the view :'.WView::get($this->yid, 'namekey'));
continue;
}
$holaParams->text=$element->name;
if(isset($this->_extras['fsize']))$field->fsize=$this->_extras['fsize'];
$element->yid=$this->yid;
$frame=WForms_default::frame($element, $holaParams, false, $this->subtype );
$frame->useCookies=true;
if(!$first){
$first=true;
$field->start($frame, $holaParams );
}
$element->editItem=$this->editItem;
if( WRoles::isAdmin('manager')){
if(( (int)$element->level > WGlobals::getCandy())){
if(!empty($this->securityCheck[$element->sid])){
$secKey=array_search($element->map, $this->securityCheck[$element->sid] );
if(isset($secKey) && $secKey !==false){
if(empty($secKey)){
unset($this->securityCheck[$element->sid] );
}else{
unset($this->securityCheck[$element->sid][$secKey] );
sort($this->securityCheck[$element->sid] );
}
if(empty($this->securityCheck[$element->sid])){
unset($this->securityCheck[$element->sid] );
}}
}
$element->disabled=true;
}}
$field->element=$element;
$field->data=$this->_data;
$field->value=$value;
$field->map=$complexMap;
$field->modelID=$this->sid;
$field->nodeID=$this->wid;
$field->eid=$this->_eid;
$field->form=$this->form;
$field->yid=$this->yid;
$field->wid=$this->wid;
$field->newEntry=$this->_newEntity;
$field->hasTabs=$this->_hasTabs;
$field->controller=new stdClass;
$field->controller->controller=$this->controller;
$field->controller->task=$this->task;
$field->controller->type=$this->type;
$field->_bottom=&$this->_bottom;
if(!empty($this->autofocus ))$field->autofocus=$this->autofocus;
if(isset($this->reqTask))$field->reqTask=$this->reqTask;
$field->formName=(!empty($this->firstFormName))?$this->firstFormName : WGlobals::get('parentFormid','','global');
WTools::getParams($field->element );
if(isset($field->element->disabled)){
$field->element->classes='disabled';
}
$field->idLabel=WView::generateID('form',$element->fid );
if( 151 !=$this->type && empty($element->readonly) && (empty($element->onlynew)
 || ($this->_newEntity && $element->onlynew==1 )
 || ( !$this->_newEntity && $element->onlynew==2 ))){
if(isset($element->rtag)){
static $tagC=null;
if(!isset($tagC))$tagC=WClass::get('output.process');
$tagC->replaceTags($field->value );
}
$htmlStatus=$field->displayForm();
$frame->extraClass=$field->extraClass;
if('<div></div>'==$field->content){
$field->content='';
}
if($htmlStatus===false){
$subvalueCheck=(isset($this->securityCheck[$element->sid]))?$this->securityCheck[$element->sid] : 0;
if(!empty($subvalueCheck)){
$foundOne=false;
foreach($subvalueCheck as $hKey=> $hVal){
if($hVal==$element->map){
$foundOne=$hKey;
break;
}
}
if($foundOne!==false){
unset($this->securityCheck[$element->sid][$foundOne] );
sort($this->securityCheck[$element->sid] );
}}if(empty($this->securityCheck[$element->sid])){
unset($this->securityCheck[$element->sid]);
}
}else{
$field->createJS($complexMap, $element->typeName );
}
}else{
if($element->typeName=='layout' || $element->typeName=='captcha')$frame->td_colspan=2;
if(isset($element->rtag)){
static $tagC=null;
if(!isset($tagC))$tagC=WClass::get('output.process');
$tagC->replaceTags($field->value );
}
$htmlStatus=$field->displayShow();
$keyArey=(!empty($element->area)?$element->area : WView::retreiveID('form',$element->fid ));
$this->_rowContent[$keyArey]=$field->content;
$frame->extraClass=$field->extraClass;
if($htmlStatus!==false){
if(isset($element->onlynew) && (
 ($this->editItem && $element->onlynew==1 )
  || ($this->editItem && $element->onlynew==2 )
)
 && isset($this->formObj)){
 $this->formObj->hidden($complexMap, $value );
 }
if(isset($element->lien)){
static $dataMapList=array();
if(!empty($this->_data)){
foreach($this->_data as $dkey=> $dval){
$realKey=substr($dkey, 0, strrpos($dkey, '_'));
$dataMapList[$realKey]=$dval;
}}
$outputLinkC=WClass::get('output.link');
$outputLinkC->wid=$this->wid;
$link=$outputLinkC->convertLink($element->lien, $element, '',$this->_model, $dataMapList );
$lien=new WLink($field->content );
$field->content=$lien->make($link );
}
}
}
$secondHTML=(isset($HTML[$element->fid])?$HTML[$element->fid] : '');
if(!$htmlStatus){
$frame->content='';
}
if($htmlStatus){
$field->addElementToField($frame, $holaParams, $secondHTML );  }
}else{
if(empty($element->readonly ) && $element->typeName !='hiddenload'){
if(isset($this->formObj)){$this->formObj->hidden($complexMap, $value );
}else{
self::$_memoryHiddenA[$complexMap]=$value;
}}
}
}
if(isset($field) && isset($frame)){
if($this->subtype==14){
$this->td_c='key';
$this->td_s='text-align:left;';
$frame->cell($frame->oneCellContent );
}
if($this->subtype==15 || $this->subtype==14){
$frame->line();
}
$field->close($frame );
$myview=$field->display();
if(!isset($HTML[$parent]))$HTML[$parent]='';
$HTML[$parent] .=$myview;
}
}
public static function getItem($sid=0,$eid=null,$data=null){
static $instance=array();
if(is_array($eid))$eid=implode('|',$eid );
if(!isset($instance[$sid][$eid])){
$myObj=new WObj();
if(isset($data) && !empty($data)){
foreach($data as $property=> $value){
$myObj->$property=$value;
}}$instance[$sid][$eid]=$myObj;
}
return $instance[$sid][$eid];
}
public function getValue($columnName,$modelName=null){
return WView::retreiveOneValue($this->_data, $columnName, $modelName );
}
public function setValue($PROPERTY,$value){if(!isset($this->_data->$PROPERTY)){
if(empty($this->_data ))$this->_data=new stdClass;
$this->_data->$PROPERTY=$value;
}}
protected function getElements($yid,$params=null){
$extraParams=new StdClass;
$extraParams->select=array('map','namekey','type','rolid','sid','did','parent','area','frame','ref_yid','readonly','hidden','required','private','params','level','initial','checktype','fdid','xsvisible','xshidden','devicevisible','devicehidden');$extraParams->area=true;
if(isset($params->_extras['unpublished'])){
$extraParams->notIN['map']=$params->_extras['unpublished'];
}
return parent::getSQLparent($yid, 'viewforms','fid',$extraParams, true, true);
}
private function _processPredefinedType(){
if(empty($this->sid )) return false;
$modelNamkey=WModel::get($this->sid, 'namekey');
$modelNamkeyType=$modelNamkey.'.type';
if( WModel::modelExist($modelNamkeyType)){
$modelTypeM=WModel::get($modelNamkeyType );
$pk=$modelTypeM->getPK();
$typeValue=$this->getValue($pk );
if(!empty($typeValue )){
$predefined=WModel::getElementData($modelTypeM->getModelID(), $typeValue, 'predefined');
if(empty($predefined)) return false;
$jsonObj=json_decode($predefined );
if(empty($jsonObj)) return false;
foreach($jsonObj as $k=> $v){
if('yn_'==substr($k, 0, 3) && !empty($v )){
$name=$this->namekey.'_'.substr($k, 3 );
$this->removeElements($name );
}}}
}
}
private function _check4FileInfo(){
$FileinfoA=array();
if(!empty($this->_model->_fileInfo)){
foreach($this->_model->_fileInfo as $key=> $val){
$FileinfoA[]=$key.'_'.$this->_model->_infos->sid;
}}
if(!empty($this->_model->_leftjoin)){
$asReversed=array_flip($this->_model->_as_cd );
foreach($this->_model->_leftjoin as $leftJoin){
$sid=$asReversed[$leftJoin->as2];
$tempModelM=WModel::get($sid );
if(!empty($tempModelM->_fileInfo )){
foreach($tempModelM->_fileInfo  as $key=> $val){
$FileinfoA[]=$key.'_'.$sid;
}}}}
return $FileinfoA;
}
private function _calculateInitial($multiform){
if( substr($multiform->initial, 0, 8 )=='{widget:'){
$mapComplex=$multiform->map.'_'.$multiform->sid;
$preInital=substr($multiform->initial, 0, 12 );
switch($preInital){
case '{widget:pref':
$specInitalA=explode('|', substr($multiform->initial, 12, -1 ));
$keyInitalA1=explode('=',$specInitalA[1] );
$keyInitalA2=explode('=',$specInitalA[2] );
$sessName=rtrim($keyInitalA2[1], '}');
$myPreference=strtoupper('P'.str_replace('.','_',$keyInitalA1[1] ). '_'.$sessName );
$this->_data->$mapComplex=WPref::load($myPreference );
break;
case '{widget:time':$this->_data->$mapComplex=time();
break;
case '{widget:user':$specInitalA=explode('|', substr($multiform->initial, 12, -1 ));
$keyInitalA1=explode('=',$specInitalA[1] );
$sessName=rtrim($keyInitalA1[1], '}');
$this->_data->$mapComplex=WUser::get($sessName );
break;
default:
$tagC=WClass::get('output.process');
$tagC->replaceTags($multiform->initial );
$this->_data->$mapComplex=$multiform->initial;
break;
}
if(isset($this->_data->$mapComplex)) return $this->_data->$mapComplex;
}
}
private function _checkoutItem(){
return true;
$sid=$this->_model->_infos->sid;
if(empty($sid ) || is_array($this->_eid) || empty($this->_eid)){
return false;
}
$nbPK=$this->_model->getPK();
if( count($nbPK) > 1 ) return true;
$chcekoutM=WModel::get('checkout','object');
$chcekoutM->whereE('sid',  $sid  );
$chcekoutM->whereE('eid',  $this->_eid  );
$item=$chcekoutM->load('o',array('uid','modified'));
$currentUID=WUser::get('uid');
if(!empty($item) && $currentUID!=$item->uid){
$USERNAME=WUser::get('name',$item->uid );
}else{
if(empty($item)){
$chcekoutM->sid=$sid;
$chcekoutM->eid=$this->_eid;
$chcekoutM->save();
}else{
$chcekoutM->setVal('modified', time());
$chcekoutM->whereE('sid',$sid );
$chcekoutM->whereE('eid',$this->_eid );
$chcekoutM->update();
}
if(isset($this->formObj))$this->formObj->hidden('blockedzeid' , $sid.'_'.$this->_eid );
else {
$formC=WView::form($this->firstFormName );
$formC->hidden('blockedzeid' , $sid.'_'.$this->_eid );
}}
}
}
class WForms_standard extends WElement {
var $map=null;var $value=null;var $idLabel='';var $newEntry=null;var $eid=null;var $modelID=null;
var $style=null;var $align=null;
var $data=null;
var $element=null;
public $formName=null;
var $wid=null;var $yid=null;var $hasTabs=false; 
var $content='';
var $crlf=''; 
public $extraClass='';
public $elementClassPosition='';
protected $extras=''; protected $inputType=null;protected $inputClass=null;
protected $addPreText=''; protected $addPostText=''; 
private static $_frame=array();
public static function &frame(&$element,&$params,$reset=false,$layoutSubType=0){
if($reset){
$frame=array();
$hello=new stdClass;
return $hello;
}
if($element->frame==0){
$availableType=array('tab','tabvertical','slider','tabjsfree','concatenate','div','column','row');if(!in_array($element->typeName, $availableType)){
if($layoutSubType>0)$element->frame=$layoutSubType;
else $element->frame=11;
}else{
if($layoutSubType>0)$element->frame=$layoutSubType;
else {
switch($element->typeName){
case 'tab':
$element->frame=21;
break;
case 'tabvertical':
$element->frame=27;
break;
case 'tabjsfree':
$element->frame=77;
break;
case 'div':
$element->frame=31;
break;
case 'slider':
$element->frame=51;
break;
case 'column':
$element->frame=83;
break;
case 'row':
$element->frame=84;
break;
case 'concatenate':
$element->frame=91;
break;
}}}}
$index=$element->yid.'_'.$element->frame.'_'.$element->parent;
if(!isset(self::$_frame[$index])){
$extraCSS='';
$data=new stdClass;
$data->frame=$element->frame;
$data->params=$params;
self::$_frame[$index]=WPage::renderBluePrint('frame',$data );
}
 self::$_frame[$index]->_data=$element;
return self::$_frame[$index];
}
public function addElementToField(&$frame,$params=null,$html=null){
WTools::getParams($this->element );
$myElement=new stdClass;
if(empty($this->element->spantit))$this->element->spantit=false;
if(empty($this->element->spanval))$this->element->spanval=false;
if($this->element->typeName=='addon' || $this->element->typeName=='layout' || $this->element->typeName=='cpanel'){
$this->element->frame=11;
$this->element->spantit=true;
}
if(isset($this->element->link)){
foreach($this->element as $eleKey=> $eleVal){
$myElement->$eleKey=$this->element->$eleKey;
}$myElement->name=$this->_addLink();
}else{
$myElement=$this->element;
}
$frame->elementClassPosition=$this->elementClassPosition;
$myElement->idLabel=$this->idLabel;
$myElement->yid=$this->yid;
switch($myElement->frame){
case 11:$frame->miseEnPageTwo($myElement, $this->display());
break;
case 12:$frame->miseEnPageOne($myElement, $this->display());
break;
case 14:$frame->oneCellContent .=$frame->miseEnPageBr($myElement, $this->display());
break;
case 15:$frame->miseEnPageFlat($myElement, $this->display());
break;
default:
$frame->miseEnPageTwo($myElement, $this->display());
break;
}
}
public function displayForm(){
$this->preCreate();
$status=$this->create();
$this->wrapperCreate();
return $status;
}
public function displayShow(){
$this->preShow();
$status=$this->show();
$this->wrapperShow();
return $status;
}
function createJS($complexMap,$type=''){
$requiredTypes='';
if($type=='captcha')$complexMap='captcha_verify';
if($this->element->required==1){
$this->_setMSG($this->wid, 'req');
foreach($this->element as $key=> $param){
$case=true;
switch ($key){
case 'num':
case 'inmin':
case 'outmin':
case 'inf':
case 'ccontain':
case 'cncontain':
case 'ws':
case 'sup':
case 'minlgt':
case 'autocheck':
case 'nozero':
break;
case 'inmax':
case 'outmax':
$requiredTypes.='['.$param.']';
break;
case 'sameas':
if(empty($this->_modelID))$this->_modelID=$this->modelID;
$requiredTypes.=','.$key.'('.$param.'_'.$this->_modelID.'_'.$this->yid.')';
break;
default :
$case=false;
break;
}
if($case)$this->_setMSG($this->wid, $key, $param );
}
if(!empty($requiredTypes)){
$this->_isFormExist();
if(isset($this->formName)) WPage::addJSScript("jCore.req['".$this->formName."']['".$complexMap."']='" . WTools::parseJSText(ltrim($requiredTypes,',')). " ; ';", 'validation', false);
}else{
$this->_isFormExist();
if(isset($this->formName)) WPage::addJSScript( "jCore.req['".$this->formName."']['".$complexMap."']=0;", 'validation', false);
}
}
}
protected function setRequire($map=''){
$this->setValidationMessage('req', WText::t('1206732374FDLI'));
WPage::$validation=true;
if(empty($map))$map=$this->map;
$this->_isFormExist();
if(isset($this->formName)){
WPage::addJSScript( "jCore.req['". $this->formName."']['".$map."']=0;", 'validation', false);
}
}
protected function setRequireEmail($name,$value){
$this->setValidationMessage('req', WText::t('1206732374FDLI'));
$requiredTypes=','.$name.'('.$value.')';
$this->_isFormExist();
if(isset($this->formName)){
WPage::addJSScript( "jCore.req['". $this->formName."']['".$this->map."']='".WTools::parseJSText( ltrim($requiredTypes,','))." ; ';", 'validation', false);
}
}
function start(&$frame,$params=null){
}
function close(&$frame){
$frame->body();
$this->content=$frame->make();
}
protected function addStyling($string){
if(empty($string)) return $string;
if(isset($this->element->style) || isset($this->element->align) || isset($this->element->classes)){
$html='<span';
if(!empty($this->element->classes))$html .=' class="'.$this->element->classes.'"';
if(!empty($this->element->align))$html .=' align="'.$this->element->align.'"';
if(!empty($this->element->style))$html .=' style="'.$this->element->style.'"';
$html .='>';
$html .=$string.'</span>';
return $html;
}else{
return $string;
}
}
protected function getValue($columnName,$modelName=null){
return WView::retreiveOneValue($this->data, $columnName, $modelName );
}
public function create(){
$name=(!empty($this->valueName)?$this->valueName : $this->value );
$this->content .=$this->addStyling($name );
if(!empty($this->disabled)){
$formObject=WView::form($this->formName );
$formObject->hidden($this->map, $this->value );
}
return true;
}
protected function show(){
$this->processCMStags();
$this->linkOnAdvSearch();
$this->valueName=$this->value . $this->addPostText;
return self::create();
}
protected function linkOnAdvSearch($content=''){
if(!empty($this->element->fdid ) && WModel::modelExist('design.modelfields')){
$fieldInfoO=WModel::getElementData('design.modelfields',$this->element->fdid );
if(!empty($fieldInfoO->advsearchable) && !empty($fieldInfoO->searchlink)){
$searchModelM=WModel::get($fieldInfoO->sid );
$advSearchKey=$searchModelM->getAdvSearchKey();
$AllAdvSearchA=WGlobals::getSession($advSearchKey );
$parentModel=WModel::get( WModel::get($fieldInfoO->sid, 'pnamekey'), 'sid');
if(!empty($AllAdvSearchA )){
$hasFitler=false;
foreach($AllAdvSearchA as $lid=> $Onefilter){
if($this->element->map==$Onefilter->column && $parentModel==$Onefilter->modelID){
$hasFitler=true;
break;
}}
if($hasFitler){
$link='controller='.$searchModelM->getDefaultController(). '&'.$advSearchKey.'['.$lid.']='.$this->value;
if(!empty($content))$content='<a href="'.WPages::link($link ). '">'.$content.'</a>';
else $this->value='<a href="'.WPages::link($link ). '">'.$this->value.'</a>';
}else{
$this->userE('1446308922FMFQ');
}
}
}
}
return $content;
}
protected function processCMStags(){
static $processContent=null;
if(!isset($processContent)){
if(!IS_ADMIN){
$processContent=WPref::load('PMAIN_NODE_FRAMEWORKCONTENT');
}else{
$processContent=false;
}}
if($processContent && !empty($this->element->processcontent)){
$CMSaddon=WAddon::get('api.'.JOOBI_FRAMEWORK.'.content');
if(!empty($CMSaddon))$CMSaddon->processContent($this->value );
}
}
protected function setValidationMessage($type,$message){
if(empty($message) || empty($type)) return false;
$message=WTools::parseJSText($message );
$jscript=WPage::getScript();
if(!isset($jscript['js']['onDOMready']['msg'][$type] )) WPage::storeScript( "jCore.msg['".$type."']='".$message."';", 'js','msg',$type, true);
}
private function _isFormExist(){
$formsnames=WGlobals::get('joobJSrequired');
$formObject=WView::form($this->formName );
if(is_array($formsnames)){
if(!in_array($formObject->name, $formsnames)){
 $formsnames[]=$formObject->name;
 WPage::addJSScript( "jCore.req['".$formObject->name."']=new Array();", 'validation', false);
}
}else{
 if(isset($formObject->name)){
 $formsnames[]=$formObject->name;
WPage::addJSScript( "jCore.req['".$formObject->name."']=new Array();", 'validation', false);
 }
}
WGlobals::set('joobJSrequired',$formsnames );
}
protected function _addLink(){
$class=(isset($this->element->classes ))?'btn btn-link '.$this->element->classes : 'btn btn-link';
$itemid=true;
$option=null;
if( WGlobals::get('appType','application','global')=='module'){
$pref=WPref::get($this->wid);
$itemid=$pref->getPref('itemid', true);
$option=$pref->getPref( JOOBI_URLAPP_PAGE, '');
if(strlen($option) < 3)$option=null;
}
$url=WPage::routeURL($this->element->link, '', false, false, $itemid , $option );
$onclick='';
if(isset($this->element->popup)){
$urlmain=WPage::routeURL($this->element->link, 'smart','popup');
$popwidth=(isset($this->element->popwidth ))?$this->element->popwidth : 640;
$popheigth=(isset($this->element->popheigth ))?$this->element->popheigth : 480;
if(isset($this->element->ajaxlnk )){
$normalLink=false;
$url=$urlmain;
}else{
$onclick='onClick="window.open(\''.$urlmain.'\', \'win2\', \'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=no,width='.$popwidth.',height='.$popheigth.',directories=no,location=no\');return false;"';
$normalLink=true;
}
return WPage::createPopUpLink($url, $this->element->name, $popwidth, $popheigth, $class, $this->idLabel, '',$normalLink, $onclick );
}
$url=ltrim($url, '/');
return '<a id="'.$this->idLabel.'" class="'.$class.'" href="'.$url.'" '.$onclick.'>'.$this->element->name.'</a>' ;
}
private function _setMSG($wid,$type,$value=''){
$tags=array('$'.strtoupper($type));
$values=array($value );
$FIELDNAME=(!empty($this->element->name)?$this->element->name : WText::t('1415301710DJEQ'));
switch($type){
case 'req':
$message=WText::t('1206732374FDLI');
$messageSpecific=str_replace(array('$FIELDNAME'), array($FIELDNAME),WText::t('1415301710DJER'));
break;
case 'ws':
$message=WText::t('1415377870CWLZ');
$messageSpecific=str_replace(array('$FIELDNAME'), array($FIELDNAME),WText::t('1415301710DJES'));
break;
case 'minlgt':
$MIN_LENGTH=$values[0];
$message=str_replace(array('$MIN_LENGTH'), array($MIN_LENGTH),WText::t('1213285201QMJN'));
break;
case 'num':
$message=WText::t('1206732374FDLM');
break;
case 'nozero':
$message='This field should not be zero!';
break;
case 'autocheck':
$AUTO_MESSAGE=$value[1];
$message=$AUTO_MESSAGE;
break;
case 'inf':
$MIN_VALUE=$values[0];
$message=str_replace(array('$MIN_VALUE'), array($MIN_VALUE),WText::t('1256875124DIGY'));
break;
case 'alpha':
$message=WText::t('1206732374FDLN');
break;
case 'alphanum':
$message=WText::t('1206732374FDLO');
break;
case 'sameas':
$message=WText::t('1206732374FDLP');
break;
case 'sup':
$SUP_VALUE=$values[0];
$message=str_replace(array('$SUP_VALUE'), array($SUP_VALUE),WText::t('1307373851DVDT'));
break;
case 'inf':
$INF_VALUE=$values[0];
$message=str_replace(array('$INF_VALUE'), array($INF_VALUE),WText::t('1307373851DVDU'));
break;
case 'cncontain':
$ILLEGAL_CHAR=$values[0];
$message=str_replace(array('$ILLEGAL_CHAR'), array($ILLEGAL_CHAR),WText::t('1206732374FDLQ'));
break;
default:
$message=$type;
break;
}
$message=WTools::parseJSText($message );
$jscript=WPage::getScript();
if(!empty($messageSpecific))$messageSpecific=str_replace( array('"'), "'", $messageSpecific );
if(!empty($message))$message=str_replace( array('"'), "'", $message );
if(!isset($jscript['js']['onThefly']['msg'][$type] )) WPage::storeScript( "jCore.msg['".$type."']=\"" . $message . "\";", 'js','msg',$type );
if(!empty($messageSpecific) && !isset($jscript['js']['onThefly']['msg'][$type."_" . $this->idLabel] )){
WPage::storeScript( "jCore.msg['".$type."_" . $this->idLabel ."']=\"" . $messageSpecific . "\";", 'js','msg');
}
}
}