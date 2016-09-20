<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class WListing_Coretypes extends WListings_default{
function create(){
$typeObj=$this->_setTypeObject();
if(empty($typeObj)) return false;
$this->content=$typeObj->getName($this->value );
return true;
}
private function _setTypeObject(){
static $types=array();
$nodeName=WExtension::get($this->nodeID, 'folder');
$fileName=(!empty($this->element->filef)?$nodeName.'.'.$this->element->filef : $nodeName.'.'.$this->element->map );
if(empty($fileName)) return false;
if(!isset($types[$fileName] )){
$pos=strrpos($fileName, ".");
if($pos===false){
$dir=WExtension::get($this->nodeID, 'folder').'.'. $fileName;
}else{
$dir=$fileName;
}
$types[$fileName]=WType::get($dir );
if(!is_object($types[$fileName]))$types[$fileName]=0;
}
if(!empty($types[$fileName])){
return $types[$fileName];
}
return false;
}
public function advanceSearch(){
$lid=$this->element->lid;
$typeObj=$this->_setTypeObject();
if(empty($typeObj)) return false;
$allListA=$typeObj->getList(true);
if(empty($allListA)) return false;
$oneDropA=array();
$hasNone=false;
foreach($allListA as $oneKey=> $oneVal){
$oneDropA[]=WSelect::option($oneKey, $oneVal );
if(empty($oneKey))$hasNone=true;
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
public function advanceSearchLinks($memory,$sessionKey,$controller,$task){
$this->content='notDefinedYet types';
return 'notDefinedYet types';
}
}