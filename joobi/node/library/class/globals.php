<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
abstract class WGlobals {
private static $levelAccess=null;
private static $levelSugar=null;
private static $_countA=array();
public static function get($name='',$default=null,$src='',$type='',$filter=true){
if('joobi'==$src)$src='global';
if(is_array($name)){
  foreach($name as $key=> $valname){
if(is_array($default)){
  $name[$key]=WGlobals::get($valname, $default[$key], $src, $type, $filter );
}else{
  $name[$key]=WGlobals::get($valname, $default, $src, $type, $filter );
}  }
  return $name;
}elseif(empty($name)){
return WGlobals::getEntireSuperGlobal($src );
}
$name=trim($name);
$data=null;
$array=$src;
WGlobals::_getArray($data, $array );
if( WGlobals::_static( 2 )
&& WGlobals::_static( 4, $array )
&& WGlobals::_static( 3, $array )
&& WGlobals::_static( 5, $name, $array )){
  switch ($array){
case '_JOOBI':
case '_GLOBAL':
return WGlobals::_static( 8, $array, $name );
break;
case '_COOKIE':
return WGlobals::getCookie($name );
break;
default:
if($filter)$val=(isset($data[$name])?$data[$name] : null );
if($filter){
$val=WGlobals::filter($val, $type );
}
return $val;
  }
}
if(!$filter){
  return WGlobals::_quotes($data[$name], $array );
}
$new=null;
if(is_array($data) && @array_key_exists($name, $data ) && $data[$name] !==null){
$new=WGlobals::filter( WGlobals::_quotes($data[$name], $array ), $type, $default, $src, $name );
WGlobals::set($name, $new, $array );
}else{
if($src=='cookie'){
return WGlobals::getCookie($name );
}else{
  $new=$default;
}}
return $new;
}
public static function set($name,$value=null,$location='',$overwrite=true){
if('joobi'==$location)$location='global';
$name=trim($name);
$data=null;
WGlobals::_getArray($data, $location );
if(!WGlobals::_static(2)) WGlobals::_static(0);
if(!WGlobals::_static( 3, $location)) WGlobals::_static( 1, $location );
if(is_array($data) && @array_key_exists($name, $data ) && ! $overwrite ) return false;
WGlobals::_set($location, $name, $value, true, $overwrite );
return true;
  }
public static function setEID($value){
if(!is_numeric($value)){
$message=WMessage::get();
$message->codeE('The value of the EID in the function setEID() must be a numeric');
return false;
}WGlobals::_storeEID(false, $value );
}
public static function getEID($double=false){
return WGlobals::_storeEID($double );
}
public static function getApp($useDefault=true){
if( JOOBI_URLAPP_PAGE=='') return '';
return WGlobals::get( JOOBI_URLAPP_PAGE, '', null, 'namekey');
}
public static function setSession($domain,$propertyName,$value,$overwrite=false){
if(!is_string($domain) || empty($domain)){
if($overwrite)$_SESSION=array();
return false;
}
if(!empty($propertyName)){
if(!$overwrite && !empty($_SESSION[$domain]->$propertyName )){
}
if(empty($_SESSION[$domain]))$_SESSION[$domain]=new stdClass;
$_SESSION[$domain]->$propertyName=$value;
} else $_SESSION[$domain]=$value;
return true;
  }
public static function getSession($domain,$propertyName='',$default=null){
if(empty($domain) || ! is_string($domain)) return false;
if(!empty($propertyName)){
if(isset($_SESSION[$domain]->$propertyName )){
 return $_SESSION[$domain]->$propertyName;
} else return $default;
}else{
if(isset($_SESSION[$domain])){
return $_SESSION[$domain];
}else{
return $default;
}}
  }
public static function setCookie($property,$value,$expire=0,$path='/',$domain=null,$secure=null){
if('wordpress'==JOOBI_FRAMEWORK_TYPE && IS_ADMIN ) return false;
if(empty($property)) return false;
if(empty($expire))$expire=time() + 17280000;
if(!isset($secure )){
$secure=true;
$HTTPS=WGlobals::get('HTTPS','off','server');
if(empty($HTTPS) || $HTTPS=='off'){
if( IS_ADMIN && ! WPref::load('PLIBRARY_NODE_SSLBE')){
$secure=false;
}elseif(!IS_ADMIN && ! WPref::load('PLIBRARY_NODE_SSLFE')){
$secure=false;
}}}
@setcookie($property, $value, $expire, $path, $domain, $secure );
return true;
  }
public static function getCookie($name,$default=null){
  if(empty($name)) return false;
  if(isset($_COOKIE[ $name ])){
  if(empty($_COOKIE[ $name ])) return '';
    return WGlobals::filter($_COOKIE[ $name ] );   }else{
  return $default;
  }
return null;
  }
public static function getCookieUser(){
$ID=WGlobals::getCookie('wzhyoID');
if(empty($ID)){
$ID=time();
$msA=explode(' ', microtime());
$msA1=explode('.',$msA[0] );
$ID .=substr($msA1[1], 0, 4 );
$ID .=rand( 100, 999 );
$expire=time() + 31536000; WGlobals::setCookie('wzhyoID',$ID, $expire );
}
return WGlobals::filter($ID, 'int');
  }
public static function currentURL($route=true){
$referer='http';
$HTTPS=WGlobals::get('HTTPS','off','server');
if(!empty($HTTPS) && $HTTPS=='on'){$referer .="s";}
$referer .="://";
$SERVER_PORT=WGlobals::get('SERVER_PORT','','server');
if($SERVER_PORT !="80"){
 $referer .=WGlobals::get('SERVER_NAME','','server','string').":" . WGlobals::get('SERVER_PORT','','server','string'). WGlobals::get('REQUEST_URI','','server','string');
}else{
 $referer .=WGlobals::get('SERVER_NAME','','server','string'). WGlobals::get('REQUEST_URI','','server','string');
}
$url=str_replace('&amp;','&',$referer );
if($HTTPS)$url=str_replace(':443','',$url );
$final=($route )?WPage::routeURL($url) : $url;
return $final;
  }
public static function getReturnId(){
  static $returnId=null;
  if(!isset($returnId)){
  $returnId=base64_decode( WGlobals::get('returnid','','string'));
  }  return $returnId;
  }
public static function getUserState($token,$name,$default=null,$type='',$getInTruc=false){
static $uid=null;
if( WGlobals::getMemoryUsed()=='cookie'){
$memory='cookie';
if(!isset($uid))$uid=WUser::get('uid');
$token=$uid . $token;}else{
$memory='session';
}$old=WGlobals::get($token, null, $memory, $type );
if(false===$getInTruc){
$new=WGlobals::get($name, null, '',$type );
}elseif( is_string($getInTruc)){
$myArrayA=WGlobals::get($getInTruc, null, '','array');
if(isset($myArrayA[$name])){
$new=$myArrayA[$name];
} else $new=null;
}elseif(true===$getInTruc){
$trk=WGlobals::get( JOOBI_VAR_DATA, null, '','array');
$new=$trk['x'][$name];
}else{
$new=WGlobals::get($name, null, '',$type );
}
if($old===null && $new===null){
WGlobals::set($token, $default, $memory );
return WGlobals::filter($default, 'string');
}elseif($new===null){
  return $old;
}else{
$new=WGlobals::filter($new, 'string');
WGlobals::set($token, $new, $memory );
return $new;
}
}
public static function getMemoryUsed(){
if( PLIBRARY_NODE_USERSTATEMEMORY=='auto'){
$acceptCookie=WGlobals::getCookie('kmZelo4d9k4m', false);
if(empty($acceptCookie)){
WGlobals::setCookie('kmelo4d9k4m', true);
$memory='session';
}else{
$memory='cookie';
}
}elseif( PLIBRARY_NODE_USERSTATEMEMORY=='cookie'){
$memory='cookie';
}else{
$memory='session';
}
return $memory;
}
public static function getEntireSuperGlobal($src=''){  
$data=null;
WGlobals::_getArray($data, $src );
return WGlobals::filter($data );
return $new;
  }
public static function setCandy($level=0,$extension='zzzzz'){
if(!isset(self::$levelAccess[$extension])) self::$levelAccess[$extension]=$level;
}
public static function getCandy($extension='zzzzz'){
if('wordpress'==JOOBI_FRAMEWORK_TYPE){
if(empty($extension) || 'zzzzz'==$extension)$extension=WExtension::get( WApplication::getApp(). '.application','wid');
return ((isset(self::$levelAccess[$extension]))?self::$levelAccess[$extension] : 0 );
}else{
return 50;}}
public static function checkCandy($level,$dontHave=false,$extension='zzzzz'){if(empty($level))$level=0;
if( is_string($level) && !is_int($level)){
switch($level){
case 'pro':
$level=50;
break;
case 'plus':
$level=25;
break;
default:
$level=0;
break;
}}
if(!is_int($level)) return ( ! $dontHave?false : true);
return (( ! $dontHave )?WGlobals::getCandy($extension ) >=$level : WGlobals::getCandy($extension ) < $level );
}
public static function getSugar($extension='zzzzz'){
return ((isset(self::$levelSugar))?self::$levelSugar : 0 );
}
public static function setSugar($full=false){
$main=($full?201 : 101 );
if(!isset(self::$levelSugar)) self::$levelSugar=$main;
}
public static function count($index='c',$plus=1){
if(!isset( self::$_countA[$index] )) self::$_countA[$index]=0;
self::$_countA[$index] +=$plus;
return self::$_countA[$index];
}
public static function stringFilter($data,$parserName='',$check='normal'){
static $parser=null;
if(( IS_ADMIN && $check=='normal') || $check=='nocheck'){
return WGlobals::_decode($data );
}
return WApplication::stringFilter($data, true);
  }
public static function filter($data=null,$type='',$default=null,$src='',$name='',$check='normal'){
if(empty($data)) return $data;
if(is_array($data)) return WGlobals::_arrayFilter($data, $type, $default, $check );
if( is_object($data) || 'object'==gettype($data)) return WGlobals::_objectFilter($data, $type, $default, $check );
switch( strtolower($type)){
case 'int':
case 'integer':
case 'numeric':
return preg_replace('#[^0-9_]#','',$data );case 'float':
case 'double':
preg_match('#-?[0-9]+(?:\.[0-9]+)?#A',(string)$data,$match);
return @(float)$match[0];
case 'number':return preg_replace('#[^0-9.,-_]#','',$data );case 'bool':
case 'boolean':
return (bool)$data;
case 'safejs':$pattern=array("'", '"', "\n\r", "\r\n", "\n", "\r" );
return (string)str_replace($pattern, " ", $data );
case 'phrase':
return (string)preg_replace( "#[^a-zA-Z_ ']#", '',$data );
case 'word':
return (string)preg_replace('#[^a-zA-Z_]#','',$data );
case 'alnum':case 'alphanum':
case 'alphanumeric':
return (string)preg_replace('#[^a-zA-Z0-9_]#','',$data);
case 'jsnamekey':  $data=str_replace( array('.','-','[',']'), '_',$data );
case 'namekey':  $data=str_replace( array('|',' '), '_',$data );
return (string)preg_replace('/[^a-zA-Z0-9_.\-]+/','',$data );
case 'email':
  if( filter_var($data, FILTER_VALIDATE_EMAIL )) return $data;
  else return false;
case 'task':return (string)preg_replace('/[^a-zA-Z0-9_&|=.\-]+/','',$data );
  case 'url':
return urlencode( filter_var($data, FILTER_SANITIZE_URL ));
case 'string':
  return filter_var($data, FILTER_SANITIZE_STRING );
  case 'sef':
  $data=str_replace( array(' ','-','.',':', "'", '__','%','&','@','#','\\','/','(',')','[',']','{','}','*',',',';','?','+','„','”','"'), '_',$data );
  if( function_exists('mb_strtolower'))$data=mb_strtolower($data, 'UTF-8');
  else {
  $mess=WMessage::get();
  $mess->adminE('The multibyte extension ("mbstring") needs to be installed!');
  }  return $data;
case 'path':
$data_raw=$data;
$special_chars=array("?", "[", "]", "/", "\\", "=", "<", ">", ":", ";", ",", "'", "\"", "&", "$", "#", "*", "(", ")", "|", "~", "`", "!", "{", "}", chr(0));
$data=preg_replace( "#\x{00a0}#siu", ' ',$data );
$data=str_replace($special_chars, '',$data );
$data=str_replace( array('%20','+'), '-',$data );
$data=preg_replace('/[\r\n\t -]+/','-',$data );
$data=trim($data, '.-_');
$parts=explode('.',$data);
if( count($parts ) <=2){
return $data;
}
$data=array_shift($parts);
$extension=array_pop($parts);
foreach((array)$parts as $part){
$data .='.'.$part;
if( preg_match("/^[a-zA-Z]{2,5}\d?$/", $part)){
$allowed=false;
}
}
$data .='.'.$extension;
return $data;
case 'html':  if( is_string($data)){
  return (string)WGlobals::stringFilter($data, '',$check );
  }else{
  return $data;
  }
case 'htmlentity':  $data=html_entity_decode($data );   return (string)WGlobals::stringFilter($data, '',$check );
case 'base64':
return (string)preg_replace('#[^a-zA-Z0-9+=\/]#','',$data );
default:
if( is_bool($data)){return $data;
}elseif( is_numeric($data)){return $data;
}
return WGlobals::filter($data, 'html',$default, '','',$check );
}
  }
private static function _arrayFilter($data,$type='',$default=null,$check='normal'){
if(is_array($data)){
$nA=array();
  foreach($data as $key=> $val){
  $nA[ WGlobals::filter($key, 'namekey', null, '','',$check ) ]=WGlobals::filter($val, $type, null, '','',$check );
  }  return $nA;
}else{
$mess=WMessage::get();
$mess->codeE('You asked for an array with a WGlobals::get( but the variable you searched is not an array. We will use the default value',array(), 4 );
return $default;
}
  }
private static function _cacheFolder(){
$mainFolder=WApplication::cacheFolder();
$cacheFolder=$mainFolder.DS.'HTMLPurifier';
$systemFolderC=WGet::folder();
if(!$systemFolderC->exist($cacheFolder)){
$systemFolderC->displayMessage(false);
if(!$systemFolderC->create($cacheFolder )){
$mess=WMessage::get();
$FOLDER=$mainFolder;
$mess->adminE('The cache folder could not be found. The cache of the input filter will be disabled. This will slow the process of pages. If you want to enable the caching, please correct the permissions of the folder '.$FOLDER );
return false;
}}
return $cacheFolder;
}
private static function _quotes($data,$type){
static $sybase;
static $magic_quotes;
if(!isset($magic_quotes))$magic_quotes=get_magic_quotes_gpc();
if($magic_quotes && $type !='_FILES'){
if(empty($data) || is_numeric($data)){
return $data;
}elseif(is_array($data)){
  $newData=array();
foreach($data as $key=> $val){
$newData[WGlobals::_quotes($key,$type)]=WGlobals::_quotes($val,$type);
}
return $newData;
}elseif( is_object($data)){
$attributes=get_object_vars($data );
if(count($attributes)>0){
foreach($attributes as $key=> $val){
unset($data->$key);
$key=WGlobals::_quotes($key,$type);
$data->$key=WGlobals::_quotes($val,$type);
}}
return $data;
}else{ 
  if(!isset($sybase))$sybase=ini_get('magic_quotes_sybase');
  if($sybase){
return str_replace("''", "'", $data);
  }  
  return stripslashes($data);
}
}
return $data;
  }
private static function _getArray(&$array,&$name){
if( substr($name,0,1) !='_')$name='_'.$name;
$name=strtoupper($name );
switch($name){
case '_SERVER':
$array=$_SERVER;
break;
case '_POST':
$array=$_POST;
break;
case '_GET':
$array=$_GET;
break;
case '_COOKIE':
$array=$_COOKIE;
break;
case '_FILES':
$array=$_FILES;
break;
case '_SESSION':
$name='_SESSION';
  $array=(isset($_SESSION))?$_SESSION : null;
break;
case '_METHOD':
$name=$_SERVER['REQUEST_METHOD'];
WGlobals::_getArray($array, $name);
break;
case '_JOOBI':
$name='_JOOBI';
break;
case '_GLOBAL':
$name='_GLOBAL';
break;
case '_GLOBALS':
  $name='GLOBALS';
  $array=$GLOBALS;
break;
default:
$name='_REQUEST';
$array=$_REQUEST;
break;
}
  }
private static function _static($type,$var='',$array='',$value=null){
  static $vars;
switch($type){
    case 0:
$vars=array();
break;
    case 1:
$vars[$var]=array();
break;
    case 2:
return is_array($vars);
    case 3:
  if(!isset($vars[$var])) return null;
return @is_array($vars[$var]);
    case 4:
return @array_key_exists($var,$vars);
    case 5:return @array_key_exists($var, $vars[$array] );
    case 6:
$vars[$var][$array]=true;
break;
    case 7:
$vars[$var][$array]=$value;
break;
    case 8:
return (isset($vars[$var][$array])?$vars[$var][$array] : null );
    case 9:
if( @array_key_exists($array, $vars[$var])){
    if( is_string($vars[$var][$array])){
$vars[$var][$array] .=$value;
  }elseif(is_array($vars[$var][$array])){
    $vars[$var][$array]=array_merge($vars[$var][$array], $value );
  }
}else{
  $vars[$var][$array]=$value;
}
break;
  default:
break;
}
  }
private static function _set($location,$name,$value,$propagate=true,$overwrite=true){
  switch($location){
case '_JOOBI':
case '_GLOBAL':
if($overwrite===true){
  $type=7;
}elseif($overwrite===false){
  $existingValue=WGlobals::_static(8,$location,$name, $value);
if(empty($existingValue)){
WGlobals::_static(7,$location,$name, $value);
}
return;
}elseif(is_string($overwrite) && $overwrite=='append'){
  $type=9;
}else{
  $mess=WMessage::get();
  $mess->codeW('The type of the variable stored in the joobi static doesn\'t support appending. You variable is '.$name );
  return;
}
WGlobals::_static($type, $location, $name, $value );
return;
break;
case '_REQUEST':
case '_GET':
case '_POST':
  break;
default:
break;
}
WGlobals::_static( 6, $location, $name );
switch($location){
case '_SERVER':
  $_SERVER[$name]=$value;
  return;
case '_SESSION':
WGlobals::setSession($name, '',$value );
return;
case '_COOKIE':
WGlobals::setCookie($name, $value );
return;
case '_REQUEST':
$_REQUEST[$name]=$value;
if($propagate){
  WGlobals::_set('_POST',$name, $value, false);
  WGlobals::_set('_GET',$name, $value, false);
}
return;
case '_GET':
$_GET[$name]=$value;
break;
case '_POST':
$_POST[$name]=$value;
break;
case '_FILES':
$_FILES[$name]=$value;
return;
default:
return;
}
if($propagate){
WGlobals::_set('_REQUEST',$name, $value, false);
}
  }
private static function _check($value){
foreach($value as $val){
if(is_object($val)){
return false;
}elseif(is_array($val)){
if(!WGlobals::_check($val)){
return false;
}
}
}return true;
  }
private static function _decode($data){
static $array=array();
if( defined('JOOBI_CHARSET') && JOOBI_CHARSET !='UTF-8'){
$data=WPage::changeEncoding($data,JOOBI_CHARSET,'UTF-8');
}
return $data;
  }
private static function _objectFilter($data,$type='',$default=null,$check='normal'){
if( is_object($data)){
$className=get_class($data);
$nO=new $className;
$attributes=get_object_vars($data );
foreach($attributes as $key=> $val){
$key=WGlobals::filter($key,'alnum', null, '','',$check );
$nO->$key=WGlobals::filter($val, $type, $default, '','',$check );
}
return $nO;
}else{
return $default;
}
  }
  private static function _storeEID($double=false,$set=null){
static $eid=null;static $setEID=null;
if(isset($set)){
$eid=$set;
$setEID=$set;
$trk=WGlobals::get( JOOBI_VAR_DATA );
$pkey=(!empty($trk['s']['pkey'])?$trk['s']['pkey'] : '');
$sid=(!empty($trk['s']['mid'])?$trk['s']['mid'] : 0 );
  $myNewpKey=array();
if(!is_array($pkey)){
  $myNewpKey[]=$pkey;
}else{
$myNewpKey=$pkey;
}
foreach($myNewpKey as $onlyOnePKEY){
  $eidmap=$onlyOnePKEY.'_'.$sid;
  WGlobals::set($eidmap, null );
}
WGlobals::set('eid', null );
return true;
}
if(isset($setEID)) return $setEID;
$modelID=WGlobals::get('formModelID', 0, 'global');
if(empty($eid) || !empty($modelID)){
$eid=WGlobals::get('eid', null, '','alnum');
if(empty($eid)){
  $trk=WGlobals::get( JOOBI_VAR_DATA );
  if(!empty($trk['s']['pkey'])){
$pkey=$trk['s']['pkey'];
}else{
$pkey='';
}
  $sid=(!empty($trk['s']['mid']))?$trk['s']['mid'] : '';
  if(!empty($modelID) && !empty($sid) && $modelID !=$sid)$sid=0;
if(!empty($pkey)){
if(is_array($pkey)){
$eid=array();
foreach($pkey as $onePKEY){
$myEID=WGlobals::get($onePKEY.'_'.$sid, null, '','alnum');
if(!empty($myEID))$eid[]=$myEID;
}}else{
$eid=WGlobals::get($pkey.'_'.$sid, null, '','alnum');
}
if(empty($eid)){
if(is_array($pkey)){
$eid=array();
foreach($pkey as $uniquePK){
$eid[]=(isset($trk[$sid][$uniquePK]))?$trk[$sid][$uniquePK] : 0;
}}else{
$eid=(isset($trk[$sid][$pkey]))?$trk[$sid][$pkey] : 0;
}
}
}elseif(!empty($sid)){  
$PKs=WModel::get($sid, 'pkey');
if( strpos($PKs, ',')===false){$eid=WGlobals::get($pkey.'_'.$sid, null, '','alnum');
}else{
$expolodePK=explode(',',$PKs );
if(!empty($expolodePK)){
foreach($expolodePK as $onePK){
if(isset($trk[$sid][$onePK]))$eid[$onePK]=self::filter($trk[$sid][$onePK], 'alnum');
}}else{
$eid=0;
}}
}else{
$eid=0;
}
}
}
if(!$double){
$vql=((is_array($eid))?( reset($eid)) : $eid );
}else{
if(is_array($eid)){
$vql=$eid;
}else{
$vql=($eid !=0 )?array( 0=>(string)$eid ) : array();
}
}
return $vql;
  }
}