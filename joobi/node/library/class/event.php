<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Library_Event_class extends WClasses {
public function checkForEvents($path,$uid=0,$params=null){
if(!WExtension::exist('campaign.node')) return false;
$getModel=true;
$caching=WPref::load('PLIBRARY_NODE_CACHING');
$caching=($caching > 5 )?'cache' : 'static';
$alleventsA=WCache::getObject($path, 'Events',$caching, false, $path );
if(!empty($alleventsA)){
foreach($alleventsA as $event){
$this->_checkEventToCampaign($event, $uid, $params );
}
}
return true;
}
private function _checkEventToCampaign($event,$uid=0,$params=null){
if(empty($event->start_stop)) return false;
$startStop=($event->start_stop==1?true : false);
unset($event->start_stop );
$trigger=false;
$eventC=WClass::get($event->namekey, null, 'event', false);
if(empty($eventC)){
$trigger=true;
}else{
$eventC->addProperties($event );
WTools::getParams($eventC );
$trigger=$eventC->create($params );
}
if($trigger){
$campaignSubscribersC=WClass::get('campaign.subscribers');
$campaignSubscribersC->oneSubscription($uid, $event->cmpgnid, $startStop );
}
return false;
}
 }
class WEvents {
public static function getSQL($ctrid){
$controllerActionM=WModel::get('campaign.event','object');$controllerActionM->makeLJ('campaign.eventtype','evtypeid');
$controllerActionM->makeLJ('campaign','cmpgnid','cmpgnid', 0, 2 );
$controllerActionM->select('namekey', 1 );
$controllerActionM->select('params', 0 );
$controllerActionM->select('start_stop', 0 );
$controllerActionM->select('cmpgnid', 2 );
$controllerActionM->whereE('namekey',$ctrid, 1 );
$controllerActionM->whereE('publish', 1 );
$controllerActionM->whereE('publish', 1, 1 );
$controllerActionM->whereE('publish', 1, 2 );
$controllerActionM->setLimit( 2000 );
$resultA=$controllerActionM->load('ol');
if(empty($resultA ))$resultA=false;
return $resultA;
}
}