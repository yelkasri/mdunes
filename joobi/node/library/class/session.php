<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Library_Session_class extends WClasses {
protected $_lifetime=15;
protected $_checkBrowser=true;
protected $_checkURL=false;
protected $_forceSSL=false;
protected static $instance;
protected $_handlerInstance=null;
protected $_state='active';
public function __construct($storage='default',$sessionPreferences=null){
if($storage=='default')$storage='db';
if(!empty($sessionPreferences->onlyIfNotStarted) && session_id()){
return true;
}
if( session_id()){
session_unset();
session_destroy();
}
@ini_set('session.save_handler','files');
@ini_set('session.use_trans_sid','0');
if(empty($sessionPreferences->lifetime))$sessionPreferences->lifetime=15;
$this->_lifetime=$sessionPreferences->lifetime;
WLoadFile('library.class.session'.$storage );
$this->_handlerInstance=Library_Sessiondb_class::getInstance($storage, $sessionPreferences );
$this->_setPreferences($sessionPreferences );
$this->_start();
$this->_setInformation();
if(!$this->_securityChecks()){
echo 'failed session : ';
echo $this->_state;
$this->destroy();
$this->close();
exit();
}
}
public function __destruct(){
$this->close();
}
public static function getInstance($handler,$sessionPreferences){
if(!is_object(self::$instance)){
self::$instance=new Library_Session_class($handler, $sessionPreferences );
}
return self::$instance;
}
public function getName(){
if($this->_state==='destroyed'){
return null;
}return session_name();
}
public function getId(){
if($this->_state==='destroyed'){
return null;
}return session_id();
}
public function getState(){
return $this->_state;
}
public function getExpire(){
return $this->_lifetime;
}
public function isNew(){
$counter=WGlobals::getSession('sessionInfo','count', 0 );
if($counter===1){
return true;
}return false;
}
public function destroy(){
if($this->_state=='destroyed'){
return true;
}
$secretFrame=JOOBI_SITE_TOKEN;
$sessionName=session_name( md5($secretFrame . JOOBI_FRAMEWORK ));
$currentSession=WGlobals::getCookie($sessionName );
if(!empty($currentSession)){
$sessionID=session_id();
WGlobals::setCookie($sessionName, '', time() - 1000000 );
}
session_unset();
session_destroy();
$this->_state='destroyed';
return true;
}
public function restart(){
$this->destroy();
if($this->_state !=='destroyed'){
return false;
}
$this->_handlerInstance->register();
$this->_state='restart';
$id=$this->_createId();
session_id($id);
$this->_start();
$this->_state='active';
$this->_validate();
$this->_setInformation();
return true;
}
public function close(){
session_write_close();
return true;
}
private function _createId(){
$id=0;
while( strlen($id) < 32){
$id .=mt_rand( 0, mt_getrandmax());
}
$id=md5( uniqid($id, true));
return $id;
}
private function _start(){
if($this->_state=='restart'){
session_regenerate_id(true);
}else{
$secretFrame=JOOBI_SITE_TOKEN;
$sessionName=session_name( md5($secretFrame . JOOBI_FRAMEWORK ));
$currentSession=WGlobals::getCookie($sessionName );
if(!$currentSession){
$currentSession=WGlobals::get($sessionName );
if($currentSession){
$sessionID=session_id($currentSession );
$expiration=time() + $this->_lifetime * 60;
WGlobals::setCookie($sessionName, $sessionID, $expiration );
}
}
}
register_shutdown_function('session_write_close');
session_cache_limiter('none');
session_start();
$this->_state='active';
return true;
}
protected function _createToken($length=32){
static $chars='0123456789abcdef';
$max=strlen($chars) - 1;
$token='';
$name=session_name();
for($i=0; $i < $length; ++$i){
$token .=$chars[(rand(0, $max))];
}
return md5($token . $name);
}
private function _setInformation(){
$count=WGlobals::getSession('sessionInfo','count');
$count++;
WGlobals::setSession('sessionInfo','count',$count );
$start=WGlobals::getSession('sessionInfo','start');
if(empty($start)){
$start=time();
WGlobals::setSession('sessionInfo','start',$start );
WGlobals::setSession('sessionInfo','last',$start );
WGlobals::setSession('sessionInfo','current',$start );
}else{
WGlobals::setSession('sessionInfo','last', WGlobals::getSession('sessionInfo','current'));
WGlobals::setSession('sessionInfo','current', time());
}
}
private function _setPreferences(&$sessionPreferences){
if(!empty($sessionPreferences->name )) session_name( md5($sessionPreferences->name ));
if(!empty($sessionPreferences->id )) session_id( md5($sessionPreferences->id ));
if(!empty($sessionPreferences->lifetime ))$this->_lifetime=$sessionPreferences->lifetime;
if(isset($sessionPreferences->checkBrowser ))$this->_checkBrowser=$sessionPreferences->checkBrowser;
if(isset($sessionPreferences->checkURL ))$this->_checkURL=$sessionPreferences->checkURL;
if(isset($sessionPreferences->forceSSL ))$this->_forceSSL=$sessionPreferences->forceSSL;
@ini_set('session.gc_maxlifetime',$this->_lifetime );
return true;
}
private function _securityChecks($restart=false){
if($restart){
$this->_state='active';
if($this->_checkURL) WGlobals::setSession('sessionInfo','clientAddress', null );
if($this->_checkBrowser) WGlobals::setSession('sessionInfo','clientBrowser', null );
WGlobals::setSession('sessionInfo','clientForward', null );
WGlobals::setSession('sessionInfo','token', null );
}
if($this->_lifetime){
$maxTime=WGlobals::getSession('sessionInfo','last', 0 ) + $this->_lifetime * 60;
if($maxTime < WGlobals::getSession('sessionInfo','current', 0 )){
$this->_state='expired';
return false;
}
}
if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
WGlobals::setSession('sessionInfo','clientForward',$_SERVER['HTTP_X_FORWARDED_FOR'] );
}
if($this->_checkURL  && isset($_SERVER['REMOTE_ADDR'])){
$ip=WGlobals::getSession('sessionInfo','clientAddress', null );
if($ip===null){
WGlobals::setSession('sessionInfo','clientAddress',$_SERVER['REMOTE_ADDR'] );
}elseif($_SERVER['REMOTE_ADDR'] !==$ip){
$this->_state='error ip';
return false;
}
}
if($this->_checkBrowser && isset($_SERVER['HTTP_USER_AGENT'])){
$browser=WGlobals::getSession('sessionInfo','clientBrowser', null );
if($browser===null){
WGlobals::setSession('sessionInfo','clientBrowser',$_SERVER['HTTP_USER_AGENT'] );
}elseif($_SERVER['HTTP_USER_AGENT'] !==$browser){
$this->_state='error useragent';
return false;
}
}
return true;
}
}