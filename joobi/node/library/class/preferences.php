<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class myPreferences extends WObj {
var $_ignore=false;var $_usersPref=false;
private $_compNamekey=null;
private static $loaded=array();
public function isLoaded($variable){
$constant=strtoupper('P'.$this->_compNamekey.'_'.$variable );
if( defined($constant)){
return $constant;
}
return false;
}
public function getPref($variable,$default=''){
$name=$this->isLoaded($variable );
if($name){
return Wpref::load($name );
}
return $default;
}
function setup($prefix=''){
if(empty($prefix)) return false;
if( is_numeric($prefix)){
return false;
}else{
$this->_compNamekey=$prefix;
}
return true;
}
 public function loadConfig($userPref=true,$force=false,&$newWay){
 if(empty($this->_compNamekey)) return false; 
 if(!$force && ! empty( self::$loaded[$this->_compNamekey] )) return false;
 self::$loaded[$this->_compNamekey]=true;
 $JoobiUser=WGlobals::getSession('JoobiUser');
if(!empty($JoobiUser ) && $userPref){
$uid=(!empty($JoobiUser->uid)?$JoobiUser->uid : 0 );
} else $uid=0;
$noYetPref=false;
$folder=$this->_getPreFolder();
if( class_exists('Default_'.$folder.'_preferences')){
return false;
}
$localtion=JOOBI_DS_NODE . $folder.DS.'preferences.php';
if( @file_exists($localtion)){
include($localtion);
$newWay=str_replace('.','_',$this->_compNamekey );
}else{
$newWay=false;
$defaultClas='class Default_'.$folder.'_preferences {} class Role_'.$folder.'_preferences {}';
eval($defaultClas);
}
$localtion=JOOBI_DS_USER.'node'.DS.$folder.DS.'preferences.php';
if( @file_exists($localtion)){
include($localtion);
}else{
$siteClass='class Site_'.$folder.'_preferences extends Default_'.$folder.'_preferences{}';
eval($siteClass);
}
  $needUserClass=false;
if($uid > 0){
$username='u'.$uid;
$localtion=JOOBI_DS_USERS . $username.DS.'node'.DS.$folder.DS.'preferences.php';
if( @file_exists($localtion)){
include($localtion);
}else{
$needUserClass=true;
}}else{
$needUserClass=true;
}
if($needUserClass){
$siteCl='Site_'.$folder.'_preferences';
if(!class_exists($siteCl)){
$userClass='class User_'.$folder.'_preferences extends Default_'.$folder.'_preferences{}';
}else{
$userClass='class User_'.$folder.'_preferences extends Site_'.$folder.'_preferences{}';
}
eval($userClass);
}
$className='User_'.$folder.'_preferences';
if( class_exists($className)){
$prefInst=new $className;
return $prefInst;
}
 }
 public function updatePref($key,$val,$users=false,$keyName='text'){
 if(empty($this->_compNamekey)) return false;
$preferenceName='P'.strtoupper( str_replace('.','_',$this->_compNamekey ). '_'.$key );
WPref::load($preferenceName );
WPref::$prefA[$preferenceName]=$val;
$folder=$this->_getPreFolder();
$defaultClass='Default_'.$folder.'_preferences';
$classExist=false;
$localtion=JOOBI_DS_USER.'node'.DS.$folder.DS.'preferences.php';
$access='Role_'.$folder.'_preferences';
if(!class_exists($access)){
return false;
}$accessInst=new $access;
if(isset($accessInst->$key)){
if(!WRole::hasRole($accessInst->$key)){
return false;
}}
if('premium'==$keyName){
return false;
}
$userClass='Site_'.$folder.'_preferences extends Default_'.$folder.'_preferences';
$updateClass='SiteUpdate_'.$folder.'_preferences';
if(!class_exists($updateClass)){
if( @file_exists($localtion)){
$content=file_get_contents($localtion );
$content=str_replace($userClass, $updateClass, $content );
$content=str_replace( array( "defined('JOOBI_SECURE') or die('J....');", "<?php" ), '',$content );
eval($content );
}
}
$siteInst=&self::_getUpdateInstance($updateClass );
$siteInst->$key=$val;
$site='';
$line="\n";
foreach($siteInst as $k=> $v){
if( is_numeric($v)){if(empty($v))$v=0;
$site .='public $'.$k.'='.$v.';';
}else{
$site .='public $'.$k . "='" . addslashes($v). "';";
}$site .=$line;
}
$file="<?php defined('JOOBI_SECURE') or die('J....');" . $line;
$file .='class Site_'.$folder.'_preferences extends Default_'.$folder.'_preferences {'.$line;
$file .=$site;
$file .='}';
$fileS=WGet::file();
$fileS->write($localtion, $file, 'overwrite');
return true;
}
 public function updateDefaultPref($key,$val,$users=false){
return $this->updatePref($key, $val, $users, 'premium');
}
public function updateUserPref($key,$val,$uid=null){
if(empty($this->_compNamekey)) return false;
if(!isset($uid))$uid=WUser::get('uid');
if(empty($uid)) return false;
$preferenceName='P'.strtoupper( str_replace('.','_',$this->_compNamekey ). '_'.$key );
WPref::load($preferenceName );
WPref::$prefA[$preferenceName]=$val;
$folder=$this->_getPreFolder();
$access='Role_'.$folder.'_preferences';
$accessInst=new $access;
if(isset($accessInst->$key)){
if(!WRole::hasRole($accessInst->$key, $uid )){
$this->adminE('You do not have enough permission to update property : '.$property.'  value : '.$val );
return false;
}}
$username='u'.$uid;
$localtion=JOOBI_DS_USERS . $username.DS.'node'.DS.$folder.DS.'preferences.php';
$fileS=WGet::file();
$userClass='User_'.$folder.'_preferences extends Site_'.$folder.'_preferences';
$updateClass='UserUpdate_'.$folder.'_preferences';
if(!class_exists($updateClass)){
if( @file_exists($localtion)){
$content=file_get_contents($localtion );
$content=str_replace($userClass, $updateClass, $content );
$content=str_replace( array( "defined('JOOBI_SECURE') or die('J....');", "<?php" ), '',$content );
eval($content );
}}
$siteInst=&self::_getUpdateInstance($updateClass );
$siteInst->$key=$val;
$site='';
$line="\n";
foreach($siteInst as $k=> $v){
if( is_numeric($v)){if(empty($v))$v=0;
$site .='public $'.$k.'='.$v.';';
}else{
$site .='public $'.$k . "='" . addslashes($v). "';";
}$site .=$line;
}
$file='<?php defined(\'JOOBI_SECURE\') or die(\'J....\');'.$line;
$file .='class User_'.$folder.'_preferences extends Site_'.$folder.'_preferences {'.$line;
$file .=$site;
$file .='}';
$status=$fileS->write($localtion, $file, 'overwrite');
return $status;
}
private function _getPreFolder(){
$flderA=explode('.',$this->_compNamekey );
$folder=array_shift($flderA );
return $folder;
}
private static function &_getUpdateInstance($class){
static $instA=array();
if(!isset($instA[$class])){
if(!class_exists($class)){
$classDef='class '.$class.' {}';
eval($classDef);
}$instA[$class]=new $class;
}
return $instA[$class];
}
}
 class WTranslation {
public static function load($wid='1',$load=1){
static $loadedLang=array();
$usedLanguage=WGlobals::getSession('JoobiUser','lgid');
$key=$wid. '-'.$load.'-'.$usedLanguage;
if(!isset($loadedLang[$key])){
$loadedLang[$key]=true;
$caching=( defined('PLIBRARY_NODE_CACHING')?PLIBRARY_NODE_CACHING : 1 );
$caching=($caching > 4 )?'cache' : 'static';
$vocabulary=WCache::getObject($wid. '-'.$load.'-'.$usedLanguage, 'Translation',$caching, false, true);
if(!empty($vocabulary) && $vocabulary !==true){
foreach($vocabulary as $key=> $val){
if(empty($val->imac)) continue;
if(empty($val->text))$val->text=(isset($val->textref)?$val->textref : '');
$constName=$val->imac;
WText::$vocab[$constName]=$val->text;
}}
}
}
public function getSQL($passedParam,$showMessage=true){
static $code=array();
$explodeMeA=explode('-',$passedParam );
$wid=$explodeMeA[0];
$load=$explodeMeA[1];
$lgid=$explodeMeA[2];
$modelTR=WTable::get('translation_reference','main_translation');$modelTR->whereE('load',$load );
if($load!=2)$modelTR->whereE('wid',$wid );
$modelTR->makeLJ('translation_en','main_translation','imac','imac');
$modelTR->select('imac', 1 );
if(!isset($code[$lgid])){
$helloLang=WLanguage::get($lgid, 'code');
$code[$lgid]=strtolower( trim($helloLang));
}
$modelExit=false;
if(!empty($code[$lgid]) && $code[$lgid] !='en'){
$tableExistM=WTable::get('translation_'.$code[$lgid], 'main_translation');
$modelExit=$tableExistM->tableExist();
}
if($modelExit){
$code[$lgid]=substr($code[$lgid], 0, 2 );
$modelTR->makeLJ('translation_'.$code[$lgid], 'main_translation','imac','imac');
$modelTR->select('text', 2);
$modelTR->select('text', 1,'textref');
}else{
$modelTR->select('text', 1 );
}
$modelTR->setLimit( 2000 );
$vocabulary=$modelTR->load('ol');
if(empty($vocabulary))$vocabulary=true;
return $vocabulary;
}
}