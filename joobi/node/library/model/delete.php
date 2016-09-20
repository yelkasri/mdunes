<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class WModel_Delete {
public function delete($eid=null,$deleteTrans=false,&$localObj){
$coreArray=&$this->_coreArray();
if(!$localObj->_validate){
if(!empty($localObj->_whereValues)){
return $localObj->deleteParent();
}elseif(!empty($eid)){
return $localObj->deleteParent($eid );
}  else {
$message=WMessage::get();
$message->adminW('You need a condition, you cannot delete the entire table.');
return false;
}}if(empty($eid)){
$tempWhere=$localObj->_whereValues;
$printOnly=$localObj->_printOnly;
$leftJoin=$localObj->_leftjoin;
}
if(!$localObj->_noCoreCheck && !isset($coreArray[$localObj->getModelID()])){
$columnM=WModel::get('library.columns');
$columnM->whereE('dbtid',$localObj->getTableId());
$columnM->whereE('name','core');
$coreArray[$localObj->getModelID()]=$columnM->exist();
}if(!$localObj->_noCoreCheck && !empty($coreArray[$localObj->getModelID()])){
if(isset($eid)){$my3PK=$localObj->getPK();
$localObj->whereE($my3PK, $eid );
$core=$localObj->load('lr','core');
}else{ $localObj->setReset(false);
$core=$localObj->load('lr','core');
$localObj->setReset(true);
}
if($core >=1){
$message=WMessage::get();
$message->userN('1298350423SVOJ');
$message->userN('1206961856OFMQ');
$localObj->skipMessage();
return true;
}
}
if($localObj->multiplePK()){
$eids[0]='_id_';
}else{
if(empty($eid)){
$localObj->resetAll();
$localObj->_whereValues=$tempWhere;
$localObj->_leftjoin=$leftJoin;
$localObj->setLimit( 5000000 );
$eids=$localObj->load('lra',$localObj->getPK());
if(!empty($eids )){
$status=true;
if( count($eids) > 1){$foreignM=WModel::get('library.foreign');
$foreignM->whereE('ref_dbtid',$localObj->getTableId());
$foreignM->whereE('ondelete', 3 );
$FKexist=$foreignM->exist();
if($FKexist || method_exists($localObj, 'deleteValidate') || method_exists($localObj, 'deleteExtra')){
foreach($eids as $eid){
if(empty($eid)) continue;
$status2=$localObj->delete($eid );
if($status2 ){
if($printOnly)$status.=$status2;
}else{
$status=false;
}
}
}else{$localObj->resetAll();
$localObj->_printOnly=$printOnly;
$localObj->_whereValues=$tempWhere;
$localObj->_leftjoin=$leftJoin;
return $localObj->deleteParent();
}
}else{
foreach($eids as $eid){
if(empty($eid)) continue;
$status2=$localObj->delete($eid );
if($status2 ){
if($printOnly)$status.=$status2;
}else{
$status=false;
}
}}return $status;
}elseif($printOnly && !empty($tempWhere)){
$localObj->_printOnly=$printOnly;
$localObj->_whereValues=$tempWhere;
$localObj->_leftjoin=$leftJoin;
return $localObj->deleteParent();
}else{
return false;
}
}
if(is_array($eid)){
$eids=$eid;
}else{
$eids[0]=$eid;
}}
if(!empty($localObj->_singleDelete)){
$status=$localObj->deleteParent($eids );
return $status;
}
static $securityAuditA=null;
foreach($eids as $eid){
if(!$localObj->_doNotDeleteValidate)$status=( method_exists($localObj, 'deleteValidate'))?$localObj->deleteValidate($eid ) : true;
else $status=true;
if(!$status) return false;
if( @is_array($eid )){
$eids=trim( implode(',' , $eid ), ',');
if(!empty($eids)){
$localObj->whereIn($localObj->getPK(), $eids );
$status=$localObj->deleteParent();
}else{
$status=false;
}}elseif($localObj->multiplePK()){
if($localObj->getAudit()){
if(!isset($securityAuditA))$securityAuditA=WClass::get('security.audit', null, 'class', false);
if(!empty($securityAuditA)){
$savedWhere=$localObj->_whereValues;
$securityAuditA->beforeDelete( 0, $localObj->getModelID(), $savedWhere );
}}
if(!empty($localObj->_whereValues)){
$alreadyValuedPKEYA=array();
foreach($localObj->_whereValues as $oneValue ) if(!empty($oneValue->value))$alreadyValuedPKEYA[$oneValue->champ]=$oneValue->value;
$alreadyValuedPKEYAkey=array_keys($alreadyValuedPKEYA );
}
foreach($localObj->getPKs() as $primK){
$primV=WGlobals::get($primK );
if(empty($alreadyValuedPKEYAkey) || in_array($primK, $alreadyValuedPKEYAkey)) continue;
if(!empty($primV)){
if(!($deleteTrans && $primK=='lgid' &&$localObj->getType()=='20'))
$localObj->whereE($primK, $primV );
}elseif(!empty($localObj->$primK )){
if(!($deleteTrans && $primK=='lgid' && $localObj->getType()=='20')){
if(is_array($localObj->$primK)){
$tempArray=$localObj->$primK;
sort($tempArray);
$localObj->whereE($primK, $tempArray[0] );
}else{
$localObj->whereE($primK, $localObj->$primK );
}}
}else{
if(empty($localObj->_whereValues )) return false;
}
}
if(!empty($localObj->_whereValues ))$status=$localObj->deleteParent();
else $status=true;
if($localObj->getAudit() && $status){
if(!empty($securityAuditA))$securityAuditA->afterDelete( 0, $localObj->getModelID(), $savedWhere );
}
}else{
if($localObj->getAudit()){
if(!isset($securityAuditA))$securityAuditA=WClass::get('security.audit', null, 'class', false);
}
if($eid !=null){
if($localObj->_keepAttributesOnDelete && method_exists($localObj, 'deleteExtra')){
$pKey=$localObj->getPK();
$localObj->whereE($pKey, $eid );
$data=$localObj->load('o');
$localObj->addProperties($data, '_');
}
if($localObj->getAudit()){
if(!empty($securityAuditA))$securityAuditA->beforeDelete($eid, $localObj->getModelID());
}
$status=$localObj->deleteParent($eid );
if($localObj->getAudit() && $status){
if(!empty($securityAuditA))$securityAuditA->afterDelete($eid, $localObj->getModelID());
}
}else{
if($localObj->getAudit()){
if(!empty($securityAuditA) && isset($localObj->$pkey))$securityAuditA->beforeDelete($localObj->$pkey, $localObj->getModelID());
}
$pkey=$localObj->getPK();
if(isset($localObj->$pkey))$status=$localObj->deleteParent($localObj->$pkey );
if($localObj->getAudit() && $status){
if(!empty($securityAuditA) && isset($localObj->$pkey))$securityAuditA->afterDelete($localObj->$pkey, $localObj->getModelID());
}
}
}
if($status){
if( method_exists($localObj, 'deleteExtra')){
if(!$localObj->deleteExtra($eid )) return false;
}
}else{
return $status;
}
}
return $status;
}
public function deleteAll($eid=0,&$localObj){
static $safeguard=array();
$coreArray=&$this->_coreArray();
if( @is_array($eid)){
foreach($eid as $id){
if(!$localObj->deleteAll($id )){
return false;
}}return true;
}elseif($eid < 1){
if($localObj->multiplePK()){
return $localObj->delete( null, true);
}else{$localObj->setLimit( 10000 );
$allIDs=$localObj->load('lra',$localObj->getPK());
if(!empty($allIDs)){
return $localObj->deleteAll($allIDs );
}}
return true;
}else{
if(!$localObj->_noCoreCheck && !isset($coreArray[$localObj->getModelID()])){
$columnM=WModel::get('library.columns');
$columnM->whereE('dbtid',$localObj->getTableId());
$columnM->whereE('name','core');
$coreArray[$localObj->getModelID()]=$columnM->exist();
}
if(!$localObj->_noCoreCheck && !empty($coreArray[$localObj->getModelID()])){
if(isset($eid)){static $loadedCoreA=array();
$my3PK=$localObj->getPK();
$key=$my3PK.'-'.$eid;
if(!isset($loadedCoreA[$key])){
$localObj->whereE($my3PK, $eid );
$loadedCoreA[$key]=$localObj->load('lr','core');
}$core=$loadedCoreA[$key];
}else{ $core=3;
}
if($core >=1){
$message=WMessage::get();
$message->userN('1298350423SVOJ');
$message->userN('1206961856OFMQ');
$localObj->skipMessage();
return true;
}
}
$mySID=$localObj->getModelID();
if(isset($safeguard[$mySID.$eid] )) return true;
else  $safeguard[$mySID.$eid]=true;
if($localObj->_keepAttributesOnDelete && method_exists($localObj, 'deleteExtra')){
static $loadedObjectA=array();
$pKey=$localObj->getPK();
$key=$pKey.'-'.$eid;
if(!isset($loadedCoreA[$key])){
$localObj->whereE($pKey, $eid );
$loadedObjectA[$key]=$localObj->load('o');
}$data=!empty($loadedObjectA[$key])?$loadedObjectA[$key] : null;
$localObj->addProperties($data, '_');
}
$status=( method_exists($localObj, 'deleteValidate'))?$localObj->deleteValidate($eid ) : true;
$localObj->_doNotDeleteValidate=true;
if(empty($status)){
return false;
}
$tableID=$localObj->getTableId();
static $fksA=array();
if(!isset($fksA[$tableID] )){
$databaseForeignM=WModel::get('library.foreign');
$databaseForeignM->remember('foreignKey_OnDelete_'.$tableID, true, 'Model');
$databaseForeignM->makeLJ('library.columns','feid','dbcid');
$databaseForeignM->makeLJ('library.model','dbtid');
$databaseForeignM->whereE('ref_dbtid',$tableID );
$databaseForeignM->whereE('ondelete', 3 );$databaseForeignM->whereE('publish' ,1 );
$databaseForeignM->whereE('publish' ,1 ,2 );
$databaseForeignM->groupBy('dbtid');
$databaseForeignM->select('name', 1, 'map');
$databaseForeignM->select('sid', 2 );
$databaseForeignM->select('dbtid');
$databaseForeignM->setLimit( 100 );
$fksA[$tableID]=$databaseForeignM->load('ol');
}foreach($fksA[$tableID] as $fk){
if(!empty($fk->sid )){
$sql=WModel::get($fk->sid, 'object');
$sql->_noCoreCheck=$localObj->_noCoreCheck;
if($sql->isReady()){
$sql->whereE($fk->map, $eid );
$allpKeys=explode(',',$sql->getPK());
if(!$sql->multiplePK()){
$sql->select($sql->getPK());
$sql->setLimit( 1000 );
$childKeys=$sql->load('lra');
if(!empty($childKeys)){
if(!$sql->deleteAll($childKeys )){
$message=WMessage::get();
$message->codeE('Error deleting the items for the model: '. $sql->getModelNamekey()) ;
return false;
}}}else{
$sql->select($fk->map );
$sql->setLimit( 1000 );
$childKeys=$sql->load('lra');
$sql->whereE($fk->map, $eid );
if(!empty($childKeys)){
if(!$sql->deleteAll()){
$message=WMessage::get();
$message->codeE('Error deleting the items for the model: '.$sql->getModelNamekey());
return false;
}}
}
}}}
return $localObj->delete($eid, true);
}
return false;
}
private function &_coreArray(){
static $coreArray=array();
return $coreArray;
}
}