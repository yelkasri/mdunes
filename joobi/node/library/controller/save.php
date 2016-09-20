<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class WController_save {
private $_loadFileFieldNameA=array();
function save(&$localObj,$getlastId=false){
  WTools::checkRobots();
if(empty($localObj->sid)){
return false;
}
$truc=WGlobals::get( JOOBI_VAR_DATA, array(), '','array');
if(empty($truc)) return false;
if(!WController::verifySpoof()){
$message=WMessage::get();
$message->exitNow('There was a security problem! Click back on your browser and try again.');
}
$status=true;
$localObj->_model=WModel::get($localObj->sid, 'objectfields');
if(empty($localObj->_model)){
return false;
}
$localObj->_model->setAudit();
$fileTrucs=$this->getUploadedFiles();
$this->_loadFileFieldNameA=WGlobals::get('laod-fild',array(), '','array');if(!empty($fileTrucs) && !empty($this->_loadFileFieldNameA))$this->getTruc($localObj, $this->getFilesInfo($fileTrucs ));else $this->getTruc($localObj );
if($getlastId)$localObj->_model->returnId(true);
$processTags=WGlobals::get('tagsproz', 0, 'int');
if($processTags==1){
$localObj->processTags($truc );
}
$savedStatus=$localObj->_model->save();
if($localObj->_model->_new){$type='create';
}else{
$type='update';
}
$status=$localObj->showM($savedStatus ,$type, 1, $localObj->sid );
if($localObj->getControllerSaveOrder() && $localObj->_model->getModelSaveOrder()){
$this->_setOrder($localObj );
}
$pKey=$localObj->_model->getPK();
if(!empty($localObj->_model->$pKey)){
$localObj->_eid=$localObj->_model->$pKey;
}
return $savedStatus;
}
function delete(&$localObj){
if(empty($localObj->sid)){$localObj->sid=WView::get($localObj->yid, 'sid');
if(empty($localObj->sid)){
$localObj->sid=WModel::get( str_replace('-','.',$localObj->controller), 'sid', null, false);
}}
$localObj->_model=WModel::get($localObj->sid );
if(empty($localObj->_model)) return false;
$localObj->_model->setAudit();
if($localObj->_model->multiplePK()){
$myPKss=$localObj->_model->getPKs();
if(!empty($myPKss)){
foreach($myPKss as $jpk){
$valuePK=WForm::getPrev($jpk );
if(!empty($valuePK)){
$localObj->_model->$jpk=$valuePK;
$localObj->_model->whereE($jpk, $valuePK );
}else{
return false;
}}}
$localObj->_model->delete();
return true;
}
if(empty($localObj->_model)){
return false;
}
if(!is_array($localObj->_eid)) return false;
$status=true;
$orderingExist=$localObj->_model->getParam('ordrg',false);
if($orderingExist){
if($localObj->_model->getParam('grpmap',false) && !empty($localObj->_eid[0])){
$groupingMap=$localObj->_model->getParam('grpmap');
$groupingVal=$localObj->_model->load($localObj->_eid[0], $groupingMap );
}else{
$orderingExist=false;}}
foreach($localObj->_eid as $eid){
if(!$localObj->_model->delete($eid )){
$status=false;
}}
WGlobals::setEID( 0 );
$nb=sizeof($localObj->_eid );
if($orderingExist){
$localObj->_model->$groupingMap=$groupingVal;
$this->_setOrder($localObj );
}
return $localObj->showM($status , 'delete',$nb, $localObj->sid );
}
function deleteall(&$localObj){
if(empty($localObj->sid)){if(!empty($localObj->yid))$localObj->sid=WView::get($localObj->yid, 'sid', null, null, false);
if(empty($localObj->sid)){
$localObj->sid=WModel::get( str_replace('-','.',$localObj->controller), 'sid', null, false);
}}
$localObj->_model=WModel::get($localObj->sid );
if(!isset($localObj->_model)) return false;
if(!is_array($localObj->_eid)) return false;
$status=true;
$localObj->_model->setAudit();
foreach($localObj->_eid as $eid){
$status=($localObj->_model->deleteAll($eid ) && $status );
if(!$status ) break;}
WGlobals::setEID( 0);
$orderingExist=$localObj->_model->getParam('ordrg',false);
$nb=sizeof($localObj->_eid );
if($orderingExist){
$this->_setOrder($localObj );
}
$localObj->showM($status ,  'delete',$nb, $localObj->sid );
return true;
}
function copy(&$localObj){
$nd=$qSet=null;
$status=true;
$errId=array();
$nb=0;
if(!empty($localObj->_eid)){
$localObj->_model=WModel::get($localObj->sid );
if(empty($localObj->_model)) return false;
$localObj->_model->setAudit('copy');
if(is_array($localObj->_eid)){
foreach($localObj->_eid as $key=> $value){
$value=(int)$value;
if(!$localObj->_model->copy($value )){
$status=false;
$errId[]=$value;
}$nb++;
}
}
$nb=sizeof($localObj->_eid );
$errText=implode('  ',$errId );
WGlobals::setEID( 0 );
$orderingExist=$localObj->_model->getParam('ordrg', false);
if($orderingExist){
$this->_setOrder($localObj );
}
$localObj->showM($status , 'copy',$nb, $localObj->sid );
return true;
}
}
function copyall(&$localObj){
if(!is_array($localObj->_eid)) return false;
$localObj->_model=WModel::get($localObj->sid );
if(empty($localObj->_model)) return false;
$localObj->_model->setAudit('copy');
$status=true;
foreach($localObj->_eid as $eid){
if(!$localObj->_model->copyAll($eid )){
$status=false;
}}
$nb=sizeof($localObj->_eid );
WGlobals::setEID( 0);
$orderingExist=$localObj->_model->getParam('ordrg',false);
if($orderingExist){
$this->_setOrder($localObj );
}
$localObj->showM($status , 'copy',$nb, $localObj->sid );
return true;
}
function getTruc(&$localObj,$anotherA=array()){
$libraryDataC=WClass::get('library.data');
$trk=$libraryDataC->processSubmittedData();
if(empty($trk)) return true;
$key=array_keys($trk );
$truc=$trk[ $key[0] ];
if(!empty($anotherA)){
foreach($anotherA as $anotherAKey=> $anotherAVal){
$trk[$anotherAKey]['wfiles']=$anotherAVal['wfiles'];
}}
$modelNamkey=WModel::get($localObj->sid, 'namekey');
if(empty($modelNamkey)) return ;
$extraSID=WGlobals::get('mlt-s_extra',array());
if(!empty($extraSID)){
foreach($extraSID as $extrakey=> $extraValA){
foreach($extraValA as $extraval){
if( substr($extraval, 1, 1)=='['){
$typeMap=substr($extraval, 0, 1);
$realMap=substr($extraval, 2, -1 );
if(!isset($trk[$extrakey][$typeMap][$realMap])){
$trk[$extrakey][$typeMap][$realMap]=array();
}
}else{
if(!isset($trk[$extrakey][$extraval])){
$trk[$extrakey][$extraval]=array();
}}
}
}
$localObj->_model->_mlt_s_extra=$extraSID;
}
$securityCheck=array();
if(!empty($this->_loadFileFieldNameA)){
$securityFieldA=array();
foreach($this->_loadFileFieldNameA as $fieldk=> $fieldv){
foreach($fieldv as $fieldk2=> $fieldv2){
$securityFieldA[]=$fieldk.'_'.$fieldv2;
unset($trk[$fieldk][$fieldv2] );
}}sort($securityFieldA );
$securityCheck['sec']=$securityFieldA;
}
$modelTempM=WModel::get($modelNamkey, 'objectfields');
if(!empty($modelTempM->_fileInfo) && is_array($modelTempM->_fileInfo)){
$FileinfoA=$modelTempM->_fileInfo;
foreach($FileinfoA as $kf=> $checkfileO ){
if(empty($checkfileO->format)){
$FileinfoA[$kf]->format=array('jpg','png','gif','jpeg');
}else{
if(!is_array($checkfileO->format)){
$checkfileO->format=explode(',',$checkfileO->format );
}
$safeFormatA=array();
$unsafeA=array('inc','phps','class','php3','php4','js','exe','htaccess');
foreach($checkfileO->format as $ft){
$ft=trim($ft);
if(false !==strpos($ft, 'php') || in_array($ft, $unsafeA )){
continue;
}$safeFormatA[]=$ft;
}$FileinfoA[$kf]->format=$safeFormatA;
}
}
}else{
$FileinfoA=array();
}
foreach($trk as $tSid=> $tporperty){
if($tSid=='s') continue;
ksort($tporperty);
if(!empty($tSid)){
$tporperty2=$tporperty;
if($tSid=='x')$tporperty2='';if(isset($tporperty2['x'])) unset($tporperty2['x'] );if(isset($tporperty2['m'])) unset($tporperty2['m'] );
if(isset($tporperty2['f'])) unset($tporperty2['f'] );
if(isset($tporperty2['wfiles'])) unset($tporperty2['wfiles'] );
if(empty($FileinfoA)) foreach($FileinfoA as $$FileinfoAK=> $FileinfoAV ) if(isset($tporperty2[$FileinfoAK])) unset($tporperty2[$FileinfoAK] );
if(!empty($tporperty2))$securityCheck[$tSid]=array_keys($tporperty2);
}
if($localObj->sid==$tSid && isset($localObj->_model)){
$localObj->_model->addProperties($tporperty );
}elseif($tSid!=0){
$childName='C'. $tSid;
if(!isset($localObj->_model))$localObj->_model=new stdClass;
if(!isset($localObj->_model->$childName))$localObj->_model->$childName=new stdClass;
foreach($tporperty as $ppKey=> $ppval){
$localObj->_model->$childName->$ppKey=$ppval;
}
}
}
if($localObj->checkSecureForm && PLIBRARY_NODE_CKFRM && PLIBRARY_NODE_SECLEV > 1){
ksort($securityCheck);
$newElement=(!empty($trk['s']['new'])?$trk['s']['new'] : 0 );
if($newElement){
$eid=array();
}else{
$eid=WGlobals::getEID(true);
}
$securityCheck['eid']=empty($eid)?'0' : serialize($eid);$formSecure=( ! empty($trk['s']['cloud'] )?$trk['s']['cloud'] : '');
if(!WTools::checkSecure($securityCheck, $formSecure )){
$message=WMessage::get();
$message->exitNow('A security error occurred. It might happen when you keep a window open for too long. Please reload your page.');
}
}
}
public function getFilesInfo($trk=null,$modelID=0){
if(!isset($trk))$trk=WGlobals::get( JOOBI_VAR_DATA, array(), 'FILES','array');
$requestTrucs=WGlobals::get( JOOBI_VAR_DATA );
$formType=$requestTrucs['s']['ftype'];
$mapArray=array();
$fileArray=array();
$errorFile=array();
foreach($trk as $fileParams=> $values){
if(!empty($values)){
$i=0; foreach($values as $sidKey=> $sidValue){
foreach($sidValue as $sidValueK=> $sidValueV){
if($fileParams=='error' && $sidValueK!=0){$errorFile[$sidKey]=$sidValueK;
break;
}}
if($formType){
foreach($sidValue as $key1=> $valueFinal){
$mapArray[$key1]=true;
if(is_array($valueFinal )){
$arrayKey=key($valueFinal);
$name=$arrayKey;
$val=$valueFinal[$arrayKey];
}else{
$name=$key1;
$val=$valueFinal;
}if(!isset($fileArray[$sidKey]['wfiles'][$name][$i]))$fileArray[$sidKey]['wfiles'][$name][$i]=new stdClass;
$fileArray[$sidKey]['wfiles'][$name][$i]->$fileParams=$val;
$fileArray[$sidKey]['wfiles'][$name][$i]->map=$name;
$fileArray[$sidKey]['wfiles'][$name][$i]->multiple=true;
$i++;
}
}else{
foreach($sidValue as $key1=> $valueFinal){
if(is_array($valueFinal )){
$arrayKey=key($valueFinal);
$name=$arrayKey;
$val=$valueFinal[$arrayKey];
}else{
$name=$key1;
$val=$valueFinal;
}if(!isset($fileArray[$sidKey]['wfiles'][$name][$i]))$fileArray[$sidKey]['wfiles'][$name][$i]=new stdClass;
$fileArray[$sidKey]['wfiles'][$name][$i]->$fileParams=$val;
$fileArray[$sidKey]['wfiles'][$name][$i]->map=$name;
$fileArray[$sidKey]['wfiles'][$name][$i]->multiple=true;
$i++;
}
}
}
}}
if(!empty($errorFile)){
foreach($errorFile as $myfileerrorK=> $myfileerrorV){
unset($fileArray[$myfileerrorK]['wfiles'][$myfileerrorV] );
}}
if(!empty($modelID)){
$modelID=WModel::get($modelID, 'sid');
if(isset($fileArray[$modelID]['wfiles'])) return $fileArray[$modelID]['wfiles'];
else return null;
}else{
return $fileArray;
}
}
public function getUploadedFiles($needSanitize=true){
$fileTrucs=array();
$filesFancyuploadC=WClass::get('files.fancyupload');
$fancyFileUpload=$filesFancyuploadC->check();
if($fancyFileUpload){
$axFilesA=WGlobals::get('ax-uploaded-files',array(), 'request','array');
if(!empty($axFilesA )){
foreach($axFilesA as $asMap=> $oneFileAx){
$asMapA=explode('_',$asMap );
$axModelID=array_shift($asMapA );
$axMapID=implode('_',$asMapA );
if(!is_array($oneFileAx))$oneFileAx=array($oneFileAx );
foreach($oneFileAx as $oneFileValeu){
if(empty($oneFileValeu)) continue;
$fileValues=json_decode( html_entity_decode( stripslashes($oneFileValeu )) );
if(empty($fileValues)) continue;
$randomKey=WGlobals::getSession('upld','rdmKy');
$fileTrucs['name'][$axModelID][][$axMapID]=$fileValues->name;
$fileTrucs['type'][$axModelID][][$axMapID]=$fileValues->type;
$fileTrucs['tmp_name'][$axModelID][][$axMapID]=JOOBI_DS_TEMP.'uploads'.DS.$randomKey.DS.$fileValues->name;
$fileTrucs['error'][$axModelID][][$axMapID]=0;
$fileTrucs['size'][$axModelID][][$axMapID]=$fileValues->size;
}
}
}
}else{
$fileTrucs=WGlobals::get( JOOBI_VAR_DATA, array(), 'FILES','array');
}
if(!empty($fileTrucs)){
if($needSanitize){
$fileSanitizeC=WClass::get('files.sanitize');
}
foreach($fileTrucs['tmp_name'] as $sid=> $mapA){
foreach($mapA as $none=> $finalMap){
if(is_array($finalMap)){
foreach($finalMap as $map=> $tmpName){
$name=$fileTrucs['name'][$sid][$none][$map];
$typeA=explode('.',$name );
$type=array_pop($typeA );
if($needSanitize && ! $fileSanitizeC->validateFile($name, $type, $tmpName )){
unset($fileTrucs['name'][$sid][$none][$map] );
unset($fileTrucs['type'][$sid][$none][$map] );
unset($fileTrucs['tmp_name'][$sid][$none][$map] );
unset($fileTrucs['error'][$sid][$none][$map] );
unset($fileTrucs['size'][$sid][$none][$map] );
}
}
}else{
$map=$none;
$tmpName=$finalMap;
$name=$fileTrucs['name'][$sid][$map];
$typeA=explode('.',$name );
$type=array_pop($typeA );
if($needSanitize && ! $fileSanitizeC->validateFile($name, $type, $tmpName )){
unset($fileTrucs['name'][$sid][$map] );
unset($fileTrucs['type'][$sid][$map] );
unset($fileTrucs['tmp_name'][$sid][$map] );
unset($fileTrucs['error'][$sid][$map] );
unset($fileTrucs['size'][$sid][$map] );
}
}
}
}
}
return $fileTrucs;
}
private function _setOrder(&$localObj){
$orderSID=$localObj->_model->getModelID();
$groupings=array();
if($localObj->_model->getParam('ordg',false))
return;
$groupingMap=$localObj->_model->getParam('grpmap');
$pKeys=$localObj->_model->getPKs();
if($localObj->_model->multiplePK()){
$arraDiff=array_diff($pKeys, array($groupingMap));
$pKey=reset($arraDiff );
} else  $pKey=$localObj->_model->getPK();
if(!isset($localObj->_model->$groupingMap)){
return;}$localObj->_model->select('ordering');
$localObj->_model->orderBy('ordering');
$localObj->_model->whereE($groupingMap,  $localObj->_model->$groupingMap );
$localObj->_model->setLimit( 5000 );
$values=$localObj->_model->load('ol',$pKeys);
if(empty($values)) return;
$localObj->_order=array();
$localObj->_eid=array();
foreach($values as $value){
$localObj->_order[]=$value->ordering;
$localObj->_eid[]=$value->$pKey;
}
$localObj->_groupingValue=$localObj->_model->$groupingMap;
$localObj->saveorder();
}
}