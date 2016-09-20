<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class WTheme extends WElement {
public static $isCloned=true;
var $headerMenu='';var $bottomMenu='';var $wizard='';var $tabs='';var $application='';var $information='';
public $folder='joobi'; public $type=0;
public $wid=0;
public $file='default.php'; 
protected $tmid=0;
var $basePath='';
var $lineend=null;
var $charset='UTF-8';
var $_data=null;
protected $themeId='';
protected $_parameters=null;
function __construct($params=null){
parent::__construct( null, $params );
if(isset($this->lineend )){
$this->setLineEnd($this->lineend );
}if(isset($this->charset )){
$this->setCharset($this->charset );
}
}
public static function get($themeID,$return=null){
static $newInstance=null;
if(empty($themeID)) return false;
$newInstance=WTheme::getInstance($themeID );
$newInstance->tmid=$themeID;
$themeO=$newInstance->load();
if( is_string($return) && isset($themeO->$return )){
return $themeO->$return;
}else{
$themeO;
}
}
public static function &getInstance($themeID,$docType='',$params=null){
static $instances=array();
if(empty($docType))$docType=WGlobals::get('format','html','','string');
$key=$themeID.'-'.$docType;
if(empty($instances[$key])){
if(empty($params)){
$params=new stdClass;
$params->charset='UTF-8';
$params->lineend='unix';
$params->tab='  ';
$params->direction=APIPage::isRTL()?'rtl' : 'ltr';
}
$class='WTheme'.strtoupper($docType );
if(!class_exists($class))$class='WThemeHTML';
$instances[$key]=new $class($params);
if( is_numeric($themeID))$instances[$key]->tmid=$themeID;
$instances[$key]->themeId=$key;
}
return $instances[$key];
}
function setContent($area,$content,$title='',$force=false){
$this->_myAreas('set',$area, $content, $force );
if(!empty($title))$this->_myAreas('setTitle',$area, $title );
}
function setTitle($area,$title=''){
$this->_myAreas('setTitle',$area, $title );
}
function getContent($area){
return $this->_myAreas('get',$area );
}
function getTitle($area){
return $this->_myAreas('getTitle',$area );
}
function setData($data,$property='data'){
$propertyUsed='_'.$property;
$this->$propertyUsed=$data;
}
function &container($name='',$areas=null){
static $instance=array();
if(isset($areas))$instance=$areas;
if(!empty($name)) return $instance[$name];
else return $instance;
}
public function setParameters($params){
if(empty($this->_parameters)){
$this->_parameters=new stdClass;
$this->_parameters=$params;
}elseif(!empty($params )){
foreach($params as $key=> $val){
$this->_parameters->$key=$val;
}}}
function message(){
$message=WMessage::get();
return $message->getM();
}
function &loadRenderer($type){
$class='WThemeRenderer_'. ucfirst($type );
$instance=new $class($this);
return $instance;
}
function load(){
if(empty($this->tmid)) return false;
$themeO=$this->_getSQL($this->tmid );
if(empty($themeO)) return false;
foreach($themeO as $key=> $oneValue){
$this->$key=$oneValue;
}
return $themeO;
}
function display(){
if(isset($this->content )) return $this->content; else return '';
}
protected function _myAreas($action,$area='',$contentValue='',$force=false){
static $myAreas=array();
static $myTitles=array();
switch($action){
case'get':
if(isset($myAreas[$this->themeId][$area])){
return $myAreas[$this->themeId][$area];
}else{
if(isset($myAreas[$this->themeId])){
return WView::retreiveOneValue($myAreas[$this->themeId], $area );
}else{
return '';
}}break;
case'set':
if($force || !isset($myAreas[$this->themeId][$area]))$myAreas[$this->themeId][$area]=$contentValue;
else {
if(!$force && $contentValue !=$myAreas[$this->themeId][$area]){
$CONTENT=$area;
$message=WMessage::get();
$message->codeE('The content you are trying to set '.$CONTENT.' is already set. Make sure you dont set twice the same content.');
}}break;
case'setTitle':if(!isset($myTitles[$this->themeId][$area]))$myTitles[$this->themeId][$area]=$contentValue;
break;
case'getTitle':
if(isset($myTitles[$this->themeId][$area])){
return $myTitles[$this->themeId][$area];
}else{
if(isset($myTitles[$this->themeId])){
return WView::retreiveOneValue($myTitles[$this->themeId], $area );
}else{
return '';
}}break;
case'getAllTheme':
return (isset($myAreas[$this->themeId])?$myAreas[$this->themeId]: null );
break;
case'reset':
default:
$myAreas=array();
$myTitles=array();
break;
}
return ;
}
protected function _getSQL($tmid){
static $staticCachedA=array();
if(empty($tmid)) return false;
$caching=WPref::load('PLIBRARY_NODE_CACHING');
$key=$tmid;
if($caching > 5){
$cache=WCache::get();
$cachedTheme=$cache->get($key, 'Theme');
}else{
$cachedTheme=(!empty($staticCachedA[$key]))?$staticCachedA[$key] : '';
}
if(empty($cachedTheme )){
$themeM=WModel::get('theme');
if( is_numeric($tmid)){
$themeM->whereE('tmid',$tmid );
}else{
$themeM->whereE('namekey',$tmid );
}$cachedTheme=$themeM->load('o');
if(!empty($cachedTheme)){
if($caching > 5)$cache->set($key, $cachedTheme, 'Theme');
else $staticCachedA[$key]=$cachedTheme;
}
}
return $cachedTheme;
}
}
class WRenderer {
var$_page=null;
var $_mime="text/html";
function __construct($page){
$this->_page=$page;
}
function getContentType(){
return $this->_mime;
}
}
class WThemeRenderer_Container {
function render($name=null,$params=array(),$content=null){
$area=&WTheme::container($name );
return $area;
}
}
class WThemeRenderer_Function {
function render($name=null,$params=array(),$content=null){
if( method_exists($this->_page, $name )){
return $this->_page->$name;
}}
}
class WThemeHTML extends WTheme {
public static $themeParams=null;
var $folder='';
public function setFolder($folder){
if(!empty($folder))$this->folder=$folder;
}
public function getFolder($type,$wid=0){
$folder=$this->_getSQLInfo($type, $wid );
if(empty($folder))$folder=$this->_getSQLInfo($type, $wid, true);
return $folder;
}
public function display($extraContent=array(),$extraTitle=null,$tagIteration=1){
if(empty($this->type)){
return '';
}
$contents='';
$themeHTMLPath=$this->_getThemePath();
if($themeHTMLPath===false) return '';
if(!empty($extraContent)){
foreach($extraContent as $kTag=> $vTag){
$strangeTitle=(isset($extraTitle[$kTag] ))?$extraTitle[$kTag] : null;
$this->setContent($kTag, $vTag, $strangeTitle );
}}
if(!empty($this->htmlfile)){
$systemFile=WGet::file();
if('joomla'==JOOBI_FRAMEWORK_TYPE){
$hasOverwrite=WView::themeIsOverWritten();
if($hasOverwrite){
if( substr($themeHTMLPath, 0, strlen( JOOBI_DS_THEME_JOOBI ))==JOOBI_DS_THEME_JOOBI){
$overwrittenPath=str_replace( JOOBI_DS_THEME_JOOBI, WPage::getTemplate('path').DS.'joobi'.DS, $themeHTMLPath );
if($systemFile->exist($overwrittenPath . $this->file )){
$themeHTMLPath=$overwrittenPath;
}}}
}
if($systemFile->exist($themeHTMLPath . $this->file )){
ob_start();
include($themeHTMLPath . $this->file );
$contents=ob_get_clean();
$CMSaddon=WAddon::get('api.'.JOOBI_FRAMEWORK.'.content');
if(!empty($CMSaddon))$contents=$CMSaddon->replaceThemeTag($contents );
}else{
$themeHTMLPath=$this->_getThemePath(true);
if($systemFile->exist($themeHTMLPath . $this->file )){
ob_start();
include($themeHTMLPath . $this->file );
$contents=ob_get_clean();
}else{
$message=WMessage::get();
$FILE=$themeHTMLPath . $this->file;
$FILE=str_replace( JOOBI_DS_JOOBI, '',$FILE );
$message->codeE('The theme file you are trying to load does not exist, file path: '.$FILE );
return false;
}
}}
$tagsToPRocess=$this->_myAreas('getAllTheme');
if(!empty($tagsToPRocess) || !empty($this->_parameters)){
$tagClass=WClass::get('output.process');
$tagClass->addParameter('container',$extraContent );
if(!empty($this->_parameters))$tagClass->setParameters($this->_parameters );
$tagClass->replaceTags($contents, null, $tagIteration );
$this->_myAreas('reset');
}
return $contents;
}
function getValue($columnName,$modelName=null){
if(isset($this->_data->$columnName )) return $this->_data->$columnName;
return WView::retreiveOneValue($this->_data, $columnName, $modelName );
}
function getRowContent($columnName,$modelName=null){
if(empty($this->_rowContent)) return false;
if(isset($this->_rowContent->$columnName )) return $this->_rowContent->$columnName;
return WView::retreiveOneValue($this->_rowContent, $columnName, $modelName );
}
function makeSafe($content){
return htmlspecialchars($content, ENT_COMPAT, $this->charset );
}
private function _getThemePath($core=false){
if(empty($this->basePath ))$this->basePath=JOOBI_DS_THEME;
$subFolderName='view';
$loadSQLINfo=false;
$mainFolder='';
switch($this->type){
case 1:case 3:if(empty($this->folder))$this->folder=WView::getDefaultTheme();
$themeHTMLPath=$this->basePath.'site'.DS.$this->folder . DS;
$this->htmlfile=true;
break;
case 50:if(empty($this->folder))$this->folder=WView::getDefaultTheme();
$themeHTMLPath=$this->basePath.'mobile'.DS.$this->folder . DS;
$this->htmlfile=true;
break;
case 2:if(empty($this->folder))$this->folder=WView::getDefaultTheme(); $themeHTMLPath=$this->basePath.'admin'.DS.$this->folder . DS;
$this->htmlfile=true;
break;
case 106:if(empty($mainFolder))$mainFolder='mail';
$subFolderName='';
$loadSQLINfo=true;
break;
case 107:if(empty($mainFolder))$mainFolder='coupon';
$subFolderName='';
$this->htmlfile=true;
$loadSQLINfo=true;
break;
case 108:if(empty($mainFolder))$mainFolder='order';
$subFolderName='';
$this->htmlfile=true;
$loadSQLINfo=true;
break;
case 105:case 49:$loadSQLINfo=true;
if(empty($mainFolder))$mainFolder='node';
$this->basePath=JOOBI_DS_THEME_JOOBI;
break;
default:
return false;
break;
}
if($loadSQLINfo){
$this->folder=$this->_getSQLInfo($this->type, $this->wid, $core );
if(empty($this->folder)){
$message=WMessage::get();
$EXTENSION=WExtension::get($this->wid, 'namekey');
$message->codeE('The theme location was either not found or not specified properly for the following extension: '.$EXTENSION );
return '';
}
if(empty($mainFolder))$mainFolder='node';
$themeHTMLPath=$this->basePath . $mainFolder.DS.$this->folder .DS;
if(!empty($subFolderName))$themeHTMLPath .=$subFolderName .DS;
}return $themeHTMLPath;
}
private function _getSQLInfo($type=0,$wid=0,$core=false){
static $staticCachedA=array();
if($type==49 ) return WExtension::get($wid, 'folder');
$caching=WPref::load('PLIBRARY_NODE_CACHING');
$key=$wid.'-'.$type;
if($caching > 5){
$cache=WCache::get();
$cachedThemeO=$cache->get($key, 'Theme');
}else{
$cachedThemeO=((!empty($staticCachedA[$key]) && !$core )?$staticCachedA[$key] : '');
}
if(empty($cachedThemeO ) || $core){
$themeM=WModel::get('theme');
$themeM->whereE('type',$type );
if(!empty($wid))$themeM->whereE('wid',$wid );
$themeM->openBracket();
$themeM->whereE('framework', 0 );
$themeM->operator('OR');
$themeM->whereE('framework', JOOBI_FRAMEWORK_TYPE_ID );
$themeM->closeBracket();
$themeM->orderBy('premium','DESC');
$themeM->orderBy('core','DESC');
if($core)$themeM->whereE('core', 1 );
$cachedThemeO=$themeM->load('o',array('folder','core','params'));
if(!empty($cachedThemeO)){
if($caching > 5)$cache->set($key, $cachedThemeO, 'Theme');
else $staticCachedA[$key]=$cachedThemeO;
}
}
if(!empty($cachedThemeO->params)) self::$themeParams=$cachedThemeO->params;
self::$isCloned=(empty($cachedThemeO->core)?true : false);
if(!empty($cachedThemeO->folder)) return $cachedThemeO->folder;
else {
return WPage::cmsDefaultTheme();
}
}
function setLineEnd($style){
switch ($style){
case 'win':
$this->_lineEnd="\15\12";break;
case 'unix':
$this->_lineEnd="\12";break;
case 'mac':
$this->_lineEnd="\15";break;
default:
$this->_lineEnd=$style;
}}
function setCharset($type='UTF-8'){
$this->_charset=$type;
}
}
class WThemePDF extends WThemeHTML {
public function setPDFLocation($path=''){
if(empty($path)){
}
}
public function display($extraContent=array(),$extraTitle=null,$tagIteration=1){
parent::display();
$xist=WExtension::exist('tcpdf.includes');
if(empty($xist)){
$message=WMessage::get();
$message->userE('1359430178PKKA');
return false;
}
}
}