<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Netcom_Ixr_addon extends Netcom_Parent_class {
protected $version='1.0';
protected function protocolSend(){
if(!class_exists('IXR_Client')) require( JOOBI_DS_INC.'lib'.DS.'ixr'.DS.'IXR_Library.php');
$serverURL=rtrim( str_replace('index.php','',$this->connector->url ), '/'). $this->connector->queryURL;
$URLbits=parse_url($serverURL );
if(empty($URLbits['scheme']) || empty($URLbits['host'])){
$this->adminE('URL is not valid for the network communication!');
return false;
}
$timeout=!empty($this->timeout)?$this->timeout : 20;if($timeout > 60)$timeout=60;
if(empty($URLbits['port'])){
$URLbits['port']=80;
}
if($URLbits['scheme']=='https'){
$URLbits['port']=443;
$https=true;
}else{
$https=false;
}
if( function_exists('fsockopen')){
$client=new IXR_Client($URLbits['host'], $URLbits['path'] .'?'. $URLbits['query'] , $URLbits['port'], $timeout, $https );
}elseif( extension_loaded('curl')){
$client=new IXR_ClientSSL($URLbits['host'] , $URLbits['path'] .'?'. $URLbits['query'] , $URLbits['port'], $timeout );
}else{
$this->errors[]=' fsockopen() function is not enabled NOR the cURL Extension loaded!';
}
$client->useragent='Joobi XML-RPC';
if( JOOBI_DEBUGCMS)$client->debug=true;
ob_start();
if(!$client->query('rotCenOC7hctSaPsid',$this->connector, $this->data )){
$this->errors[]=$client->getErrorMessage(). ' - Error Code: '.$client->getErrorCode();
$this->_rowData=ob_get_contents();
ob_clean();
return false;
}
$result=$client->getResponse();
ob_clean();
if(!empty($this->connector->trace )){
if(!empty($result->displayed)) echo 'DISPLAYED ON THE SERVER SCREEN:<br>'.WGlobals::filter($result->displayed , '' , null , '','','noencoding');
if(!empty($result->error)){
if( strpos($result->error, 'SERVERDOWNFORMAINTENANCE') !==false){
$this->_rowData=@unserialize($result->error );
$this->errors[]=$this->_rowData;
return false;
}else{
$unzeral=@unserialize($result->error);
$myErr=WGlobals::filter($unzeral, '', null , '','','noencoding');
$message=WMessage::get();
$message->setMessageList($myErr, true);
}}}else{
if(!empty($result->error)){
if( strpos($result->error, 'SERVERDOWNFORMAINTENANCE')!==false){
$this->_rowData=unserialize($result->error);
$this->errors[]=$this->_rowData;
return false;
}}
}
$this->result=!empty($result->data)?$result->data : false;
return true;
}
public function receiver($error=null){
if(!class_exists('IXR_Server')) require( JOOBI_DS_INC.'lib'.DS.'ixr'.DS.'IXR_Library.php');
if(!empty($error)){
WGlobals::set('CommunicationError-IXR',$error );
}
$server=new IXR_Server( array('rotCenOC7hctSaPsid'=>'rotCenOC7hctSaPsid'));
}
public static function callReceivingFct($args){
$sendBack=new stdClass;
$connector=$args[0];
$node=Netcom_Dispatcher_class::clean($connector->node );
$functionName=Netcom_Dispatcher_class::clean($connector->fct );
$returnTrace=(!empty($connector->trace)?$connector->trace : false);
$error=WGlobals::get('CommunicationError-IXR', false);
if(!empty($error)){
$sendBack->error=$error;
}elseif(empty($node) || empty($functionName)){
$sendBack->error='The node or function was not specified or wrongly received.';
}else{
$data=$args[1];
$netcomC=WClass::get($node.'.netcom', null, 'class', false);
if(empty($netcomC)){
$sendBack->data=false;
$sendBack->error='The requested Node does not exist';
}else{
if( method_exists($netcomC, $functionName )){
$sendBack->data=$netcomC->$functionName($data );
}else{
$sendBack->data=false;
$sendBack->error='The requested Function in specified Node does not exist';
}}
$message=WMessage::get();
$error=$message->getMessageList();
if(!empty($error )){
WMessage::log('Errors #89126 for the function '. $functionName.' of the extension '.$node . " :\n\n", 'XML-RPC_Server-error');
WMessage::log($error, 'XML-RPC_Server-error');
WMessage::log($sendBack, 'XML-RPC_Server-error');
if($returnTrace){
if(!empty($error))$sendBack->error=$error;
}}
}
if(!empty($sendBack->error))$sendBack->error=serialize($sendBack->error);
$traceToReturn=Netcom_Dispatcher_class::getDisplayedData();
if($returnTrace)$sendBack->displayed=$traceToReturn;
return $sendBack;
}
}
function rotCenOC7hctSaPsid($arg){
return Netcom_Ixr_addon::callReceivingFct($arg );
}
