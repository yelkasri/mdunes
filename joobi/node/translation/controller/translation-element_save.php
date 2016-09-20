<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Translation_element_save_controller extends WController {
function save(){
$sid=self::getFormValue('sid');
$eid=self::getFormValue('eid');
if(is_array($eid))$eid=$eid[0];
$type=self::getFormValue('type');
$map=self::getFormValue('map');
$eidmap=self::getFormValue('eidmap');
$lgid=self::getFormValue('lgid');
if($type=='area'){
$value=self::getFormValue('description','x', null, 'html');
}else{
$value=self::getFormValue('name');
}
if(empty($sid)) return false;
$mainModelName=WModel::get($sid, 'mainmodel');
$myModel=WModel::get($mainModelName );
if(empty($myModel) || !method_exists($myModel, 'secureTranslation')){
return false;
}
if(!$myModel->secureTranslation($sid, $eid )) return false;
$modelM=WModel::get($sid, null, null, false);
if(empty($modelM)) return false;
if('shorturl'==$map){
$modelM->lgid=$lgid;
$modelM->$eidmap=$eid;
$modelM->$map=$value;
$modelM->auto=5;
$modelM->save();
return true;
}else{
$modelM->whereE('lgid',$lgid );
$modelM->whereE($eidmap, $eid );
$modelM->setVal($map, $value );
$modelM->setVal('auto', 5 );
$modelM->update();
if($modelM->affectedRows() < 1){
$modelM->setVal('lgid',$lgid );
$modelM->setVal($eidmap, $eid );
$modelM->setVal($map, $value );
$modelM->setVal('auto', 5 );
$modelM->insertIgnore();
}
}
WGlobals::set('sid',$sid );
WGlobals::set('lgid',$lgid );
WGlobals::set('type',$type );
WGlobals::set('map',$map );
WGlobals::set('eidmap',$eidmap );
WGlobals::setEID($eid );
WGlobals::set($eidmap, $eid );
$dbtid=WModel::get($sid, 'dbtid');
if(!empty($dbtid)){
$populateM=WModel::get('translation.populate');
$populateM->makeLJ('library.columns','dbcid','dbcid');
$populateM->makeLJ('library.table','dbtid','dbtid', 1, 2 );
$populateM->whereE('eid',$eid );
$populateM->whereE('dbtid',$dbtid, 2 );
$populateM->whereE('name',$map, 1 );
$myIMAC=$populateM->load('lr',array('imac'));
if(!empty($myIMAC)){
$code=WLanguage::get($lgid, 'code');
$dictionaryM=WModel::get('translation.'.$code, 'object', null, false);
if(!empty($dictionaryM)){
$dictionaryM->setVal('auto', 5 );$dictionaryM->setVal('text',$value );
$dictionaryM->whereE('imac',$myIMAC );
$dictionaryM->update();
}}
}
return true;
}
}