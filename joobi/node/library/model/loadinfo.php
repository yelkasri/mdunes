<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class WModel_Loadinfo {
public function getModelInformation($modelName,$eid,$property='data',$lgid=null){
static $allModelInformationA=array();
if(empty($modelName) || empty($eid)) return false;
$modelID=WModel::getID($modelName );
if(empty($modelID)) return false;
$multipleValues=false;
$modelMemoryM=WModel::get($modelName );
if($modelMemoryM->hasMemory()){
$returnedA=$modelMemoryM->loadMemory($eid, $lgid, false);
}else{
$PK='';
$returnedA=$this->_loadDataFromModel($modelID, $modelName, $eid, $multipleValues, $PK, $lgid );
}if($multipleValues){
$finalReturnedInfo=array();
foreach($returnedA as $oneReturned){
if(!isset($oneReturned->$PK)) continue;
else $PKValue=$oneReturned->$PK;
$allModelInformationA[$modelID][$PKValue]=$oneReturned;
$finalReturnedInfo[$PKValue]=$this->_formatReturnObject($oneReturned, $property );
}return $finalReturnedInfo;
}else{
$allModelInformationA[$modelID][$eid]=$returnedA;
return $this->_formatReturnObject($returnedA, $property );
}
return false;
}
private function _formatReturnObject($object,$property){
if('params'==$property){
if(isset($object->$property)) return $object->$property;
}
if(isset($object->params) && ! is_array($property)){
WTools::getParams($object );
}
if('data'==$property || empty($property)){
return $object;
}elseif( is_string($property)){
if(isset($object->$property)) return $object->$property;
else return null;
}elseif(is_array($property)){$newObject=new stdClass;
foreach($property as $oneP){
if(isset($object->$oneP))$newObject->$oneP=$object->$oneP;
}if(isset($newObject->params)){
WTools::getParams($newObject );
}return $newObject;
}
return false;
}
private function _loadDataFromModel($modelID,$modelName,$eid,&$multipleValues,&$PK,$lgid=null){
$modelInstanceM=WModel::get($modelID, 'object');
if(empty($modelInstanceM)) return false;
if($modelInstanceM->multiplePK()) return false;
$modelNamekey=WModel::get($modelName, 'namekey', null, false);
$modelIDTrans=WModel::getID($modelNamekey.'trans');
if(!empty($modelIDTrans)){
$modelInstanceM->makeLJ($modelIDTrans );
$modelInstanceM->whereLanguage( 1, $lgid );
$modelInstanceM->select('*', 1 );
}$modelInstanceM->select('*');
$PK=$modelInstanceM->getPK();
$typeREturn='o';
if( is_numeric($eid)){
$modelInstanceM->whereE($PK, $eid );
}elseif( is_string($eid)){
$exist=$modelInstanceM->columnExists('namekey');
if($exist)$modelInstanceM->whereE('namekey',$eid );
else return false;
}elseif(is_array($eid)){
$eidInt=array();
$eidString=array();
$typeREturn='ol';
foreach($eid as $oneIED){
if( is_int($oneIED)){
 $eidInt[]=$oneIED;
}elseif( is_string($oneIED)){
$eidString[]=$oneIED;
}}
$needOR=false;
if(!empty($eidInt)){
$modelInstanceM->whereIn($PK, $eidInt );
$needOR=true;
}
if(!empty($eidString)){
$hasNamekey=false;
$exist=$modelInstanceM->columnExists('namekey');
if($exist){
if($needOR)$modelInstanceM->operator('OR');
$modelInstanceM->whereIn('namekey',$eidString );
$hasNamekey=true;
}
}
if(!$needOR && !$hasNamekey ) return false;
else $multipleValues=true;
}
return $modelInstanceM->load($typeREturn );
}
}