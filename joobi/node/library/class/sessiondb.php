<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
abstract class Library_Sessionparent_class extends WClasses {
protected static $handler=array();
public function __construct($preferences=null){
$this->register();
}
public static function getInstance($name='default',$preferences=null){
if(empty(self::$handler[$name])){
self::$handler[$name]=WClass::get('library.session'.$name, $preferences );
}return self::$handler[$name];
}
public function register(){
session_set_save_handler(
array($this, 'open'), array($this, 'close'), array($this, 'read'),
array($this, 'write'), array($this, 'destroy'), array($this, 'cleanUp')
);
}
public function read($id){
return;
}
public function write($id,$data){
return true;
}
public function destroy($id){
return true;
}
public function open($savePath,$sessionName){
return true;
}
public function cleanUp($expire=1440){
return true;
}
}
class Library_Sessiondb_class extends Library_Sessionparent_class {
public function read($id){
$sessionM=WModel::get('library.session');
$sessionM->whereE('sessid',$id );
$sessionM->select('ip', 0, 'previousip','ip');
$sessionInfo=$sessionM->load('o','data');
if(!empty($sessionInfo)){
$userSessionC=WUser::session();
$myIP=$userSessionC->getIP();
if(!empty($sessionInfo->previousip) && '0.0.0.0' !=$sessionInfo->previousip && $sessionInfo->previousip !=$myIP){
$sessionM->whereE('sessid',$id );
$sessionM->setVal('ip',$myIP, 0, null, 'ip');
$sessionM->update();
}}
if(!empty($sessionInfo->data)){
return $sessionInfo->data;
}
return '';
}
public function write($id,$data){
$sessionM=WModel::get('library.session');
$sessionM->whereE('sessid',$id );
$sessid=$sessionM->load('lr');
if(!empty($sessid)){
$sessionM->whereE('sessid',$id );
$sessionM->setVal('framework', WApplication::$ID );
$sessionM->setVal('data',$data );
$sessionM->setVal('modified', time());
$sessionM->setVal('uid', WUser::get('uid'));
$status=$sessionM->update();
}else{
$sessionM->setVal('sessid',$id );
$sessionM->setVal('framework', WApplication::$ID );
$sessionM->setVal('data',$data );
$sessionM->setVal('modified', time());
$sessionM->setVal('uid', WUser::get('uid'));
$userSessionC=WUser::session();
$myip=$userSessionC->getIP();
if(!empty($myip))$sessionM->setVal('ip',$myip, 0, null, 'ip');
$sessionM->setVal('created', time());
$status=$sessionM->insert();
}
return $status;
}
public function destroy($id){
$sessionM=WModel::get('library.session');
$sessionM->whereE('sessid',$id );
return $sessionM->delete();
}
public function cleanUp($expire=1440){
$oldSessionTime=time() - $expire;
$sessionM=WModel::get('library.session');
$sessionM->where('modified','<',$oldSessionTime );
return $sessionM->delete();
}
public function close(){
session_write_close();
return true;
}
}