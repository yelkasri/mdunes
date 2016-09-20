<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
ob_start();
define('JOOBI_FRAMEWORK_TYPE','framework');
define('JOOBI_FRAMEWORK_TYPE_ID', 1 );
define('JOOBI_MAIN_APP','japps');
define('JOOBI_SITE_NAME','Joobi');
define('JOOBI_FORM_METHOD','post');
define('JOOBI_FORM_HASOPTION', false);
define('JOOBI_FORM_HASRETURNID', true);
define('JOOBI_FORM_AUTOCOMPLETE', true);
define('JOOBI_APP_DEVICE_TYPE','bw');
define('JOOBI_APP_DEVICE_SIZE','');
define('JOOBI_URLAPP_PAGE','');
define('JOOBI_PAGEID_NAME','noUsedItemPage');
define('JOOBI_USE_SEF', false);
class APIPage {
public static $headerA=array();
public static function setTitle($title){
static $already=false;
if($already ) return;
self::$headerA['title']=$title;
$already=true;
}
public static function setDescription($desc=''){
self::$headerA['desc']=$desc;
}
public static function setMetaTag($key,$value=''){
self::$headerA['others'][$key]=$value;
}
public static function setGenerator($gen){
self::$headerA['generator']=$gen;
}
public static function setLink($link,$relation,$relType='rel',$extraAttributesA=array()){
$obj=new stdClass;
$obj->link=$link;
$obj->relation=$relation;
$obj->relType=$relType;
$obj->extraA=$extraAttributesA;
self::$headerA['link'][]=$obj;
}
public static function setType($type){
}
public static function setLanguage($lang='en-GB'){
self::$headerA['lang']=$lang;
}
public static function setDirection($dir='ltr'){
self::$headerA['dir']=$dir;
}
public static function getTemplate(){
}
public static function isRTL(){
if(!empty( self::$headerA['dir'] )){
return ( self::$headerA['dir']=='ltr'?true : false);
}else{
return true;
}}
public static function getSpoof($alt=null){
static $id=null;
if(isset($id)) return $id;
$id=md5( WUser::get('uid'). WUser::getSessionId());
return $id;
}
public static function addScript($header,$type='text/javascript'){
self::$headerA['js'][$header]=true;
}
public static function addStyleSheet($header,$type='text/css',$media=null,$attributes=array()){
self::$headerA['css'][$header]=true;
}
public static function addCSS($script,$type='text/css'){
self::$headerA['css_sc'][]=$script;
}
public static function addJS($script,$type='text/javascript'){
self::$headerA['js_sc'][]=$script;
}
public static function encoding(){
return 'utf-8';
}
public static function cmsRoute($link,$SSL=null){
return $link;
}
public static function cmsGetShema(){
WMessage::log('still need to implement this function cmsGetShema  ','development-missing');
return 'https';
}
public static function frameworkToken(){
return JOOBI_SITE_TOKEN;
}
function getMailInfo(){
}
public static function cmsDefaultTheme(){
return 'framework';
}
public static function keepAlive($get=false){
static $keepAlive=false;
if($get){
if($keepAlive){
return '';
}}else{
echo 'needs to be implemented keep page alive';
$keepAlive=true;
}}
}
class APIUser {
public static function getSessionId(){
return session_id();
}
public static function cmsMyUser($property=''){
$user=WGlobals::getSession('JoobiUser');
return ((empty($property))?$user : $user->$property );
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
$absoluteLink=trim($absoluteLink);
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
$absoluteLinkNewLink=JOOBI_SITE.$absoluteLink.'/';
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
if(!IS_ADMIN && !$noSEF){
if( WPref::load('PLIBRARY_NODE_SSLFE')){
$SSL=true;
}
$link=rtrim($link,'&');
if( substr($link, 0, strlen(JOOBI_SITE))==JOOBI_SITE){
$subLink=substr($link, strlen(JOOBI_SITE));
$url=($itemId )?WPage::cmsRoute($subLink, $SSL ) : $subLink;
static $pathOnly=null;
if(!isset($pathOnly))$pathOnly=trim( JOOBI_SITE_PATH, '/');
if(!empty($pathOnly)){
$pathOnlyLen=strlen($pathOnly);
if( substr($url, 0, $pathOnlyLen) ==$pathOnly)$url=substr($url, $pathOnlyLen );
}
$url=ltrim($url, '/');
$url=JOOBI_SITE . $url;
}else{
$url=$link;
}return $url;
}else{
$url=rtrim($link,'&');
return $url;
}
}
public static function createPopUpRelTag($x=550,$y=400){
}
public static function cmsGetComponentItemId($component,$view=''){
}
public static function cmsGetLinkBasedItemId($itemid){
return false;
}
public static function refreshFrameworkMenu($wid=null,$action='',$recursive=false){
return true;
}
public static function getPageId($page='',$task=''){
}
public static function getSpecificItemId($controller='',$task=''){
}
function jsPreload(){
$isPopUp=WGlobals::get('is_popup', false, 'global');
if( IS_ADMIN && !$isPopUp){
$js='var submenu=document.getElementById("submenu-box");if(submenu)submenu.style.display=\'none\';';
$js.='var toolmenu=document.getElementById("toolbar-box");if(toolmenu)toolmenu.style.display=\'none\';';
WPage::addJSScript($js);
}
return true;
}
public static function createPopUpLink($url,$text,$x=550,$y=400,$className='',$idName='',$title='',$justNormalLink=false,$extras=''){
if(empty($url)) return $text;
if( strpos($x, '%') !==false){
$x_pr=str_replace ("%", "", $x);
if($x_pr > 100)$x_pr=100;
if($x_pr < 20)$x_pr=20;
$x='function(width){
screen_width=document.compatMode==\'CSS1Compat\' && !window.opera?document.documentElement.clientWidth:document.body.clientWidth;
wPX=Math.ceil((width*screen_width)/100);
return wPX;
}('.$x_pr.')';
}
if(strpos($y, '%') !==false){
$y_pr=str_replace ("%", "", $y);
if($y_pr > 100)$y_pr=100;
if($y_pr < 20)$y_pr=20;
$y='function(height){
if(navigator.userAgent.toLowerCase().indexOf(\'opera\') > -1){screen_height=document.documentElement.clientHeight}
else { screen_height=document.compatMode==\'CSS1Compat\' && !window.opera?document.documentElement.clientHeight:document.body.clientHeight;}
hPX=Math.ceil((height*screen_height)/100);
return hPX;
}('.$y_pr.')';
}
WPage::addJSLibrary('joobibox');
if(!empty($title))$title=' title="'.WGlobals::filter($title, 'string'). '"';
if(!empty($className))$className=' class="'.$className.'"';
if(!empty($idName))$idName=' id="'.$idName. '"';
$relPop=($justNormalLink )?'' : ' rev="joobibox" rel="{handler: \'iframe\',size:{x:'.$x.',y:'.$y.'}}"';
return '<a href="'.$url.'"'.$relPop . $title. $idName . $className . $extras.'>'.$text.'</a>';
}
public static function includeMootools(){
}
public static function includejQuery(){
static $includejQuery=false;
if(!$includejQuery){
WPage::addScript( JOOBI_SITE_PATH . JOOBI_FOLDER.'/node/api/addon/'.JOOBI_FRAMEWORK.'/js/jquery-1.11.1.js','none');
$includejQuery=true;
}}
public static function includeRootScript(){
static $includeRootscript=false;
if(!$includeRootscript){
self::includejQuery();
$rootscript=JOOBI_SITE_PATH . JOOBI_FOLDER.'/node/api/addon/'.JOOBI_FRAMEWORK.'/js/rootscript.1.2.js';
WPage::addScript($rootscript, 'none');
$includeRootscript=true;
}}
public static function includeJoobiBox(){
}
function setToolTips(){
}
function interpretURL($segments){
$vars=array();
$vars['controller']=str_replace(':','-',$segments[0]);
$i=1;
while (isset($segments[$i])){
WPage::parseURL($segments[$i], $vars );
$i++;
}
return $vars;
}
function buildURL(&$query){
}
function parseURL($string,&$vars){
}
public static function linkNoSEF($url='',$type='standard'){
$url=trim($url);
return $url;
}
public static function formURL($option='',$controller=''){
if(!empty($controller))$controller='controller='.$controller;
return JOOBI_SITE . JOOBI_INDEX . $controller;
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
return 'en-GB';
}
public static function cmsUserLang($short=false){
return 'en';
}
public static function cmsAvailLang($path=''){
return array('en'=>'en');
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
public static $cmsName='framework';
public static $ID=1;
public static function getFrameworkName(){
return self::$cmsName;
}
public static function name($short='default',$wPageID=null,$linkController=null){
return '';
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
if(!empty($availableLanguageA )){
$foundLanguage=false;
foreach($langCode as $oneLGCode){
foreach($availableLanguageA as $availLang){
if($availLang->code==$oneLGCode){
$foundLanguage=true;
$lang=$availLang;
break;
}}if($foundLanguage ) break;
}}}
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
class WApplication_framework {
public $cmsName='joobi';
private function _loadFrameWork(){
require_once( JOOBI_DS_CONFIG . JOOBI_FRAMEWORK_CONFIG.'.php');
$configC=new WFramework_Load_Config;
$configC->loadConfig();
}
public static function getFrameworkName(){
return self::$cmsName;
}
function make($entrypoint=null,$params=null){
static $joobiConf=true;
$processApplication=true;
if($joobiConf){
$joobiConf=false;
$this->_loadFrameWork();
require_once( JOOBI_LIB_CORE.'define.php');
require_once( JOOBI_DS_NODE.'api'.DS.'addon'.DS.JOOBI_FRAMEWORK.DS.'api.php');
define('JOOBI_CHARSET','UTF-8');
$config=WGet::loadConfig();
define('JOOBI_SITE_TOKEN' , $config->secret );
WGet::loadLibrary();
$UserSessionInfo=WGlobals::getSession('JoobiUser');
if(empty($UserSessionInfo)){
$tools=WUser::session();
$tools->setGuest();
}
}
WGlobals::set('resetForm','yes','global');
$extType='application';
$namekey='';
if($processApplication)$content=WGet::startApplication($extType, $namekey, $params );
else {
$content='';
}
$ecoContent=ob_get_contents();
if(!empty($ecoContent)) WMessage::log($ecoContent, 'error-buffer-ouput');
ob_end_clean();
$debug=JOOBI_DEBUGCMS || WPref::load('PLIBRARY_NODE_DBGERR');
WLoadFile('api.addon.framework.page');
$page=new Api_Framework_Page_addon;
return $page->createPage($content, APIPage::$headerA, $debug );
return $content;
}
}
