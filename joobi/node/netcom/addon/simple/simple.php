<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
WLoadFile('netcom.parent.class');
class Netcom_Simple_addon extends Netcom_Parent_class {
var $encoding='UTF-8';
private $_resultHTML='';
protected function protocolSend(){
if( is_object($this->data)){
$this->data->node=$this->connector->node;
$this->data->fct=$this->connector->fct;
}elseif(is_array($this->data)){
$this->data['node']=$this->connector->node;
$this->data['fct']=$this->connector->fct;
}else{
$value=$this->data;
$this->data=new stdClass;
$this->data->data=$value;
$this->data->node=$this->connector->node;
$this->data->fct=$this->connector->fct;
}
$result=base64_encode( json_encode($this->data));
$link='controller=main&task=image&path='.$result;
$this->_resultHTML='<img src="'.WPages::linkHome($link ). '" border="0" width="1" height="1" />';
return true;
}
public function getImageLink(){
return $this->_resultHTML;
}
public function getResults($url='',$returnedFormat=null){
if  ( in_array('curl', get_loaded_extensions())){
$ch=curl_init($url );
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_FAILONERROR, 1 );
curl_setopt($ch, CURLOPT_TIMEOUT, 10 );
$data=curl_exec($ch );
curl_close($ch );
}elseif( ini_get('allow_url_fopen')){ob_start();
$data=file_get_contents($url);
$errors=ob_get_clean();
}else{
if($this->showMessage){
$message=WMessage::get();
$message->adminE('Could not connect to the URL, because neither CURL nor allow_url_fopen is supported on the server.');
}$data=false;
}
if(empty($data)) return $data;
$returnedFormat=strtolower($returnedFormat );
if(empty($returnedFormat )){
return $data;
}elseif('json'==$returnedFormat){
return json_decode($data );
}elseif('xml'==$returnedFormat){
return xml_parse($data );
}else{
if($this->showMessage){
$message=WMessage::get();
$message->codeE('Format not yet supported!');
}return $data;
}
}
}