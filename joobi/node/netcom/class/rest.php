<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Netcom_Rest_class extends WClasses {
private $_userAgent='';
private $_content_type='';
private $_urlEncode=true;private $_encodingFormat='serialize';
private $_follow=1;
private $_method='POST';
private $_force_fsock=false;
private $_vars=null;
private $_timeout=20;
private $_httpHeaderA=array();
var $_username;
var $_password;
var $_authtype;
var $_authNeeded=false;
function __construct($url='',$vars=null){
if($url !=''){
$this->_url=$url;
$this->_scan_url();
}
if($vars !=null){
$this->_vars=$vars;
}
}
public function setTimeOut($timoutSecond=20){
if($timoutSecond < 20)$timoutSecond=20;
$this->_timeout=$timoutSecond;
}
public function setContentType($contentType){
if(!empty($contentType))$this->_content_type=$contentType;
}
public function setEncodingData($encoding='serialize'){
$this->_encodingFormat=$encoding;
}
public function urlEncode($urlEncode=true){
$this->_urlEncode=$urlEncode;
}
public function setMethod($method='POST'){
$this->_method=strtoupper($method );
}
public function setUserAgent($userAgent){
$this->_userAgent=$userAgent;
}
public function setHeader($header){
if(empty($header )) return false;
$this->_httpHeaderA[]=$header;
}
function useCurl($bool=true){
$this->_force_fsock=!$bool;
}
function setCredentials($username,$password,$authtype='HTTP'){
$this->_authNeeded=true;
$this->_username=$username;
$this->_password=$password;
$this->_authtype=$authtype;
}
function getCredentials(&$username,&$password,&$authtype){
$username=$this->_username;
$password=$this->_password;
$authtype=$this->_authtype;
}
public function send($url='',$vars=null){
if(!$this->_force_fsock){
if(!function_exists('curl_init')){
@dl('curl.'.PHP_SHLIB_SUFFIX);
}
}
if($url!=''){
$this->_url=$url;
$this->_scan_url();
}
if($vars==null)$vars=$this->_vars;
if(!$this->_force_fsock && function_exists('curl_init')){
$ch=curl_init();
switch($this->_method){
case 'POST':
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $this->_encodeData($vars ));
break;
case 'GET':
if( count($vars) > 0){
$this->_url .='?';
$this->_url.=$this->_encodeData($vars );
}break;
case 'PUT':
 curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
 curl_setopt($ch, CURLOPT_POSTFIELDS, $this->_encodeData($vars ));
 break;
case 'DELETE':
 curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
 if( count($vars) > 0){
$this->_url .='?';
$this->_url.=$this->_encodeData($vars );
 } break;
}
if(!empty($this->_content_type))$this->_httpHeaderA[]='Content-Type: '.$this->_content_type;
if(!empty($this->_httpHeaderA )) curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_httpHeaderA );
if(!empty($this->_userAgent )) curl_setopt($ch, CURLOPT_USERAGENT, $this->_userAgent );
$safwMode=ini_get('safe_mode');
$openBaseDir=ini_get('open_basedir');
curl_setopt($ch, CURLOPT_URL, $this->_url );
curl_setopt($ch, CURLOPT_VERBOSE, 1);
curl_setopt($ch, CURLOPT_FAILONERROR, 1 );
if(empty($safwMode) && empty($openBaseDir)) curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $this->_follow );
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
if($this->_authNeeded){
$username=$password=$authtype='';
$this->getCredentials($username, $password, $authtype );
switch($authtype){
case 'HTTP':
curl_setopt($ch, CURLOPT_USERPWD, "$username:$password" );
break;
}}
curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1 );
curl_setopt($ch, CURLOPT_TIMEOUT, $this->_timeout );
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->_timeout );
$body=curl_exec($ch);
if( curl_errno($ch)){
$this->codeE( curl_error($ch));
}
$headers=curl_getinfo($ch );
curl_close($ch);
}else{$crlf="\r\n";
$request=$this->_method.' ';
$auth='';
if($this->_authNeeded){
$username=$password=$authtype='';
$this->getCredentials($username, $password, $authtype );
switch($authtype){
case 'HTTP':
$auth="Authorization: Basic " . base64_encode($username . ":" . $password). $crlf;
break;
}
}
switch($this->_method){
case 'POST':
if(is_array($vars) && count($vars) > 0){
$vars=$this->_encodeData($vars );
}
$request .=$this->_path.' HTTP/1.0'.$crlf;
$request .=$auth;
if($this->_userAgent!='')$request.="User-Agent: ".$this->_userAgent.$crlf;
if($this->_content_type=='')$this->_content_type='application/x-www-form-urlencoded';
$request .="Content-Type: ".$this->_content_type.$crlf;
$request .="Content-Length: " . strlen($vars).$crlf.$crlf;
$request .=$vars;
break;
case 'GET':
if(is_array($vars) && count($vars) > 0){
$url='?';
$url .=$this->_encodeData($vars);
}else{
$url='';
}$request .=$this->_path . $url.' HTTP/1.0'.$crlf;
$request .=$auth;
if($this->_userAgent!='')$request .="User-Agent: " . $this->_userAgent . $crlf;
if($this->_content_type=='')$this->_content_type='application/x-www-form-urlencoded';
$request .="Content-Type: " . $this->_content_type . $crlf . $crlf;
break;
}
$this->_fp=@fsockopen(( in_array($this->_protocol,array('https','ssl'))?'ssl://' : ''). $this->_host, $this->_port, $errNumber, $errString, $this->_timeout );
if(!$this->_fp){
$HOST=$this->_host;
$this->userE('1352928214LWCK',array('$HOST'=>$HOST));
$this->codeE('Could not establish a connection to '.$this->_protocol.'://'.$this->_host.':'.$this->_port.$this->_uri.' : '.$errString);
return false;
}
fputs($this->_fp, $request );
$response='';
while( !feof($this->_fp)){
$response .=fgets($this->_fp, 1024);
}
fclose($this->_fp);
$pos=strpos($response, $crlf . $crlf );
if($pos===false){
$this->codeE('Badly formatted response : '.$response);
return false;
}
$header=substr($response, 0, $pos );
$body=substr($response, $pos + 2 * strlen($crlf));
$headers=$this->_decode_header($header);
if($this->_follow && isset($headers['location'] )){
return $this->send($headers['location'],$vars);
}
$body=$this->_decode_body($headers, $body, $crlf );
}
$headerCode=$headers['http_code'];
if($headerCode >=200 && $headerCode < 250){
return $body;
}elseif($headerCode >=300 && $headerCode < 310 )  {
return $body;
}elseif($headerCode >=400 && $headerCode < 500 )  {
$sResult=htmlentities($body, null, JOOBI_CHARSET );
$this->codeE('Error: header code:'. $headerCode.' | returned string:'.$sResult );
return false;
}elseif($headerCode >=500 && $headerCode < 600 )  {
$this->codeE('Server Error: '.$headerCode );
return false;
}else{
return $body;
}
return false;
}
public function downloadAndSaveFromURL($url,$location='',$showMessage=false){
$content=$this->downloadFileFromURL($url, $showMessage );
if(empty($content)){
return false;
}
$fileS=WGet::file();
$status=$fileS->write($location, $content, 'overwrite');
return $status;
}
public function downloadFileFromURL($url,$showMessage=false,$username='',$password=''){
@ini_set('allow_url_fopen', 1 );
$content='';
if( ini_get('allow_url_fopen')){
if(!empty($username) && !empty($password)){
$opts=array(
'http'=> array(
'method'=> "GET",
'header'=> "Authorization: Basic " . base64_encode( "$username:$password" )
)
);
$context=stream_context_create($opts );
$content=file_get_contents($url, false, $context );
}else{
$content=file_get_contents($url );
}
}elseif( function_exists('curl_init')){
$ch=curl_init();
curl_setopt($ch, CURLOPT_POST, 0 );
curl_setopt($ch, CURLOPT_FAILONERROR, 1 );
curl_setopt($ch, CURLOPT_URL, $url );
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
curl_setopt($ch, CURLOPT_TIMEOUT, 10 );
if(!empty($username) && !empty($password)) curl_setopt($ch, CURLOPT_USERPWD, $username.':'.$password );
$content=curl_exec($ch);
curl_close($ch);
}else{
if($showMessage){
$this->userE('1428371805ACSK');
}
return false;
}
return $content;
}
public function fetchFile($url='',$mode=2){
$contents='';
if(!ini_get('allow_url_fopen')){
if($mode==1)$this->userW('1392306654FFTR');
if($mode==1 || $mode==2 ) WMessage::log('allow_url_fopen is not enable on your server so file from external website cannot be downloaded','FileFetchError');
return $contents;
}
$contents=@file_get_contents($url );
if($contents===false){
if($mode==1)$this->userE('1392306654FFTS',array('$url'=>$url));
if($mode==1 || $mode==2 ) WMessage::log('File from url : '.$url.' could not be opened. Get the system on debug mode to read about the error.','FileFetchError');
$contents='';
}
return $contents;
}
private function _scan_url(){
$req=$this->_url;
if(function_exists('parse_url'))
{
$parsed=parse_url($req);
$this->_protocol=$parsed['scheme'];
$this->_host=$parsed['host'];
if(!isset($parsed['user']))
$parsed['user']='';
if($parsed['user'] !='')
{
$this->_username=$parsed['user'];
if(isset($parsed['pass']) && $parsed['pass'] !='')
{
$this->_password=$parsed['pass'];
}
}
if(!isset($parsed['path']))
$parsed['path']='';
$this->_path=$parsed['path'];
if(!isset($parsed['query']))
$parsed['query']='';
$this->_query=$parsed['query'];
if(!isset($parsed['fragment']))
$parsed['fragment']='';
$this->_fragment=$parsed['fragment'];
if(!isset($parsed['port'])){
$parsed['port']=(in_array($this->_protocol,array('https','ssl')))?443 : 80;
}
$this->_port=$parsed['port'];
$this->_uri=$this->_path.($this->_query!=''?'?'.$this->_query : ''). ($this->_fragment!=''?'#'.$this->_fragment : '');
}
else
{
$pos=strpos($req, '://');
$this->_protocol=strtolower(substr($req, 0, $pos));
$req=substr($req, $pos+3);
$pos=strpos($req, '/');
if($pos===false)
$pos=strlen($req);
$host=substr($req, 0, $pos);
if(strpos($host,'@') !==false)
{
list($credentials,$host)=explode('@',$host);
list($this->_username,$this->_password)=explode(':',$credentials);
}
if(strpos($host, ':') !==false)
{
list($this->_host, $this->_port)=explode(':',$host);
}
else
{
$this->_host=$host;
$this->_port=(in_array($this->_protocol,array('https','ssl')))?443 : 80;
}
$this->_uri=substr($req, $pos);
if($this->_uri=='')
$this->_uri='/';
if(strpos($this->_uri, '?') !==false)
{
list($this->_path, $rest)=explode('?',$this->_uri,2);
}
else
{
$rest=$this->_uri;
}
if(strpos($rest, '#') !==false)
{
if(isset($this->_path))
list($this->_query, $ths->_fragment)=explode('#',$rest,2);
else
list($this->_path, $ths->_fragment)=explode('#',$rest,2);
}
else
{
if(isset($this->_path))
$this->_query=$rest;
else
$this->_path=$rest;
}
}
}
private function _decode_header($str){
$part=preg_split ( "/\r?\n/", $str, -1, PREG_SPLIT_NO_EMPTY );
$out=array ();
for($h=0; $h < sizeof ($part ); $h++ )
{
if($h !=0 )
{
$pos=strpos ($part[$h], ':');
$k=strtolower ( str_replace (' ','', substr ($part[$h], 0, $pos )) );
$v=trim ( substr ($part[$h], ($pos + 1 )) );
}
else
{
$k='http_code';
$v=explode (' ',$part[$h] );
$v=$v[1];
}
if($k=='set-cookie')
{
$out['cookies'][]=$v;
}
elseif($k=='content-type')
{
if(($cs=strpos ($v, ';')) !==false)
{
$out[$k]=substr ($v, 0, $cs );
}
else
{
$out[$k]=$v;
}
}
else
{
$out[$k]=$v;
}
}
return $out;
}
private function _decode_body($info,$str,$eol="\r\n"){
$tmp=$str;
$add=strlen ($eol );
if(isset($info['transfer-encoding'] ) && $info['transfer-encoding']=='chunked')
{
$str='';
do
{
$tmp=ltrim ($tmp );
$pos=strpos ($tmp, $eol );
$len=hexdec ( substr ($tmp, 0, $pos ));
if(isset($info['content-encoding'] ))
{
$str .=gzinflate ( substr ($tmp, ($pos + $add + 10 ), $len ));
}
else
{
$str .=substr ($tmp, ($pos + $add ), $len );
}
$tmp=substr ($tmp, ($len + $pos + $add ));
$check=trim ($tmp );
} while ( ! empty ($check ));
}
elseif(isset($info['content-encoding'] ))
{
$str=gzinflate ( substr ($tmp, 10 ));
}
return $str;
}
private function _encodeData($vars){
if(empty($vars)) return '';
$string='';
switch($this->_encodingFormat){
case 'json':
$string=json_encode($vars );
break;
case 'serialize':
default:
$first=true;
if(!empty($vars) && (is_array($vars) || is_object($vars))){
foreach($vars as $k=> $v){
if($first)$first=false;
else $string.='&';
$string .=$k.'=';
if($this->_urlEncode){
if(is_array($v) || is_object($v))$v=serialize($v );
$string .=urlencode( stripslashes($v));
} else $string .=$v;
}}
break;
}
return $string;
}
}