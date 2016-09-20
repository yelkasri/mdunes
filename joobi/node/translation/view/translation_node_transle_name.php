<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Translation_Translation_node_transle_name_view extends Output_Forms_class {
function prepareQuery(){
$sid=$this->_saveIntoSession('sid');
$lgid=$this->_saveIntoSession('lgid');
$map=$this->_saveIntoSession('map');
$eidmap=$this->_saveIntoSession('eidmap');
$type=$this->_saveIntoSession('fieldtopo');
$eid=$this->_saveIntoSession('eid');
if($type=='area'){
$this->removeElements('translation_node_translate_name_name');
}else{
$this->removeElements('translation_node_translate_name_description');
}
$modelM=WModel::get($sid );
if(empty($modelM )){
$this->codeE('Could not load the model.');
return false;
}
if(!$modelM->multiplePK()) return false;
$objData=new stdClass;
$allPKSA=$modelM->getPKS();
foreach($allPKSA as $onePK){
if($onePK==' lgid'){
$modelM->whereE('lgid',$lgid );
$objData->lgid=$lgid;
}else{
$otherValue=$this->_saveIntoSession($onePK );
$modelM->whereE($onePK, $otherValue );
$objData->$onePK=$otherValue;
}}
$myTranslation=$modelM->load('lr',$map );
WGlobals::set('elementSID',$sid );
if($type=='area'){
$objData->description=$myTranslation;
}else{
$objData->name=$myTranslation;
}
$objData->sid=$sid;
$objData->map=$map;
$objData->eid=$eid;
$objData->type=$type;
$objData->eidmap=$eidmap;
WGlobals::set('eidmap',$eidmap );
WGlobals::set('type',$type );
WGlobals::set('map',$map );
WGlobals::set('sid',$sid );
WGlobals::set('name',$myTranslation );
WGlobals::set('description',$myTranslation );
$mainModelName=WModel::get($sid, 'mainmodel');
$myModel=WModel::get($mainModelName, null, null, false);
if(empty($myModel) || !method_exists($myModel, 'secureTranslation')){
$this->codeE('The page is not secure so to prevent any problem access is denied!');
return false;
}
if(!$myModel->secureTranslation($sid, $eid )) return false;
if(!empty($objData))$this->addData($objData );
else {
$this->userN('1260434893HJHQ');
}
return true;
}
private function _saveIntoSession($map){
if('eid'== $map){
$val=WGlobals::getEID();
} else $val=WGlobals::get($map );
if(!empty($val)){
WGlobals::setSession('translateElement',$map, $val );
}else{
$val=WGlobals::getSession('translateElement',$map );
}
return $val;
}
}