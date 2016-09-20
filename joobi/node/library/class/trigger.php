<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class Library_Trigger_class extends WClasses {
public function actions($trigger,&$params,$status=null,$before=false){
static $alreadyTriggered=array();
$controllerID=$trigger->ctrid;
$entirekey=$controllerID.'|'.md5( serialize($params)). '|'.$status;
if(isset($alreadyTriggered[$entirekey])) return true;
if(!$before)$alreadyTriggered[$entirekey]=true;
$getModel=true;
$caching=WPref::load('PLIBRARY_NODE_CACHING');
$caching=($caching > 8 )?'cache' : 'static';
$allActions=WCache::getObject($trigger->ctrid, 'Action',$caching );
if(!empty($allActions)){
foreach($allActions as $action){
if($before && !$action->before ) continue;
if(!$before && $action->before ) continue;
if(empty($action->publish)) continue;
if(!$action->core){
if(empty($params->custom))$params->custom=true;
}else{
$params->custom=false;
}
$newAction=WAction::get($action->namekey, $params, $status );
}
}
return true;
}
public function getSQL($ctrid,$showMessage=true){
static $memory=array();
if(!isset($memory[$ctrid] )){
$actionA=Library_Trigger_class::loadTriggerSQL($ctrid );
if(!isset($actionA)){
$memory[$ctrid]='';
return null;
}
if(!empty($actionA )){
$checkActionInsertedA=array();
foreach($actionA as $oneAction){
if(empty($oneAction->actid))$checkActionInsertedA[]=$oneAction->action;
}
if(!empty($checkActionInsertedA)){
$actionM=WModel::get('library.action','object');
$actionM->whereIn('namekey',$checkActionInsertedA );
$allActionsA=$actionM->load('ol',array('actid','namekey'));
$sortedActionsA=array();
foreach($allActionsA as $oneNow){
$sortedActionsA[$oneNow->namekey]=$oneNow->actid;
}
$controllerActionM=WModel::get('library.controlleraction','object');
foreach($actionA as $oneAction){
if(empty($oneAction->actid)){
if(empty($sortedActionsA[$oneAction->action])) continue;
$controllerActionM->whereE('ctr_action_id',$oneAction->ctr_action_id );
$controllerActionM->setVal('actid',$sortedActionsA[$oneAction->action] );
$controllerActionM->update();
}
}
$actionA=Library_Trigger_class::loadTriggerSQL($ctrid, true);
}
$memory[$ctrid]=$actionA;
}else{
$memory[$ctrid]='';
}
}
return $memory[$ctrid];
}
private static function loadTriggerSQL($ctrid,$onlyVAlidActions=false){
$controllerActionM=WModel::get('library.controlleraction','object');
$controllerActionM->makeLJ('library.action','actid');
$controllerActionM->whereE('ctrid',$ctrid );
$controllerActionM->whereE('publish', 1 );
if($onlyVAlidActions)$controllerActionM->where('actid','!=', 0 );
$controllerActionM->orderBy('ordering');
$controllerActionM->select( array('action','ctr_action_id'), 0 );
$controllerActionM->select( array('namekey','folder','status','filter','before','params','core','publish'), 1 );
$controllerActionM->setLimit( 500 );
$resultA=$controllerActionM->load('ol');
return $resultA;
}
}
