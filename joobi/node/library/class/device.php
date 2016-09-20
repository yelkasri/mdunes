<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Library_Device_class extends WClasses {
public function isMobile(){
  $nua=WGlobals::get('HTTP_USER_AGENT', null, 'server');
 if( strstr($nua, 'Mobile')){
 return true;
 }
 return false;
}
public function browser($return='object'){
static $browserO=null;
if(empty($browserO->key )){
$browserO=new stdClass;
$browserO->key=239;
$browserO->name=WText::t('1236672306LHEA');
$browserO->namekey='unknown';
$nua=WGlobals::get('HTTP_USER_AGENT', null, 'server');
if(!empty($nua)){
$nua=strtolower($nua );
$allBrowsersA=array();
$allBrowsersA[151]='robot';
$allBrowsersA[155]='googlebot';
$allBrowsersA[156]='feedfetcher-google';
$allBrowsersA[160]='msnbot';
$allBrowsersA[170]='bingbot';
$allBrowsersA[180]='baiduspider';
$allBrowsersA[185]='slurp';
$allBrowsersA[6]='rockmelt';
$allBrowsersA[5]='opera';
$allBrowsersA[2]='chrome';
$allBrowsersA[3]='safari';
$allBrowsersA[4]='msie';
$allBrowsersA[1]='firefox';
$allBrowsersA[220]='mozilla';
$allBrowsersA[221]='linux-gnu';
$allBrowsersA[181]='jakarta';$allBrowsersA[182]='feedparser';$allBrowsersA[183]='sogou';$allBrowsersA[184]='wordpress';$allBrowsersA[186]='site24x7';$allBrowsersA[187]='feedburner';$allBrowsersA[188]='kcb';
$allBrowsersA[189]='bot';
$found=false;
foreach($allBrowsersA as $key=> $brows){
if( strstr($nua, $brows )){
$myBrows=$key;
$browserO->key=$key;
$browserO->namekey=$brows;
$found=true;
break;
}
}
if(!$found){
}
$securityBroswerT=WType::get('security.browser', false);
if(!empty($securityBroswerT ))$browserO->name=$securityBroswerT->getName($browserO->key );
else $browserO->name=$browserO->namekey;
switch($browserO->namekey){
case 'msie': 
$www=explode(';',$nua );
if(!empty($www)){
foreach($www as $one){
if( strstr($one, 'msie')){
$nmv=explode(' ',$one );
$wwwFlA=array_filter($nmv );
break;
}}}
if(empty($wwwFlA)){
WMessage::log('Unknown Version of IE! :'.$nua, 'unknown-browser');
WMessage::log($www, 'unknown-browser');
WMessage::log($browserO, 'unknown-browser');
}else{
$browserO->version=array_pop($wwwFlA);
}
break;
case 'chrome': $aresult=explode('/', stristr($nua, 'Chrome'));
$aversion=explode(' ',$aresult[1]);
$browserO->version=$aversion[0];
break;
case 'firefox':
case 'mozilla':
$www=explode('/',$nua );
$browserO->version=preg_replace('#\((?:(?!\)).)*\)#','',end($www));
break;
case 'safari': $www=explode(' ',$nua );
foreach($www as $w){
if( strstr($w, 'version')){
$nmvA=explode('/',$w );
$browserO->version=array_pop($nmvA );
break;
}}break;
default:
break;
}
}
}
if($return!='object'){
return $browserO->$return;
} else return $browserO;
}
public function platform($return='object'){
static $platformO=null;
if(empty($platformO->key)){
$platformO=new stdClass;
$platformO->key=239;
$platformO->name=WText::t('1236672306LHEA');
$platformO->namekey='unknown';
$nua=WGlobals::get('HTTP_USER_AGENT', null, 'server');
if(!empty($nua)){
$nua=strtolower($nua );
$patformListA=array();
$patformListA['googlebot']=155;
$patformListA['feedfetcher-google']=156;
$patformListA['bingbot']=170;
$patformListA['baiduspider']=180;
$patformListA['slurp']=185;
$patformListA['ipad']=12;
$patformListA['ipod']=11;
$patformListA['iphone']=10;
$patformListA['macintosh']=1;
$patformListA['mac']=1;
$patformListA['android']=50;
$patformListA['windows']=2;
$patformListA['linux']=3;
$patformListA['nokia']=220;
$patformListA['blackberry']=221;
$patformListA['opensolaris']=5;
$patformListA['sunos']=4;
$patformListA['win']=2;
$patformListA['mozilla']=222;
$patformListA['bot']=189;
$patformListA['kcb']=188;
$patformListA['feedburner']=187;
$patformListA['site24x7']=186;
$patformListA['sogou']=183;
$patformListA['feedparser']=182;
$patformListA['jakarta']=181;
$found=false;
foreach($patformListA as $key=> $brows){
if( strstr($nua, $key)){
$platformO->namekey=$key;
$platformO->key=$brows;
$found=true;
break;
}
}
if(!$found){
}
$securityBroswerT=WType::get('security.os', false);
if(!empty($securityBroswerT))$platformO->name=$securityBroswerT->getName($platformO->key );
else $platformO->name=$platformO->namekey;
}
}
if($return!='object'){
return $platformO->$return;
} else return $platformO;
}
 }