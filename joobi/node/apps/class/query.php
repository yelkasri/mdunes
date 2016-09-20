<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class Apps_Query_class extends WClasses {
public function checkQuery($file,$wid){
return 50;
}
public function getSweetInfo($file,$wid,$param=''){
$sx1Value=$this->_getFileInformation($file, $wid );
if(empty($param)){
return $sx1Value;
}elseif(is_array($param) && !empty($param)){
$obj=new stdClass;
foreach($param as $oneP){
if(isset($sx1Value->$oneP))$obj->$oneP=$sx1Value->$oneP;
}
return $obj;
}else{
if(!is_string($param)) return null;
if(isset($sx1Value->$param)) return $sx1Value->$param;
}
return null;
}
private function _getFileInformation($file,$wid=0){
static $sx1Value=array();
$className='';
if(!empty($wid)){
$folder=WExtension::get($wid, 'folder');
$className=$folder.'_icecream_Special';
if( class_exists($className)){
return (isset($sx1Value[$file])?$sx1Value[$file] : false);
}
}
if(!isset($sx1Value[$file]) && file_exists($file)){
include_once($file );
if( class_exists($className)){
$myClass=new $className;
$setKey='ds878%^hdafnga7HJJkT$%567fIOh UIg____----dafuobqhb3$@$Fadfadsfdjfhj';
$dynamicKey='lavoi'.time(). 'Hds';
$base64=base64_decode( substr($myClass->d, 5 ));
if(false===$base64){
$base64=base64_decode($myClass->d );
}
$sx1Value[$file]=unserialize($base64 );
if(!isset($sx1Value[$file]->subtype) && !empty($sx1Value[$file]->epytype) && $sx1Value[$file]->epytype !=17)$sx1Value[$file]->subtype=35;
}else{
$sx1Value[$file]='';
}
}
return (isset($sx1Value[$file])?$sx1Value[$file] : false);
}
public function getSweetSpot($file,$wid){
static $sx1s=array();
$option=WExtension::get($wid, 'data');
if(isset($sx1s[$file.$option->namekey])){
return $sx1s[$file.$option->namekey];
}
$sx1Value=$this->_getFileInformation($file, $wid );
if(!empty($sx1Value)){
$sx1fredURL=(!empty($sx1Value->domainURL))?$sx1Value->domainURL : $sx1Value->fredURL;
$sx1Epytype=(!empty($sx1Value->typeOfFun))?$sx1Value->typeOfFun : $sx1Value->epytype;
$sx1Sunset=(!empty($sx1Value->anotherDaypasted))?$sx1Value->anotherDaypasted : $sx1Value->sunset;
$sx1Sunrise=(!empty($sx1Value->mostBeautiful))?$sx1Value->mostBeautiful : $sx1Value->sunrise;
$sx1GostmeIn=(!empty($sx1Value->IgotU))?$sx1Value->IgotU : $sx1Value->gostmeIn;
$sx1SnowMan=(!empty($sx1Value->summerMan))?$sx1Value->summerMan : $sx1Value->snowMan;
$sx1VillaLevel=(!empty($sx1Value->castLevel))?$sx1Value->castLevel : $sx1Value->villaLevel;
$sx1Token=(!empty($sx1Value->jeton))?$sx1Value->jeton : $sx1Value->token;
$sx1Domain=(!empty($sx1Value->tubig))?$sx1Value->tubig : 0;
$iDomino=(!empty($sx1Value->iDomino))?$sx1Value->iDomino : '';
$iMacPRO=(!empty($sx1Value->iMacPRO))?$sx1Value->iMacPRO : '';
if(!$sx1Value){
$message=WMessage::get();
$message->notify( 8 );
$message->adminW('There is a problem with the api key!');
$sx1s[$file.$option->namekey]=0;
return $sx1s[$file.$option->namekey];
}
$validateMyLic=true;
if($option->namekey !=$sx1SnowMan){
$message=WMessage::get();
$message->notify( 7 );
$message->adminW('There is a problem with the license!');
$sx1s[$file.$option->namekey]=0;
return $sx1s[$file.$option->namekey];
}
switch($sx1Epytype){
case '125': 
case '126': 
case '17': 
case '120': 
case '200': 
case '8': 
case '9': 
if(!defined('PAPPS_NODE_NEXTCHECK')) WPref::get('apps.node');
if( date("Y-m-d", strtotime('+10 day')) > $sx1Sunset && PAPPS_NODE_NEXTCHECK < time()){
if( WRoles::isNotAdmin()){
$pref=WPref::get('apps.node');
$pref->updatePref('nextcheck', time() + 86000 );
}
$extensionInfoC=WClass::get('apps.info');
$status=$extensionInfoC->requestCandy($sx1Epytype, $sx1SnowMan , $sx1VillaLevel, $sx1Token, false);
if(!$status){
}
}
$today=date("Y-m-d" );
break;
case '101': 
case '102': 
break;
default:
break;
}
$databaseLicense=WExtension::get($option->namekey, 'license', 1 );
$sx1s[$file.$option->namekey]=$sx1VillaLevel;
return $sx1s[$file.$option->namekey];
}
$sx1s[$file.$option->namekey]=0;
return $sx1s[$file.$option->namekey]; 
}
}
