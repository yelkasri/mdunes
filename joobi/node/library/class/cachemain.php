<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Library_Cachemain_class {
public $cache=null;
public function __construct($time=0){
if(!isset($this->cache)){$this->cache=new Library_Cachefile_class($time );
}
return $this->cache;
}
public static function &getInstance($time){
static $cache=array();
if(!isset($cache[$time])){$cache[$time]=new Library_Cachemain_class($time );
}
return $cache[$time];
}
public function getCache($folder,$id,$checkTime=true){
$d=$this->cache->retrieve($id, $folder, $checkTime );
if($d===false){
$locktest=$this->cache->lock($id, $folder );
if($locktest->locked==true && $locktest->locklooped==true){
$d=$this->cache->retrieve($id, $folder, $checkTime );
}if($locktest->locked==true){
$this->cache->unlock($id, $folder );
}}
if($d !==false){
$d=unserialize( trim($d ));
}
return $d;
}
public function setCache($folder,$id,$d,$wrkarounds=true){
$locktest=$this->cache->lock($id, $folder );
if($locktest->locked==false && $locktest->locklooped==true){
$locktest=$this->cache->lock($id, $folder);
}
$sucess=$this->cache->store( serialize($d), $id, $folder );
if($locktest->locked==true){
$this->cache->unlock($id, $folder );
}
return $sucess;
}
public function remove($id,$folder){
return $this->cache->remove($id, $folder );
}
public function clean($folder){
return $this->cache->clean($folder );
}
}
class Library_Cachefile_class extends Library_Cachemain_class {
private $_lifetime=720;private $_now=0;
private $_initalString='<?php die("die"); ?>#x#';
private $_root=null;
private static $_fileS=null;
public function __construct($lifeTime=0){
if(!empty($lifeTime))$this->_lifetime=$lifeTime;
$this->_now=time();
$this->_root=WApplication::cacheFolder(). DS;
}
public function retrieve($id,$folder,$checkTime=true){
$d=false;
$path=$this->_getFilePath($id, $folder );
if($checkTime==false || ($checkTime==true && $this->_checkExpire($id, $folder )===true)){
if( file_exists($path)){
$d=file_get_contents($path );
if($d){
$d=str_replace($this->_initalString, '',$d );
}}
return $d;
}else{
return false;
}
}
public function store($d,$id,$folder){
$written=false;
$path=$this->_getFilePath($id, $folder );
$d=$this->_initalString . $d;
$fileopen=@fopen($path, "wb" );
if($fileopen){
$len=strlen($d );
@fwrite($fileopen, $d, $len );
$written=true;
}
if($written && ($d==file_get_contents($path))){
return true;
}else{
return false;
}
}
public function remove($id,$folder){
$path=$this->_getFilePath($id, $folder );
if(!file_exists($path ) || ! unlink($path)){
return false;
}
return true;
}
public function clean($folder){
$return=true;
if(!isset(self::$_fileS)) self::$_fileS=WGet::folder( null, false);
if(empty($folder)){
$return=self::$_fileS->delete($this->_root );
if($return)$return=self::$_fileS->create($this->_root );
}else{
if( is_string($folder))$return=self::$_fileS->delete($this->_root.DS.$folder );
elseif(is_array($folder)) foreach($folder as $f)$return=self::$_fileS->delete($this->_root.DS.$f );
}
return $return;
}
public function lock($id,$folder,$lockTime=15){
$returning=new stdClass;
$returning->locklooped=false;
if('d-xzwNO__NEED__STATICwxz'==$id){$returning->locked=false;
return $returning;
}
$loopTime=$lockTime * 10;
$path=$this->_getFilePath($id, $folder );
if(!file_exists($path)){
$returning->locked=false;
return $returning;
}
$fileOpen=@fopen($path, "r+b" );
if(!is_resource($fileOpen )){
$returning->locked=false;
return $returning;
}
if($fileOpen){
$dLock=@flock($fileOpen, LOCK_EX );
}else{
$dLock=false;
}
if($dLock===false){
$lock_counter=0;
while($dLock===false){
if($lock_counter > $loopTime){
$returning->locked=false;
$returning->locklooped=true;
break;
}
usleep(100);
$dLock=@flock($fileOpen, LOCK_EX );
$lock_counter++;
}
}
$returning->locked=$dLock;
return $returning;
}
public function unlock($id,$folder=null){
$path=$this->_getFilePath($id, $folder );
if(!file_exists($path)){
return true;
}
$fileOpen=@fopen($path, "r+b" );
if(!is_resource($fileOpen )){
return true;
}
if($fileOpen){
$ret=@flock($fileOpen, LOCK_UN );
@fclose($fileOpen );
}
return $ret;
}
protected function _getCacheId($id,$folder){
if( strpos($id, '{') !==false){
$name=md5($id );}else{
$name=$id;}return 'w-'.$name;}
protected function _getFilePath($id,$folder){
$name=$this->_getCacheId($id, $folder );
$dir=$this->_root . $folder;
if(!is_dir($dir)){
if(!isset(self::$_fileS)) self::$_fileS=WGet::folder( null, false);
self::$_fileS->create($dir );
}
if(!is_dir($dir)){
return false;
}
return $dir.DS.$name.'.che';
}
protected function _checkExpire($id,$folder){
$path=$this->_getFilePath($id, $folder );
if( file_exists($path)){
$time=@filemtime($path);
if(($time + $this->_lifetime ) < $this->_now || empty($time)){
@unlink($path );
return false;
}
return true;
}
return false;
}
}