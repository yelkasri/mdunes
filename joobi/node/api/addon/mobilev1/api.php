<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
abstract class WUsers extends WUser {
}
abstract class WRoles extends WRole {
}
abstract class WPages extends WPage {
public static function redirect($url=null,$wPageID=true,$route=true,$code=303,$extraURL=''){
static $count=0;
if( WGlobals::get('wajx')) return;
$errorCode='';
$count++;
if($count > 3){
$errorCode='The page is redirecting too many times!';
WMessage::log(' --- The page is redirecting too many times! : ','mobile-debug-redirect');
WMessage::log($url , 'mobile-debug-redirect');
WMessage::log( print_r( debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS ), true) , 'mobile-debug-redirect');
echo WApplication_mobilev1::response($errorCode, 'TOO_MANY_REDIRECT', WText::t('1420853890NVDH'));
exit;
}
if($url=='previous'){
static $countPrevious=0;
$countPrevious++;
if($countPrevious > 1){
WGlobals::set('controller','');
WGlobals::set('task','');
}
self::_launchAgainApplication($errorCode );
}
$storeMessage=true;
if(!empty($extraURL)) WGlobals::set('extraURL-Joobi',$extraURL, 'global');
if(!isset($url)){
$url=WGlobals::getReturnId();
$url=WPage::routeURL($url, '','default',$SSL, $wPageID );
}elseif($route){
$url=trim($url );
$startURL=substr($url, 0, 4 );
if($startURL !='http' && $startURL !='inde'){
$url=WPage::routeURL($url,'smart', false, false, $wPageID ); }else{
$storeMessage=false;
}
}
if($storeMessage){
$message=WMessage::get();
$php_errors=ob_get_clean();
$php_errors=trim($php_errors);
if(!empty($php_errors))$message->adminE($php_errors );
$message->store();
}
if(!empty($extraURL)){
$urlExtra=strpos($url, '?')?'&' : '?';
$urlExtra .=ltrim($extraURL, '&');
$url .=htmlentities($urlExtra );}
$isPopUp=WGlobals::get('is_popup', false, 'global');
if(!IS_ADMIN && $isPopUp){
$url .=URL_NO_FRAMEWORK;$url .='&isPopUp=true';}
$url=str_replace('&amp;','&',$url );
if( strpos($url, '&') !==false){
$explodedA=explode('&',$url );
$explodedNewA=array();
$len=strlen( JOOBI_VAR_DATA );
foreach($explodedA as $oneS1){
if( substr($oneS1, 0, $len )==JOOBI_VAR_DATA ) continue;
$explodedNewA[]=$oneS1;
}$url=implode('&',$explodedNewA );
}
$pos=strrpos($url, '?');
if($pos !==false){
$url=substr($url, $pos+1 );
}
$explodeURLA=explode('&',$url );
$hasTask=false;
if(!empty($explodeURLA)){
foreach($explodeURLA as $onePs){
$stilEA=explode('=',$onePs );
if(!empty($stilEA[0])){
$value=(!empty($stilEA[1])?$stilEA[1] : '');
WGlobals::set($stilEA[0], $value );
if('task'==$stilEA[0])$hasTask=true;
}}
if(empty($hasTask)){
WGlobals::set('task','');
}
$ctrl=WGlobals::get('controller');
if(  'cart'==$ctrl){
$cartO=WCart::get();
$cartO->resetCart();
}
self::_launchAgainApplication($errorCode );
}
return false;
}
private static function _launchAgainApplication($errorCode){
WController::resetTask();
$app=new WApplication_mobilev1();
$response=$app->make( null, null, $errorCode );
echo $response;
exit;
}
}