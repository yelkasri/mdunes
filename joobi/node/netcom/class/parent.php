<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Netcom_Parent_class extends WClasses {
public $protocol='ixr';
protected $version='1.0';
protected $timeout=0;
protected $showMessage=true;
private $encrypted=false;
private $_filterData=false;
protected $errors=array();
protected $nbErrors=0;
protected $node='';
protected $method='';
protected $result=null;
protected $_baseQueryURL='/joobi/index.php?netcom=netcom';
private $_queryURL='';
protected $connector=null;
private $_rowData=null;
protected $credentials=null;
protected $formatRequest='XML';
protected $formatResponse='XML';
protected $netcomInstanceC=null;
protected function _object2array($object){
return @json_decode(@json_encode($object),1);
}
public function defineCredentials($credentialsO){
$credentialsA=$this->_object2array($credentialsO );
if(!isset($this->credentials))$this->credentials=new stdClass;
$this->credentials->APIUsername=(!empty($credentialsA['username'])?$credentialsA['username'] : '');
$this->credentials->APIPassword=(!empty($credentialsA['password'])?$credentialsA['password'] : '');
$this->credentials->APISignature=(!empty($credentialsA['signature'])?$credentialsA['signature'] : '');
}
public function checkCredentials(){
if(!isset($this->netcomInstanceC))$this->netcomInstanceC=WClass::get($this->connector->node.'.netcom');
$servicesCredentialsA=$this->netcomInstanceC->getServicesCredentials();
if(!isset($servicesCredentialsA[$this->connector->fct] )
|| $servicesCredentialsA[$this->connector->fct]===false) return false;
if($servicesCredentialsA[$this->connector->fct]===true) return true;
$credentialsNode=$servicesCredentialsA[$this->connector->fct];
$classC=WClass::get($credentialsNode.'.credentials', null, 'class', false);
if(empty($classC)) return false;
$UserID=$classC->checkCredentials($this->credentials );
if($UserID===false) return false;
$this->netcomInstanceC->setAPIUserID($UserID );
return true;
}
public function setTimeout($timeout=20){
$this->timeout=$timeout;
}
public function setShowMessage($showMessage=true){
$this->showMessage=$showMessage;
}
public function setVersion($version=null){
if(isset($version)){
$this->version=$version;
}else{
if(!isset($this->netcomInstanceC))$this->netcomInstanceC=WClass::get($this->connector->node.'.netcom');
$this->netcomInstanceC->setVersion($this->connector->version );
}
}
public function defineFormat($format,$protocol='both'){
$format=strtoupper($format );
$acceptedValuesA=array('XML','JSON','SOAP11');
if(!in_array($format, $acceptedValuesA )){
$message=WMessage::get();
$message->userE('1351891949KVQG');
return false;
}
$protocol=strtolower($protocol );
if($protocol=='both'){
$this->formatRequest=$format;
$this->formatResponse=$format;
}elseif($protocol=='request'){
$this->formatRequest=$format;
}elseif($protocol=='response'){
$this->formatResponse=$format;
}
return true;
}
public function send($site,$node,$method,$data=null,$showMessage=null){
static $instance=array();
$nameServerStatus=WPref::load('PLIBRARY_NODE_SERVER_STATUS');
if(!$nameServerStatus){
$message=WMessage::get();
$message->adminE('The External Communication option is turn off. Turn it on in the General Preferences.');
return true;
}
if(isset($showMessage))$this->showMessage=$showMessage;
if( is_numeric($node))  $node=WExtension::get($node, 'namekey');
if(empty($node)) return false;
$this->node=$node;
$this->method=$method;
$this->data=$data;
if(empty($site)){
$site=WPref::load('PAPPS_NODE_HOME_SITE');
}
if(!isset($this->timeout))$this->timeout=WPref::load('PLIBRARY_NODE_SERVER_TIMEOUT');if($this->timeout < 10)$this->timeout=20;
$maxExecution=@ini_get('max_execution_time');
if($maxExecution > 10 && $maxExecution < $this->timeout)$this->timeout=$maxExecution;
if( strpos($this->_baseQueryURL, '&')===false){
if( strpos($this->_baseQueryURL, '?')===false){
$queryURL=$this->_baseQueryURL.'?protocol='.$this->protocol;
}else{
$queryURL=$this->_baseQueryURL.'&protocol='.$this->protocol;
}
} else $queryURL=$this->_baseQueryURL.'&protocol='.$this->protocol;
if(is_array($site)){
foreach($site as $s){
if(!$this->_validWebsite($s)){
continue;
}
$this->_callSend($s );
if($this->result !=null ) return $this->result;
}
}else{
if(!$this->_validWebsite($site)){
return false;
}
$this->_callSend($site, $queryURL );
if(!empty($this->result) && isset($this->result->status) && empty($this->result->status) && !empty($this->result->code)){
WNetcom::convertCode($this->result->code );
}
return $this->result;
}
}
public function getResults($url='',$returnedFormat=null){
return false;
}
public function showErrors(){
if(!empty($this->errors)){
$message=WMessage::get();
foreach($this->errors as $oneErr){
$message->userE($oneErr );
}}
}
private function _callSend($site,$queryURL){
$this->result=null;
$site=rtrim($site,'/');
$this->connector=new stdClass;
$this->connector->url=$site;
$this->connector->queryURL=$queryURL;
$this->connector->node=$this->node;
$this->connector->fct=$this->method;
if( defined('PLIBRARY_NODE_PLEX') && PLIBRARY_NODE_PLEX )  $this->connector->trace=true;$status=$this->protocolSend();
if($this->_filterData)$this->result=WGlobals::filter($this->result, '', null, '','','noencoding');
$this->nbErrors=count($this->errors );
if(!$status && !empty($this->errors))$this->_showErrors();
}
private function _validWebsite($site){
if(empty($site) || $site=='http://' || $site=='https://'){
$mess=WMessage::get();
$mess->adminW('A website need to be specified!');
return false;
}return true;
}
private function _showErrors(){
if(!empty($this->errors)){
$serverDown=false;
$noProtocol=false;
if( strpos($this->_rowData, 'SERVERDOWNFORMAINTENANCE') !==false){
$serverDown=true;}elseif( strpos($this->_rowData, 'REQUESTEDPROTOCOLNOTAVAILABLE') !==false){
$noProtocol=true;}$message=WMessage::get();
foreach($this->errors as $oneError){
if($this->showMessage){
if($serverDown)$message->userE('1298350265OFHX');
elseif($noProtocol)$message->userE('1298350265OFHY');
else {
$message->userE('1430154193FIWY',array('$oneError'=>$oneError));
$message->codeE('See the response below: ');
$clearScreen=ob_get_clean();
$message->codeE($this->_rowData.'<br/><br/>'.$clearScreen );
}}else{
if($serverDown)$message->log('The server is down for maintenance. Please try again later.','netcom_client');
elseif($noProtocol)$message->log('The protocol requested is not available on the server.','netcom_client');
else {
$message->log('XML-RPC communication error: '.$oneError, 'netcom_client');
$message->log('See the response below: ','xmlrpc_client');
$message->log($this->_rowData . "\n\n".ob_get_clean() , 'netcom_client');
}
}
}}
}
}