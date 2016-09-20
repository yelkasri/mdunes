<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Library_Cache_class {
private $time=0;
private static $_cacheInstanceA=array();
public function setTime($time){
$this->time=$time;
}
public function get($id,$index='Others',$type='cache',$checkTime=false){
$registry=$this->_getRegistry($type );
$result=$registry->getCache($index, $id, $checkTime );
return $result;
}
public function set($id,$value,$index='Others',$type='cache'){
$registry=$this->_getRegistry($type );
return $registry->setCache($index, $id, $value );
}
public function getdata($id,$className,$cachingType='cache',$mandatoryStatic=true,$stringID=false,$functionName='',$params=null,$showMessage=true){
static $localData=array();
$registry=array();
$registry[$cachingType]=$this->_getRegistry($cachingType );
$loadData=false;
if( is_numeric($id) || $stringID){
if($mandatoryStatic){
if(!isset($localData[$className]['d'][$id])){
$localDataLoaded=$registry[$cachingType]->getCache($className, 'd-'.$id, false);
if(empty($localDataLoaded))$loadData=true;else {$localData[$className]['d'][$id]=$localDataLoaded;
if(!empty($localDataLoaded->namekey))$localData[$className]['k'][$localDataLoaded->namekey]=$id;
$returnId=$id;
}}else $returnId=$id;
}else{
$dataFromCache=$registry[$cachingType]->getCache($className, 'd-'.$id, false);
if(empty($dataFromCache))$loadData=true;
else $returnId=$id;
}
}else{
if($mandatoryStatic){
if(!isset($localData[$className]['k'][$id])){
$localIDLoaded=$registry[$cachingType]->getCache($className, 'k-'.$id, false);
if(empty($localIDLoaded))$loadData=true;
else {$localData[$className]['k'][$id]=$localIDLoaded;
$localDataLoaded=$registry[$cachingType]->getCache($className, 'd-'.$localIDLoaded, false);
if($localDataLoaded ===null){
$returnId=false;
$loadData=true;}else{
$localData[$className]['d'][$localIDLoaded]=$localDataLoaded;
$returnId=$localIDLoaded;
}}} else $returnId=$localData[$className]['k'][$id];
}else{
$returnId=$registry[$cachingType]->getCache($className, 'k-'.$id, false);
if(empty($returnId))$loadData=true;
}
}
if($loadData){
$WclassName='W'.$className;
$functionName='getSQL'.$functionName;
$newInstance=new $WclassName;
if(!empty($params))$data=$newInstance->$functionName($id, $showMessage, $params );
else $data=$newInstance->$functionName($id, $showMessage );
if(!empty($data)){if(!empty($data->namekey )){
$namekeyToUse=( is_numeric($id))?$data->namekey: $id;
$registry[$cachingType]->setCache($className, 'k-'.$namekeyToUse, $data->id );
if($mandatoryStatic)$localData[$className]['k'][$namekeyToUse]=$data->id;
}
if( is_numeric($id) || $stringID)$returnId=$id;
else {
if($mandatoryStatic)$returnId=(!empty($data->namekey))?$localData[$className]['k'][$id] : false;
else $returnId=(!empty($data->namekey))?$registry[$cachingType]->getCache($className, 'k-'.$id, false) : false;
}
if($mandatoryStatic)$localData[$className]['d'][$returnId]=$data;
$registry[$cachingType]->setCache($className, 'd-'.$returnId, $data );
}else{
$data='xzwNO__NEED__STATICwxz';
$letter=( is_numeric($id) || $stringID?'d' : 'k');
if($mandatoryStatic)$localData[$className][$letter][$id]=$data;
$registry[$cachingType]->setCache($className, $letter.'-'.$id, $data );
return $data;
}
}
if(empty($returnId)) return false;
if($mandatoryStatic && isset($localData[$className]) && isset($localData[$className]['d']) && isset($localData[$className]['d'][$returnId])) return $localData[$className]['d'][$returnId];
else return (!empty($dataFromCache)?$dataFromCache : $registry[$cachingType]->getCache($className, 'd-'.$returnId, false));
}
public function resetCache($domain=null,$id=null,$type='cache'){
if(empty($id)){
if(empty($domain)){
$allLibCacheA=array('Extension','Model','Views','Picklist','Filter',
 'Theme','Table','Events','Model_mailing_node','Controller','Action','Language',
'Tag','Preference','Translation','Menus','Widgets');
$libraryTableM=WModel::get('library.table');
$allModelTbaleA=$libraryTableM->load('lra','name');
if(!empty($allModelTbaleA)){
foreach($allModelTbaleA as $oneTable)$allLibCacheA[]='Model_'.$oneTable;
}
$registry=$this->_getRegistry($type );
foreach($allLibCacheA as $oneCache){
$registry->clean($oneCache );
}return true;
}else{
if($domain=='Preference') unset($_SESSION['jPreference']['uid'] );
unset($_SESSION['extInfo'] );
$registry=$this->_getRegistry($type );
return $registry->clean($domain );
}
}else{
$registry=$this->_getRegistry($type );
if($domain=='Preference') unset($_SESSION['jPreference']['uid'] );
unset($_SESSION['extInfo'] );
return $registry->remove($id, $domain );
}
}
private function _getRegistry($cachingType='cache'){
switch($cachingType){
case 'static':
self::$_cacheInstanceA[$cachingType]=new WCaching_Static('static');
break;
case 'cache':
WLoadFile('library.class.cachemain');
self::$_cacheInstanceA[$cachingType]=Library_Cachefile_class::getInstance($this->time );
if(empty(self::$_cacheInstanceA[$cachingType]->cache)){
self::$_cacheInstanceA[$cachingType]=new WCaching_Static('static');
}break;
case 'session':
self::$_cacheInstanceA[$cachingType]=new WCaching_Static('session');
break;
case 'none':
default:
self::$_cacheInstanceA[$cachingType]=new WCaching_Static('none');
break;
}
return self::$_cacheInstanceA[$cachingType];
}
}
class WCaching_Static {
private $_useSession=false;
private static $_memoryA=array();
function __construct($type){if($type=='none')$this->_registery('','','reset');
elseif($type=='session')$this->_useSession=true;
}
public function getCache($index,$key){
if(empty($key) || empty($index)) return null;
return $this->_registery($index, $key );
}
public function setCache($index,$key,$value){
if(empty($key) || empty($index)) return null;
return $this->_registery($index, $key, 'set',$value );
}
public function clean($index=null,$id=null){
if(!empty($id))$this->_registery($index, $id, 'delete');
else $this->_registery($index, '','reset');
}
private function _registery($index,$key,$action='get',$value=null){
if(!$this->_useSession){
if($action=='reset')  {
if(empty($index)) self::$_memoryA=array();
else unset( self::$_memoryA[$index] );
}
elseif($action=='get') return isset(self::$_memoryA[$index][$key])?self::$_memoryA[$index][$key] : null;
elseif($action=='set') self::$_memoryA[$index][$key]=$value;
elseif($action=='delete') unset(self::$_memoryA[$index][$key]);
}else{
if($action=='reset')  {
if(empty($index))  $_SESSION['WCache91']=array();
else unset($_SESSION['WCache91'][$index] );
}
elseif($action=='get') return isset($_SESSION['WCache91'][$index][$key])?$_SESSION['WCache91'][$index][$key] : null;
elseif($action=='set')$_SESSION['WCache91'][$index][$key]=$value;
elseif($action=='delete') unset($_SESSION['WCache91'][$index][$key] );
}
}
}