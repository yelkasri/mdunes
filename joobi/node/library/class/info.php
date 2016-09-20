<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Library_info_class {
public function getNamekeyFromNode($extID){
$type=WExtension::get($extID, 'type');
if($type==1 ) return WExtension::get($extID, 'namekey');
$installAppsdependencyM=WModel::get('install.appsdependency');
$installAppsdependencyM->rememberQuery(true);
$installAppsdependencyM->makeLJ('install.apps','wid','wid');
$installAppsdependencyM->whereE('ref_wid',$extID );
$installAppsdependencyM->whereIn('type',array( 1, 150 ), 1 );
$installAppsdependencyM->orderBy('type','ASC', 1 );
$installAppsdependencyM->orderBy('wid','ASC', 1 );
$installAppsdependencyM->select('type', 1 );
$allAppSA=$installAppsdependencyM->load('ol','wid');
if(empty($allAppSA)) return JOOBI_MAIN_APP.'.application';
$AppA=array();
$nodeA=array();
foreach($allAppSA as $oneApp){
if($oneApp->type==1)$AppA[]=$oneApp->wid;
else $nodeA[]=$oneApp->wid;
}
if(!empty($AppA)){
$s101=null;
foreach($AppA as $oneAppli){
$extensionO=WExtension::get($oneAppli, 'data');
if(empty($extensionO)) continue;
WExtension::checkAuthorizedLevel($extensionO );
$s=WGlobals::getSugar();
if($s==201){
return $extensionO->namekey;
}elseif($s==101){
if(!isset($s101))$s101=$extensionO->namekey;
}}
if(!empty($s101)){
$extensionO=WExtension::get($s101, 'data');
WExtension::checkAuthorizedLevel($extensionO );
return $s101;
}else{
return JOOBI_MAIN_APP.'.application';
}
}else{
$count=0;
do {
$count++;
$AppA=$this->_getDependancyExtnsionA($nodeA );
} while(empty($AppA) && !empty($nodeA) && $count < 10 );
if(!empty($AppA)){
$s101=null;
foreach($AppA as $oneAppli){
$extensionO=WExtension::get($oneAppli, 'data');
WExtension::checkAuthorizedLevel($extensionO );
$s=WGlobals::getSugar();
if($s==201){
return $extensionO->namekey;
}elseif($s==101){
if(!isset($s101))$s101=$extensionO->namekey;
}}
if(!empty($s101)){
$extensionO=WExtension::get($s101, 'data');
WExtension::checkAuthorizedLevel($extensionO );
return $s101;
}else{
return JOOBI_MAIN_APP.'.application';
}
}else{
return JOOBI_MAIN_APP.'.application';
}
}
}
private function _getDependancyExtnsionA(&$nodeA){
$AppA=array();
$installAppsdependencyM=WModel::get('install.appsdependency');
$installAppsdependencyM->rememberQuery(true);
$installAppsdependencyM->makeLJ('install.apps','wid','wid');
$installAppsdependencyM->whereIn('ref_wid',$nodeA );
$installAppsdependencyM->whereIn('type',array( 1, 150 ), 1 );
$installAppsdependencyM->orderBy('type','ASC', 1 );
$installAppsdependencyM->orderBy('wid','ASC', 1 );
$installAppsdependencyM->select('type', 1 );
$allAppSA=$installAppsdependencyM->load('ol','wid');
if(empty($allAppSA)) return $AppA;
$nodeA=array();
foreach($allAppSA as $oneApp){
if($oneApp->type==1)$AppA[]=$oneApp->wid;
else $nodeA[]=$oneApp->wid;
}
return $AppA;
}
}