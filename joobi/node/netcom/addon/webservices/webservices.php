<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Netcom_Webservices_addon extends Netcom_Parent_class {
public $protocol='webservices';
protected $version='1.0';
protected $_baseQueryURL='/ws.php';
public $result=null;
public $errors=null;
public function protocolSend(){
if(empty($this->data)){
$this->errors='Data is empty.';
return false;
}
$SerializeOption=null;
$response=null;
$isError=false;
$status=false;
$netcomRequest=new NetcomRequest;
if(!empty($this->credentials->APIUsername)){
$netcomCredentials=new NetcomCredentials;
$netcomCredentials->username=$this->credentials->APIUsername;
if(!empty($this->credentials->APIPassword))$netcomCredentials->password=$this->credentials->APIPassword;
if(!empty($this->credentials->APISignature))$netcomCredentials->signature=$this->credentials->APISignature;
$netcomRequest->credentials=$netcomCredentials;
}$netcomRequest->data=$this->data;
try {
switch($this->formatRequest){
case 'JSON':
WLoadFile('main.encoders.jsonencoder', JOOBI_DS_INC );
$requestToSend=$this->_jsonFunctions('encode',$netcomRequest, $isError );
$response=$this->_makeTheCall($requestToSend );
break;
case 'SOAP11':
WLoadFile('main.encoders.soapencoder', JOOBI_DS_INC );
$requestToSend=SoapEncoder::Encode($netcomRequest, $SerializeOption );
$response=$this->_makeTheCall($requestToSend );
break;
case 'XML':
WLoadFile('main.encoders.xmlencoder', JOOBI_DS_INC );
$requestToSend=XMLEncoder::Encode($netcomRequest, $SerializeOption );
$response=$this->_makeTheCall($requestToSend );
break;
default:
$this->errors='Request data format not supported.';
$response=false;
return false;
break;
}
if($response !==false && substr($response, 0, 6 ) !='ERROR:'){
switch($this->formatResponse){
case 'JSON':
$response=$this->_jsonFunctions('decode',$response, $isError );
break;
case 'SOAP11':
WLoadFile('main.encoders.soapencoder', JOOBI_DS_INC );
$response=SoapEncoder::Decode($response, $isError );
break;
case 'XML':
WLoadFile('main.encoders.xmlencoder', JOOBI_DS_INC );
$response=XMLEncoder::Decode($response, $isError );
break;
default:
$this->errors='Response data format not supported.';
return false;
break;
}
if($isError){
$this->errors=$response;
$response=null;
$status=false;
}else{
$status=true;
}
}else{
if($response !==false)$response=substr($response, 6 );
else $response='';
$status=false;
}
}catch( Exception $ex){
throw new Exception('Error occurred in protocolSend method');
}
$this->result=$response;
return $status;
}
public function receiver($error=null){
if(!isset($this->connector))$this->connector=new stdClass;
$this->connector->name=WGlobals::get('HTTP_X_WEBSERVICE_APP_NAME','','server');
$this->connector->version=WGlobals::get('HTTP_X_WEBSERVICE_APP_VERSION','','server');
$this->connector->id=WGlobals::get('HTTP_X_WEBSERVICE_APP_ID','','server');
$this->connector->node=WGlobals::get('HTTP_X_WEBSERVICE_APP_NODE','','server');
$this->connector->fct=WGlobals::get('HTTP_X_WEBSERVICE_APP_FUNCTION','','server');
$this->connector->node=Netcom_Dispatcher_class::clean($this->connector->node );
$this->connector->fct=Netcom_Dispatcher_class::clean($this->connector->fct );
WMessage::log('Netcom_Dispatcher_class receiver','webservices');
if(empty($this->connector->node) || empty($this->connector->fct)){
return $this->_dataToReturn('Node and Function not valid.', false);
}
$requestFormat=WGlobals::get('HTTP_X_WEBSERVICE_DATA_FORMAT_REQUEST','','server');
if(empty($requestFormat)){
return $this->_dataToReturn('Request Data format not defined.', false);
}$this->defineFormat($requestFormat, 'request');
$responseFromat=WGlobals::get('HTTP_X_WEBSERVICE_DATA_FORMAT_RESPONSE','','server');
if(empty($responseFromat))$this->formatResponse=$this->formatRequest;
else $this->defineFormat($responseFromat, 'response');
$ip=WGlobals::get('HTTP_X_WEBSERVICE_SECURITY_IP','','server');
$entireData=file_get_contents("php://input");
WMessage::log('data received','webservices');
WMessage::log($entireData, 'webservices');
if(empty($entireData) || trim($entireData)=='') return $this->_dataToReturn('Data provided is empty.', false);
$isError=false;
switch($this->formatRequest){
case 'JSON':
$entireData=$this->_jsonFunctions('decode',$entireData, $isError );
break;
case 'SOAP11':
WLoadFile('main.encoders.soapencoder', JOOBI_DS_INC );
$entireData=SoapEncoder::Decode($entireData, $isError );
break;
case 'XML':
$entireData=simplexml_load_string($entireData );
if(empty($entireData )){
return $this->_dataToReturn('No data has been defined or the format is not XML', false);
}
break;
}
if(!empty($entireData->credentials->signature)){
$entireData->credentials->signature=base64_decode($entireData->credentials->signature );
}
if(!empty($entireData->credentials->password)){
$entireData->credentials->password=base64_decode($entireData->credentials->password );
}
WMessage::log('credentials','webservices');
WMessage::log($entireData->credentials, 'webservices');
if(!empty($entireData->credentials)){
$this->defineCredentials($entireData->credentials );
}
if(!empty($entireData->action)){
$this->connector->node=$entireData->action->node;
$this->connector->fct=$entireData->action->method;
}
if(empty($entireData->data )){
return $this->_dataToReturn('No data has been defined or the format is incorrect', false);
}
$data=$entireData->data;
WMessage::log('Define &&&& credentials','webservices');
WMessage::log($this->credentials, 'webservices');
if(!isset($this->netcomInstanceC))$this->netcomInstanceC=WClass::get( trim($this->connector->node). '.netcom', null, 'class', false);
if(empty($this->netcomInstanceC)){
return $this->_dataToReturn('The requested Node does not exist', false);
}else{
$functionName=$this->connector->fct;
if( method_exists($this->netcomInstanceC, $functionName )){
$this->setVersion();
if(!$this->checkCredentials()){
return $this->_dataToReturn('The credentials could not be verified for the specified function.', false);
}
if($isError){
return $this->_dataToReturn('Error decoding the data.', false);
}
$data=$this->_object2array($data );
WMessage::log('$_usingExpotFct 2','webnserice-export');
WMessage::log($data, 'webnserice-export');
$response=$this->netcomInstanceC->$functionName($data );
WMessage::log($response, 'webservices');
WMessage::log('Step 3','webservices');
$SerializeOption=null;
switch($this->formatResponse){
case 'JSON':
$response=$this->_jsonFunctions('encode',$response, $isError );
break;
case 'SOAP11':
WLoadFile('main.encoders.soapencoder', JOOBI_DS_INC );
$response=SoapEncoder::Encode($response, $SerializeOption );
break;
case 'XML':
WLoadFile('main.encoders.xmlencoder', JOOBI_DS_INC );
$response=XMLEncoder::Encode($response, $SerializeOption );
break;
}
return $this->_dataToReturn($response );
}else{
return $this->_dataToReturn('The requested Function in specified Node does not exist', false);
}
}
return $this->_dataToReturn('Error Web Services', false);
}
private function _dataToReturn($data='',$status=true,$formatError=false){
if(!$status){
if($formatError){
$data='ERROR:'.$data;
}else{
$data='ERROR:'.$data;
}
}ob_end_clean();
echo $data;
}
private function _jsonFunctions($encode,$data,&$isError){
if($encode=='encode'){
if( function_exists('json_encode')){
$response=json_encode($data );
if($response===false)$isError=true;
}else{
WLoadFile('main.encoders.jsonencoder', JOOBI_DS_INC );
$response=JSONEncoder::Encode($data );
}}else{
  if( function_exists('json_decode')){
$response=json_decode($data );
if($response===false)$isError=true;
}else{
WLoadFile('main.encoders.jsonencoder', JOOBI_DS_INC );
$strObjName='';
$data=JSONEncoder::Decode($response, $isError, $strObjName );
}}
return $response;
}
private function _makeTheCall($MsgStr){
if(!defined('TRUST_ALL_CONNECTION')) define('TRUST_ALL_CONNECTION', false);
$ch=curl_init();
curl_setopt($ch, CURLOPT_URL, $this->connector->url . $this->connector->queryURL );
curl_setopt($ch, CURLOPT_VERBOSE, 0 );
if( strtoupper(TRUST_ALL_CONNECTION)=='FALSE'| TRUST_ALL_CONNECTION==0){
 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
 curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
}elseif(strtoupper(TRUST_ALL_CONNECTION)=='TRUE'| TRUST_ALL_CONNECTION==1){
 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
 curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
}
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1 );
curl_setopt($ch, CURLOPT_POST, 1 );
$headersA=$this->_setupHeaders();
curl_setopt($ch, CURLOPT_HTTPHEADER, $headersA );
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_POSTFIELDS, $MsgStr );
$response=curl_exec($ch);
if( curl_errno($ch)){
  $curl_error_no=curl_errno($ch) ;
  $curl_error_msg=curl_error($ch);
 }else{
 curl_close($ch);
 }
return $response;
}
private function _setupHeaders($authMode=false){
$headersA=array();
$headersA[]="X-WEBSERVICE-APP-NAME: ".'Service';$headersA[]="X-WEBSERVICE-APP-VERSION: " . $this->version;$headersA[]="X-WEBSERVICE-APP-ID: " . 1;
$headersA[]="X-WEBSERVICE-APP-NODE: " . $this->connector->node;$headersA[]="X-WEBSERVICE-APP-FUNCTION: " . $this->connector->fct;
$headersA[]="X-WEBSERVICE-DATA-FORMAT-REQUEST: " . $this->formatRequest;$headersA[]="X-WEBSERVICE-DATA-FORMAT-RESPONSE: " . $this->formatResponse;
$headersA[]="X-WEBSERVICE-SECURITY-IP: ". WUser::get('ip');
return $headersA;
}
}
class NetcomRequest {
public $credentials='';
public $data='';
}
class NetcomCredentials {
public $username='';
public $password='';
public $signature='';
}