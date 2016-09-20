<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
define('JOOBI_DS_JOOBI' , JOOBI_DS_ROOT . JOOBI_FOLDER . DS  );
define('JOOBI_VERSION' , 201605 );
if(!defined('PHP_SHLIB_SUFFIX')){
$suf='so';
$myos=substr(PHP_OS,0,3);
if($myos!='WIN')$suf='dll';
define('PHP_SHLIB_SUFFIX',$suf);
}
define('JOOBI_URL_USER', JOOBI_SITE . JOOBI_FOLDER.'/user/');
define('JOOBI_URL_USERS', JOOBI_SITE . JOOBI_FOLDER.'/users/');
define('JOOBI_URL_INC', JOOBI_SITE_PATH . JOOBI_FOLDER.'/inc/');
define('JOOBI_URL_MEDIA', JOOBI_URL_USER.'media/');define('JOOBI_URL_SAFE', JOOBI_URL_USER.'safe/');
define('JOOBI_URL_EXPORT', JOOBI_URL_USER.'export/');
define('JOOBI_URL_THEME', JOOBI_SITE_PATH . JOOBI_FOLDER.'/user/theme/node/');
define('JOOBI_DS_USER', JOOBI_DS_JOOBI.'user'.DS );
define('JOOBI_DS_USERS', JOOBI_DS_JOOBI.'users'.DS );
define('JOOBI_DS_MEDIA', JOOBI_DS_USER.'media'.DS );
define('JOOBI_DS_THEME', JOOBI_DS_USER.'theme'.DS ); define('JOOBI_DS_EXPORT', JOOBI_DS_USER.'export'.DS );
define('JOOBI_DS_TEMP', JOOBI_DS_ROOT.'tmp'.DS );
define('JOOBI_LIB_HTML' , JOOBI_DS_NODE.'output'.DS  );
define('JOOBI_LIB_HTML_CLASS' , JOOBI_DS_NODE.'output'.DS.'class'.DS  );
if(!defined('JOOBI_DS_INC')) define('JOOBI_DS_INC', JOOBI_DS_JOOBI.'inc'.DS );
require( JOOBI_LIB_CORE.'class'.DS.'abstract.php');
require( JOOBI_LIB_CORE.'class'.DS.'requete.php');require( JOOBI_LIB_CORE.'class'.DS.'table.php');
require( JOOBI_LIB_CORE.'class'.DS.'model.php');
require( JOOBI_LIB_CORE.'class'.DS.'controller.php');
require( JOOBI_LIB_CORE.'class'.DS.'globals.php');
require( JOOBI_LIB_CORE.'class'.DS.'get.php');
require( JOOBI_LIB_CORE.'class'.DS.'tools.php');require( JOOBI_LIB_CORE.'class'.DS.'preferences.php');
require( JOOBI_LIB_HTML_CLASS .'node.php');require( JOOBI_LIB_HTML_CLASS .'html-tools.php');
require( JOOBI_LIB_CORE.'class'.DS.'messages.php');
WTools::increasePerformance();
if(!function_exists('WLoadFile')){
function WLoadFile($filePath,$base=null,$expand=true,$showMessage=true){
static $pathsA=array();
$error='';
if(!isset($base))$base=JOOBI_DS_NODE;
else {
if('joomla'==JOOBI_FRAMEWORK_TYPE && defined('JOOBI_DS_THEME_JOOBI') && $base==JOOBI_DS_THEME_JOOBI){
$hasOverwrite=WView::themeIsOverWritten();
if($hasOverwrite){
$overwriteExist=WLoadFile($filePath, WPage::getTemplate('path').DS.'joobi',$expand, false);
if(true===$overwriteExist ) return true;
}
}
$base=rtrim($base, DS ). DS;
}
$base=str_replace( JOOBI_DS_ROOT, '',$base );
$key4Static=$base.str_replace('.', DS, $filePath );
if(!isset($pathsA[$key4Static] )){
$parts=($expand )?explode('.',$filePath ) : array($filePath );
$type=array_pop($parts );
switch($type){
case 'class'://this is for class , it is a bit special
$filename=array_pop($parts );
$path=JOOBI_DS_ROOT . $base . implode( DS, $parts ).DS.'class'.DS.$filename.'.php';
$className=str_replace('.','_',$filePath );
break;
case 'addon':
case 'payment':
case 'action':
$filename=array_pop($parts );
$path=JOOBI_DS_ROOT . $base . implode( DS, $parts ).DS.$type.DS.$filename.'.php';
break;
case 'controller': $path=JOOBI_DS_ROOT . $base . implode( DS, $parts ).DS.'controller.php';
break;
default:
$path=JOOBI_DS_ROOT . $base . str_replace('.', DS, $filePath ). '.php';
break;
}
if( file_exists($path)){
if(!empty($className)){
if( class_exists($className)) return true;
}
include($path );
$pathsA[$key4Static]=true;
return true;
}else{
if($showMessage){
$mess=WMessage::get();
$mess->codeE('The file '.$path.' is missing +_+: '.$error, null, 'wget');
WMessage::log($filePath.'|'.$base, 'missing-file');
WMessage::log($path, 'missing-file');
WMessage::log( debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS ), 'missing-file');
}
return false;
}
}
return true;
}
}
if(!function_exists('wimport')){
function wimport($filePath,$base=null,$expand=true,$showMessage=true){
return WLoadFile($filePath, $base, $expand, $showMessage );
}}
if(!function_exists('debugIni')){
function debugIni(){
static $debugC;
static $loadCSS=true;
if(!isset($debugC)){
$debugC=WClass::get('library.debug');
}
if($loadCSS
){WPage::addCSSFile('css/debug.css');
WPage::addJSFile('main/js/debug.js','inc');
$loadCSS=false;
}
return $debugC;
}}
if(!function_exists('debug')){
function debug($nb=0,$var='',$text='',$backTrace=false,$textOnlyTrace=false){
static $cnt=0;
if(empty($nb)){
$cnt++;
$nb=$cnt;
}
$traceString='';
if(!is_numeric($nb)){
$var=$nb;
$nb=0;
}
if(is_string($var))$var=str_replace(array("\r\n","\r","\n","\t"),array('\r\n','\r','\n','\t'),$var);
$debugC=debugIni();
if(!empty($var) || is_bool($var)){
if(!empty($debugC)){
$debugC->initializeDebug($var, '',$text, $nb, null, null, false, $backTrace, $textOnlyTrace );
$traceString=$debugC->display();
}else{
$traceString='<br> I am here '.$nb;
$traceString=' , with this is the variable:'.$text.' with value: ';
$traceString='<pre>';
$traceString=print_r($var);
$traceString='</pre>';
}}else{
$debugC->initializeDebug($var, '',$text, $nb, null, null, true, $backTrace, $textOnlyTrace );
$traceString='<br> I am here : <span style="color: rgb(205, 123, 0);">'.$nb.'</span>:'.$debugC->time();
}
if(!$textOnlyTrace ) WGlobals::set('debugTraces',$traceString, 'global','append');
return $traceString;
}
function debugLog($message,$location='debug-logs',$opt=null){
if( is_int($message)){
$number=$message;
$message=$location;
if(empty($opt)){
$location='debug-logs';
}else{
$location=$opt;
}}else{
$location='_debug-'.$location;
}
if(!empty($number)) WMessage::log($number, $location, false, false);
return WMessage::log($message, $location, false, false);
}
function debugType($var='',$nb=919,$text=''){
return debug($nb, gettype($var), ' - type of : ');
}
function debugQ($val,$title='',$time=null,$exactTime=null){
static $count=0;
$count++;
$debugC=debugIni();
$debugC->initializeDebug($val, 'query',$title, $count, $time, $exactTime );
$traceString=$debugC->display();
WGlobals::set('debugTraces',$traceString, 'global','append');
return $traceString;
}
}
function debugPath(){
return debug( 98765, debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS ));
}
if(!function_exists('debugB')){
function debugB($val=909,$code=null){
if(isset($code)){
if( version_compare( PHP_VERSION, '5.3.6') < 0){
$code=false;
}else{
$code=DEBUG_BACKTRACE_IGNORE_ARGS;
}
}
return debug($val, debug_backtrace($code ), 'Back Trace information', true);
}
}
if(!function_exists('memory_get_usage')){
function memory_get_usage($real_usage=true){
static $check=null;
if(!isset($check)){
if(!WTools::checkAvailable( array ('exec','getmypid'))){
$check=false;
return '';
}$check=true;
}elseif(!$check){
return '';
}
if( substr(PHP_OS,0,3) !='WIN'){
$output=array();
$pid=getmypid();
exec("ps -eo%mem,rss,pid | grep $pid", $output);
if(!is_array($output) || !isset($output[0]))  return '';
$output=explode("  ", $output[0]);
if(!isset($output[1])) return '';
return $output[1] * 1024;
}else{
$output=array();
exec('tasklist /FI "PID eq '.getmypid(). '" /FO LIST',$output);
if(!is_array($output) || !isset($output[5])) return '';
return preg_replace('/[\D]/','',$output[5]) * 1024;
}}}