<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
ob_start();
define('JOOBI_FRAMEWORK_TYPE','mobile');
define('JOOBI_FRAMEWORK_TYPE_ID', 101 );
define('JOOBI_MAIN_APP','japps');
define('JOOBI_SITE_NAME','Joobi');
define('JOOBI_FORM_METHOD','');
define('JOOBI_FORM_HASOPTION', false);
define('JOOBI_FORM_HASRETURNID', false);
define('JOOBI_FORM_AUTOCOMPLETE', false);
define('JOOBI_URLAPP_PAGE','jmobile');
define('JOOBI_PAGEID_NAME','noUsedItemPage');
define('URL_NO_FRAMEWORK','');
define('JOOBI_DB_TYPE','mysqli');
define('JOOBI_SESSION_LIFETIME', 43200 );
define('JOOBI_USE_SEF', false);
class APIPage {
public static $title='';
public static $headerA=array();
public static function setTitle($title){
static $already=false;
if($already ) return;
$already=true;
APIPage::$title=$title;
}
public static function setDescription($title=''){
}
public static function setMetaTag($key='',$value=''){
}
public static function setGenerator($title=''){
}
public static function setLink($link,$relation,$relType='rel',$extraAttributesA=array()){
}
public static function setType($type){
}
public static function setLanguage($lang='en'){
}
public static function setDirection($dir='ltr'){
}
public static function getTemplate(){
}
public static function isRTL(){
}
public static function getSpoof($alt=null){
return false;
}
public static function addScript($header,$type='text/javascript'){
self::$headerA['js'][$header]=true;
}
public static function addStyleSheet($header,$type='text/css',$media=null,$attributes=array()){
self::$headerA['css'][$header]=true;
}
public static function addCSS($header,$type='text/css'){
self::$headerA['css_sc'][$header]=true;
}
public static function addJS($header,$type='text/javascript'){
self::$headerA['js_sc'][$header]=true;
}
public static function encoding(){
return 'utf-8';
}
public static function cmsRoute($link,$SSL=null){
return $link;
}
public static function cmsGetShema(){
return 'https';
}
public static function frameworkToken(){
return JOOBI_SITE_TOKEN;
}
function getMailInfo(){
}
public static function cmsDefaultTheme(){
return 'ionic';
}
public static function keepAlive($get=false){
}
}
class APIUser {
public static function getSessionId(){
return session_id();
}
public static function cmsMyUser($property=''){
$user=WGlobals::getSession('JoobiUser');
return ((empty($property))?$user : (isset($user->$property)?$user->$property : null ));
}
function cmsMakePassword($password){
}
}
class CMSAPIPage extends APIPage {
public static function routeURL($link,$absoluteLink='',$index=false,$SSL=false,$itemId=true,$foption=null,$noSEF=false){
static $currentOption=null;
static $item=null;
$link=trim($link);
if( substr($link, 0, 4 )==='http') return $link;
$absoluteLink=trim($absoluteLink );
if($link=='previous'){
$url=WGlobals::getReturnId();
if(!empty($url)) return WPage::routeURL($url, '','link',$SSL, false);
$referer=WGlobals::get('HTTP_REFERER','','server','string');
if(empty($referer) || strpos($referer,JOOBI_SITE)===false){
$referer=JOOBI_SITE;
}else{
$referer=str_replace('&amp;','&',$referer );
}return $referer;
}elseif($link=='home'){
return JOOBI_SITE;
}
if($index===false){
$isPopUp=WGlobals::get('is_popup', false, 'global');
if(($isPopUp ))$index='popup';
else $index='default';
}else{
$index=trim(strtolower($index));
}
$home=false;
if($absoluteLink=='smart'){
$absoluteLinkNewLink='';
}elseif($absoluteLink=='home'){
$absoluteLinkNewLink=JOOBI_SITE;
}elseif($absoluteLink=='admin'){
$absoluteLinkNewLink=JOOBI_SITE.'administrator/';
$itemId=false;
}elseif($absoluteLink){
$absoluteLinkNewLink=JOOBI_SITE . $absoluteLink.'/';
}else{
$absoluteLinkNewLink=$absoluteLink;
$noIndex=true;
}
if($index=='default'){
if(strpos($link,'index')!==0){
if(!isset($currentOption) && $foption==null)$currentOption=WApplication::name();
$link=ltrim($link,'&');
$link=$absoluteLinkNewLink . JOOBI_INDEX.'?'.$link;
}else{
$link=$absoluteLinkNewLink . $link;
}
}elseif($index=='popup'){
if(!isset($currentOption) && $foption==null)$currentOption=WApplication::name();
$link=$absoluteLinkNewLink . JOOBI_INDEX2.'?'.$link . URL_NO_FRAMEWORK;
}elseif($index=='link'){
$link=$absoluteLinkNewLink . (isset($noIndex)?'' : JOOBI_INDEX.'?'). $link;
}
$url=rtrim($link, '&');
return $url;
}
public static function createPopUpRelTag($x=550,$y=400){
return '';
}
public static function cmsGetComponentItemId($component,$view=''){
}
public static function cmsGetLinkBasedItemId($itemid){
}
public static function refreshFrameworkMenu($wid=null,$action='',$recursive=false){
}
public static function getPageId($page='',$task=''){
}
public static function getSpecificItemId($controller='',$task=''){
return null;
}
function jsPreload(){
}
public static function createPopUpLink($url,$text,$x=550,$y=400,$className='',$idName='',$title='',$justNormalLink=false,$extras=''){
if(empty($url)) return $text;
WPage::addJSLibrary('joobibox');
if(!empty($title))$title=' title="'.WGlobals::filter($title, 'string'). '"';
if(!empty($className))$className=' class="'.$className.'"';
if(!empty($idName))$idName=' id="'.$idName. '"';
return '<a href="'.$url.'"'.$title. $idName . $className . $extras.'>'.$text.'</a>';
}
public static function includeMootools(){
}
public static function includejQuery(){
static $includejQuery=false;
if(!$includejQuery){
$includejQuery=true;
}}
public static function includeRootScript(){
static $includeRootscript=false;
if(!$includeRootscript){
$rootscript=JOOBI_FOLDER.'/node/api/addon/'.JOOBI_FRAMEWORK.'/js/rootscript.1.2.js';
WPage::addScript($rootscript, 'none');
$rootscript=JOOBI_FOLDER.'/node/api/addon/'.JOOBI_FRAMEWORK.'/js/themescript.1.2.js';
WPage::addScript($rootscript, 'none');
$includeRootscript=true;
}}
public static function includeJoobiBox(){
}
function setToolTips(){
}
function interpretURL($segments){
}
function buildURL(&$query){
}
function parseURL($string,&$vars){
}
public static function linkNoSEF($url='',$type='standard'){
$url=trim($url);
return JOOBI_INDEX . $url;
}
public static function formURL($option='',$controller=''){
return '';
}
public static function clearCache($folder=''){
}
}
abstract class APIApplication {
public static function version($return='short'){
}
public static function cacheFolder(){
return JOOBI_DS_ROOT.'cache';
}
public static function cmsMainLang($location='site'){
$lgid=self::_getPremiumLanguage();
return WLanguage::get($lgid, 'code');
}
public static function cmsUserLang($short=false){
$lgid=self::_getPremiumLanguage();
$lang=WLanguage::get($lgid, 'code');
if($short)$lang=substr($lang, 0, 2 );
return $lang;
}
private static function _getPremiumLanguage($premium=true){
static $codelgid=null;
if(isset($codelgid)) return $codelgid;
$LanguagesM=WModel::get('library.languages');
$LanguagesM->remember('xLanguagePremiumx'.$premium, true); if($premium)$LanguagesM->whereE('premium', 1 );
$LanguagesM->orderBy('lgid','ASC');
$codelgid=$LanguagesM->load('lr','lgid');
return $codelgid;
}
public static function cmsAvailLang($path=''){
static $availLangA=array();
if(!empty($availLangA)) return $availLangA;
$LanguagesM=WModel::get('library.languages');
$LanguagesM->remember('xLanguagePublishedx', true); $LanguagesM->whereE('publish', 1 );
$LanguagesM->operator('OR');
$LanguagesM->whereE('availsite', 1 );
$LanguagesM->operator('OR');
$LanguagesM->whereE('availadmin', 1 );
$LanguagesM->orderBy('premium','DESC');
$LanguagesM->orderBy('lgid','ASC');
$AllCodesA=$LanguagesM->load('lra','code');
if(!empty($AllCodesA)){
foreach($AllCodesA as $oneLG){
$availLangA[$oneLG]=$oneLG;
}}else{
$availLangA=array('en'=>'en');
}
return $availLangA;
}
public static function cmsInitPlugin($obj){
}
public static function extract($file,$dest){
return false;
}
public static function installThemePath(){
define('JOOBI_URL_THEME_JOOBI','/');
define('JOOBI_DS_THEME_JOOBI', DS );
}
public static function renderLevel($level){
return '';
}
}
abstract class WApplication extends APIApplication {
public static $cmsName='mobilev1';
public static $ID=101;
public static function getFrameworkName(){
return self::$cmsName;
}
public static function name($short='default',$wPageID=null,$linkController=null){
$myOption=JOOBI_URLAPP_PAGE;
switch($short){
case 'application':
return $myOption.'.application';
break;
case 'wid':
return WExtension::get($myOption.'.application','wid');
break;
case 'short':
case 'com':
case 'default':
default:
return $myOption;
break;
}
}
public static function getApp($useDefault=true){
static $app=null;
if(isset($app)) return $app;
$url=WGlobals::get( JOOBI_URLAPP_PAGE, '', null, 'namekey');
if($useDefault && empty($url)){
$url=JOOBI_MAIN_APP;
}
$app=strtolower($url );
return $app;
}
public static function mainLanguage($return='lgid',$force=false,$suggestedLang=array(),$location='site'){
static $lang=null;
if(empty($lang) || $force){
$langCode=array( APIApplication::cmsMainLang($location ));
if(!empty($langCode)){
$langCode[]=substr($langCode[0], 0, 2 );
$availableLanguageA=WApplication::availLanguages( array('lgid','name','code','locale'));
$foundLanguage=false;
foreach($langCode as $oneLGCode){
foreach($availableLanguageA as $availLang){
if($availLang->code==$oneLGCode){
$foundLanguage=true;
$lang=$availLang;
break;
}}if($foundLanguage ) break;
}
}
if(empty($lang)){
$lang=new stdClass;
$lang->lgid=1;
$lang->name='English';
$lang->code='en';
$lang->locale='en_GB.utf8,en_GB.UTF-8,en_GB,eng_GB,en,english,english-uk,uk,gbr,britain,england,great britain,uk,united kingdom,united-kingdom';
}
}
return $lang->$return;
}
public static function userLanguage(){
$lang=APIApplication::cmsUserLang();
$langCode=array( substr($lang, 0, 2 ));
$location=IS_ADMIN?'admin' : 'site';
$myLang=WApplication::mainLanguage('lgid', false, $langCode, $location );
return $myLang;
}
public static function availLanguages($map='code',$site='current'){
static $results=array();
if(is_array($map)){
$key=serialize($map);
}else{
$key=$map;
}
if(!isset($results[$key.$site])){
$results[$key.$site]=WApplication::_getLanguages($map );
}
return $results[$key.$site];
}
private static function _getLanguages($map){
static $results=array();
$languages=APIApplication::cmsAvailLang();
$bool=WPref::load('PLIBRARY_NODE_EXTLANG');
$availLangs=array();
foreach($languages as $lgKey=> $language){
if($bool){
$availLangs[]=$lgKey;
}else{
$availLangs[]=substr($lgKey, 0, 2 );
}}
$keyG=serialize($availLangs);
$cachedLanguageA=array();
foreach($availLangs as $oneCode){
$cachedLanguageA[]=WLanguage::get($oneCode, array('name','code','lgid','real','locale'));
}
if( is_string($map)){
$a=array();
foreach($cachedLanguageA as $oneLnag){
if(isset($oneLnag->$map))$a[]=$oneLnag->$map;
}return $a;
}elseif(is_array($map)){
$a=array();
foreach($cachedLanguageA as $oneLnag){
$obj=new stdClass;
foreach($map as $myMap){
$obj->$myMap=$oneLnag->$myMap;
}$a[]=$obj;
}return $a;
}else{
}
}
public static function setWidget($object){
}
public static function createMenu($name,$menuParent,$link,$option,$client=1,$access=0,$level=0,$ordering=0){
}
public static function isEnabled($component,$strict=true){
return true;
}
public static function enable($extension,$value=1,$type=''){
return true;
}
public static function getComponents($column=null){
}
public static function date($format=null,$time=null){
if(empty($time))$time=time();
if(empty($format))$format=WTools::dateFormat('date-number');
return date($format, $time );
}
public static function dateOffset(){
return 0;
}
public static function stringToTime($date=null){
if(empty($date))$date=time();
return strtotime($date );
}
public static function stringFilter($string,$html=false){
if(!class_exists('JFilterInput')) return $string;
if($html){
$safeHtmlFilter=JFilterInput::getInstance( null, null, 1, 1 );
$cleanString=$safeHtmlFilter->clean($string, 'string');
}else{
$noHtmlFilter=JFilterInput::getInstance();
$cleanString=$noHtmlFilter->clean($string, 'string');
}
return $cleanString;
}
}
class WApplication_mobilev1 {
public $cmsName='mobilev1';
public static function getFrameworkName(){
return self::$cmsName;
}
function make($entrypoint=null,$params=null,$errorCode=null){
static $joobiConf=true;
if(empty($errorCode)){
$processApplication=true;
}else{
$processApplication=false;
}
if($joobiConf){
define('JOOBI_CHARSET','UTF-8');
$joobiConf=false;
$this->_loadFrameWork();
require_once( JOOBI_LIB_CORE.'define.php');
require_once( JOOBI_DS_NODE.'api'.DS.'addon'.DS.JOOBI_FRAMEWORK.DS.'api.php');
WPref::override('PUSERS_NODE_FRAMEWORK_FE','users');
WPref::override('PUSERS_NODE_FRAMEWORK_BE','users');
WPref::override('PUSERS_NODE_LOGIN_STYLE', false);
WPref::override('PUSERS_NODE_USECAPTCHA', false);
WPref::override('PUSERS_NODE_LOGINALLOW', true);
WPref::override('PUSERS_NODE_REGISTRATION_LANDING', false);
WPref::override('PUSERS_NODE_REGISTRATION_PAGE', false);
WPref::override('PUSERS_NODE_LOGIN_PAGE', false);
WPref::override('PUSERS_NODE_LOGIN_LANDING', false);
WPref::override('PUSERS_NODE_ACTIVATIONMETHOD','none');
WPref::override('PLIBRARY_NODE_WIZARD', false);
WPref::override('PLIBRARY_NODE_WIZARDFE', false);
WPref::override('PLIBRARY_NODE_FANCYUPLOAD', false);
WPref::override('PLIBRARY_NODE_USEMINIFY', false);
WPref::override('PCART_NODE_USENEWCART', true);
WPref::override('PCART_NODE_ONEPAGECOLUMN', 1 );
WPref::override('PCART_NODE_AJAXCART', false);
WPref::override('PLIBRARY_NODE_AJAXPAGE', false);
define('JOOBI_SITE_TOKEN' , WGet::loadConfig('secret','mobilev1'));
WGet::loadLibrary();
if(!defined('IS_ADMIN')){
$isAdmin=WGlobals::get('isAdmin');
if(!empty($isAdmin )) define('IS_ADMIN', true);
else define('IS_ADMIN', false);
}
$session=WGet::session();
$UserSessionInfo=WGlobals::getSession('JoobiUser');
$logUser=WGlobals::get('logUser','');
$logPwd=WGlobals::get('logPwd','');
if(!empty($logUser) && !empty($logPwd)){
$usersCredentialC=WUser::credential();
$usersCredentialC->automaticLogin($logUser, $logPwd );
$UserSessionInfo=WGlobals::getSession('JoobiUser');
}
if(empty($UserSessionInfo)){
$tools=WUser::session();
$tools->setGuest();
}
}
WGlobals::set('resetForm','yes','global');
$content='';
$extType='application';
$namekey='';
$requestObj=WGlobals::getEntireSuperGlobal('request');
$space=WGlobals::get('space');
$apikey=WGlobals::get('apikey');
$skipKey=WGlobals::get('skipkey');
$token=WGlobals::get('token');
$lang=WGlobals::get('devicelanguage');
$lattitude=WGlobals::get('lat');
$longitute=WGlobals::get('lng');
$ip=WGlobals::get('ip');
$deviceInfoO=WGlobals::get('deviceInfo');
$inputA=WGlobals::get('inputA');
$initA=WGlobals::get('initA');
if(!empty($inputA))$inputA=json_decode($inputA );
if(!empty($initA))$initA=json_decode($initA );
if(!empty($deviceInfoO))$deviceInfoO=json_decode($deviceInfoO );
if(!empty($lang)){
$usedLang=substr($lang, 0, 2 );
WGlobals::set('lang',$usedLang, 'get');
}
if(!defined('JOOBI_APP_DEVICE_SIZE')){
if(!empty($deviceInfoO)){
if(!empty($deviceInfoO->isPhone) && $deviceInfoO->isPhone=='true'){
define('JOOBI_APP_DEVICE_SIZE', 11 );
define('JOOBI_APP_DEVICE_TYPE','ph');
}elseif(!empty($deviceInfoO->isTablet) && $deviceInfoO->isTablet=='true'){
define('JOOBI_APP_DEVICE_TYPE','tb');
define('JOOBI_APP_DEVICE_SIZE', 22 );
}else{
define('JOOBI_APP_DEVICE_TYPE','ph');
define('JOOBI_APP_DEVICE_SIZE', 33 );
}}else{
define('JOOBI_APP_DEVICE_TYPE','ph');
define('JOOBI_APP_DEVICE_SIZE', 11 );
}
}
$returnFormat=WGlobals::get('htmlOnly');
if(!empty($returnFormat)){
WGlobals::setSession('outputFormat','htmlOnly',$returnFormat );
}else{
$returnFormat=WGlobals::getSession('outputFormat','htmlOnly','');
}
if(!defined('JOOBI_FRAMEWORK_FORMAT_OUPUT')) define('JOOBI_FRAMEWORK_FORMAT_OUPUT',$returnFormat );
if(!empty($skipKey)){
$spkipCheck=WGlobals::getSession('device','skipKey');
if($spkipCheck !=$skipKey ) exit;
}else{
$specialController=WGlobals::get('controller');
if('mobile-key' !=$specialController){
$spaceDeviceC=WClass::get('mobile.device');
$spaceDeviceC->checkAPIkey($apikey );
}
}
$controller=WGlobals::get('controller');
$task=WGlobals::get('task','', null, 'task');
if(empty($controller)){
if(!empty($space)){
$outputSpaceC=WClass::get('output.space');
$spaceO=$outputSpaceC->findSpace();
WGlobals::set('controller',$spaceO->controller );
}else{
$processApplication=false;
$content='The data submitted contain some errors, the controller or space could not be identified!';
}
}
$success=true;$errorMessage='';
if($processApplication){
$content=WGet::startApplication($extType, $namekey, $params );
if(empty($content)){
$codeError=WGlobals::get('errorCode','','global');
if(!empty($codeError)){
$success=false;
$content=WText::t('1420853890NVDI');
switch($codeError){
case 'EXTENSION_NOT_FOUND':
$success='EXTENSION_NOT_FOUND';
$errorMessage=WText::t('1418240185PFXV');
break;
default:
$success='UNKNOWN_ERROR';
$errorMessage=WText::t('1418240185PFXW');
break;
}}}
}
$result=WApplication_mobilev1::response($content, $success, $errorMessage );
return $result;
}
private function _loadFrameWork(){
require( JOOBI_DS_CONFIG . JOOBI_FRAMEWORK_CONFIG.'.php');
$configC=new WFramework_Load_Config;
$configC->loadConfig();
}
public static function response($content='',$success=true,$errorMessage=''){
$controller=WGlobals::get('controller');
if('mobile-notif'==$controller){
$uid=WUser::get('uid');
$mobileId=999;
$mainMessageQueueC=WClass::get('main.messageprocess');
$notificationA=$mainMessageQueueC->getMobileNotification($uid, $mobileId );
}
$debugOnly=WGlobals::get('debugOnly');
if(empty($debugOnly)){
$ecoContent=ob_get_contents();
trim($ecoContent );
if( ob_get_contents()) ob_end_clean();
}
$response=new Connection_sending;
if( WUser::get('uid')){
$logPwd=WGlobals::get('logPwd','','global');
if(!empty($logPwd)){
$response->logPwd=$logPwd;
$logUser=WUser::get('username');
if(!empty($logUser))$response->logUser=$logUser;
WGlobals::set('isMenuLoaded','true');
}}else{
$response->action='RESET_CREDENTIALS';
$success=true;
}
if(true===$success){
$debug=JOOBI_DEBUGCMS || WPref::load('PLIBRARY_NODE_DBGERR') || JOOBI_FRAMEWORK_FORMAT_OUPUT || $debugOnly;
WLoadFile('api.addon.mobilev1.page');
$page=new Api_Mobilev1_Page_addon;
$response->html=$page->createPage($content, APIPage::$headerA, $debug );
$response->head=$page->createHead( APIPage::$headerA, $debug );
if(!empty($notificationA)){
$response->notification=$notificationA;
}
if( JOOBI_FRAMEWORK_FORMAT_OUPUT){
$head=str_replace('<script src="','<script src="'.JOOBI_SITE_PATH, $response->head );
$head=str_replace('/mobilev1/js/rootscript.1.2.js','/joomla30/js/rootscript.1.2.js',$head );
$body=str_replace('<form role="form"','<form role="form" action="'.JOOBI_SITE_PATH.'m1.php?option=com_japps&space=phonevendors&apikey=APIJOOBIMOBILETEST&htmlOnly=browser"',$response->html );
$fakeHTML='<!DOCTYPE html><html>';
$fakeHTML .='<head>';
$fakeHTML .=$head;
$fakeHTML .='</head>';
$fakeHTML .='<body>';
$fakeHTML .=$body;
$fakeHTML .='<br/><br/> ONLY DEBUG ON JOOMLA<br/>';
$fakeHTML .='</body>';
$fakeHTML .='</html>';
return $fakeHTML;
}
$mobileAction=WGlobals::get('mobileAction');
if(!empty($mobileAction)){
$response->action=strtoupper($mobileAction );
}
$params=WGlobals::get('mobileActionParams', null, 'global');
if(!empty($params )){
$response->params=$params;
}
$redirecturl=WGlobals::get('mobileActionURL','','global');
if(!empty($redirecturl )){
$response->redirectURL=$redirecturl;
}
$URL='';
$controller=WGlobals::get('controller');
if(!empty($controller ) && 'mobile-notif' !=$controller){
$URL='controller='.$controller;
}
if(!empty($URL))$response->currentURL=$URL;
if(true || WGlobals::get('isMenuLoaded','false')=='false'){
$keysA=WGlobals::get('menuKeys',array(), 'global');
if(empty($keysA)){
$outputSpaceC=WClass::get('output.space');
$spaceO=$outputSpaceC->findSpace();
if(!empty($spaceO->menu)){
$menuDispaly=WView::get($spaceO->menu );
if(!empty($menuDispaly )){
$menuDispaly->makeMenu(true);}
}
$keysA=WGlobals::get('menuKeys',array(), 'global');
}
if(!empty($keysA)){
$menuObj=new stdClass;
$menuObj->names=$keysA;
foreach($keysA as $temp){
$tempname=$temp;
$menuObj->$temp=WGlobals::get($temp, '','global');
}$response->menu=$menuObj;
$response->showMenu=true;
}else{
$response->showMenu=false;
WMessage::log('error loading the menu for the mobile apps','error-mobile-menu');
}
}else{
$response->showMenu=true;
}
}else{
$codeError=WGlobals::get('errorCode','','global');
if(empty($response->action)){
if(!empty($success))$response->action=$success;
elseif(!empty($codeError))$response->action=$codeError;
else $response->action='UNKNOWN_ERROR';
}if(!empty($errorMessage))$response->html=$errorMessage;
elseif(!empty($content)){
$response->html=$content;
}else{
$response->html=WText::t('1420853891WXL');
}}
$encoding=json_encode($response );
return $encoding;
}
}
class Connection_sending {
public $html='';
public $token='';public $expire='';public $head='';
public $action='';
public $menu=array();public $showMenu=false;
public $notification=array(); 
public $params=array(); public $redirectURL=''; 
}
