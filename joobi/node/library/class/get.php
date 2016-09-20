<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
define('JOOBI_READY', true);
define('JOOBI_VAR_DATA','trucs');define('JOOBI_SPOOF','zjg1');
define('JOOBI_JS_APP_NAME','joobi');
abstract class WGet {
public static $rLine='';public static $ready=false;
public static function folder($storageType='hdd',$showMessage=true){
$myClass=WClass::get('library.folder',$storageType, 'class', false);
if(empty($myClass)) return false;
return $myClass;
}
public static function file($storageType='hdd'){
$myClass=WClass::get('library.file',$storageType, 'class', false);
if(empty($myClass)) return false;
return $myClass;
}
public static function session($expiration=0,$onlyIfNotStarted=false){
if(empty($expiration))$expiration=JOOBI_SESSION_LIFETIME;
if(empty($expiration))$expiration=15;
$sessionPreferences=new stdClass;
$sessionPreferences->lifetime=$expiration;
$sessionPreferences->onlyIfNotStarted=$onlyIfNotStarted;
WLoadFile('library.class.session');
$session=Library_Session_class::getInstance('default',$sessionPreferences );
return $session;
}
private static function _filterGet(){
static $done=false;
if($done ) return;
$done=true;
$get=WGlobals::getEntireSuperGlobal('get');
if(!empty($get )){
foreach($get as $k=> $v){
WGlobals::set($k, WGlobals::filter($v, 'string'), 'get', true);
}}
}
public static function startApplication($extType,$namekey='',$params=null){
static $javascript=true;
self::$ready=true;
WText::load('library.node');
WText::load('users.node');
$URLlang=WGlobals::get('lang','','get','string');
if(empty($URLlang)){
$URLlang=WGlobals::get('language','','','string');
}
if(!empty($URLlang)){
$lang=WGlobals::getSession('JoobiUser','lang','');
if(empty($lang) || $lang !=$URLlang){
$lgid=WLanguage::get($URLlang, 'lgid', true);
$URLlang=WLanguage::get($lgid, 'code');
WUser::set('lgid',$lgid );
WUser::set('lang',$URLlang );
}
}
WGlobals::set('appType',$extType, 'global');
switch($extType){
case 'application': $controller=WGlobals::get('controller','', null, 'string');
self::_filterGet();
WView::form( null, true);
if(empty($controller) && !empty($namekey)){
if( WRoles::isAdmin('storemanager')){
$e=explode('.',$namekey );
$controller=array_shift($e);
}else{
$prefHandler=WPref::get($namekey );
$name=$prefHandler->isLoaded('defaulturl');
if($name){
$value=constant($name );
if(!empty($value)){
WPages::redirect($value );
}}$controller=WApplication::getApp();
}}
if(empty($controller)){
WMessage::log('controller not provided.','controller-issue');
return '';
}
$explodeControllerA=explode('-',$controller );
$extID=WExtension::get($explodeControllerA[0].'.node','wid');
if(empty($extID)){
$extID=WExtension::get($explodeControllerA[0].'.application','wid');
}
if(empty($extID)){
WMessage::log('extension ID not provided, could not get from controller : '.$controller , 'controller-issue');
WMessage::log($explodeControllerA , 'controller-issue');
WGlobals::set('errorCode','EXTENSION_NOT_FOUND','global');
return '';
}
if(empty($namekey)){
$libraryInfoC=WClass::get('library.info');
$namekey=$libraryInfoC->getNamekeyFromNode($extID );
}
if($javascript){
WPage::addJSLibrary('rootscript');
$javascript=false;
}
if(empty($extID)){echo '<br />This page does not exist!<br />';
echo '<br />The controller is controller='.$controller;
echo '<br />The resulting application is '.$explodeControllerA[0];
WMessage::log('The extension was not found for '.$explodeControllerA[0].'.node','error-controller');
WMessage::log('The controller was '.$controller , 'error-controller');
return '';
}
$namekey=WExtension::get($extID, 'namekey');
WGlobals::set('extensionID',$extID, 'global', true);
WGlobals::set('extensionKEY',$namekey, 'global', true);
$myParams=null;
$optionL=WApplication::name('short');
if($optionL==JOOBI_MAIN_APP){
$appsUserInfoM2=WModel::get('apps.userinfos');
$appsUserInfoM2->whereE('enabled', 1 );
$appsUserInfoM2->where('license','!=','');
$appsUserInfoM2->orderBy('level','desc');
$newWID=$appsUserInfoM2->load('lr','wid');
if(!empty($newWID ))$optionL=WExtension::get($newWID, 'folder', null, null, false);
if(empty($optionL))$optionL=JOOBI_MAIN_APP;
}
$appLevel=WExtension::get($optionL.'.application','data');
if(!empty($appLevel))$appLevel->application=$optionL;
WExtension::checkAuthorizedLevel($appLevel, true);
break;
case'module': 
self::_filterGet();
$myParams=$params;
$moduleData=WExtension::get($namekey, 'data');
if(!isset($moduleData->level) || $moduleData->level < 1){
if(isset($moduleData->qaz)){
$parentApplication=WExtension::get($moduleData->qaz, 'data');
if($parentApplication->type==1){
$moduleData->level=WExtension::checkAuthorizedLevel($parentApplication, false);
WGlobals::setCandy($moduleData->level, $moduleData->wid );
}}}else{
WGlobals::setCandy($moduleData->level, $moduleData->wid );
}
$extID=(!empty($moduleData->wid)?$moduleData->wid : 0 );
WGlobals::set('extensionKEY',$namekey, 'global', true);
WGlobals::set('extensionID',$extID, 'global', true);
if($javascript){
WPage::addJSLibrary('rootscript');
$javascript=false;
if(!IS_ADMIN){
if(!defined('JOOBI_URL_THEME_JOOBI')) WView::definePath();
WPage::addCSSFile('css/style.css');
}
}
break;
case'plugin': $myParams=null;
break;
case'install':
$installer=WClass::get('install.package');
if(isset($_SESSION['joobi']['online'])){
$auto=$_SESSION['joobi']['online'];
$installer->auto=$auto;
}
return $installer->installPackagesFromSession();
break;
default:
WGlobals::setCandy( 100 );return '';
break;
}
$content=WExtension::get($namekey, 'application', null, $myParams );
if( JOOBI_CHARSET !='UTF-8'){
$content=WPage::changeEncoding($content, 'UTF-8', JOOBI_CHARSET );
}
if('plugin' !=$extType &&
(
JOOBI_DEBUGCMS
|| ( defined('PLIBRARY_NODE_PLEX') && PLIBRARY_NODE_PLEX )
)
){
if(!WGlobals::get('wajx')){
$html="\n\r<!-- Start Joobi App: " . $extType;
if(!empty($namekey ))$html .=' , namekey: '.$namekey;
$html .=" -->\n\r";
$html .=$content;
$html .="\n\r<!-- End Joobi App -->\n\r";
}else{
$html=$content;
}
$content=$html;
}
return $content;
}
public static function loadConfig($name=null,$default=null){
static $config=null;
if(!isset($config)){
if(!defined('JOOBI_DS_CONFIG')) define('JOOBI_DS_CONFIG', JOOBI_DS_JOOBI . DS );
WLoadFile('config' , JOOBI_DS_CONFIG );
if( class_exists('Joobi_Config'))$config=new Joobi_Config;
else {
echo 'Joobi Configuratin file could not be loaded...';
exit;
}}
if(!empty($name)){
return (isset($config->$name)?$config->$name : $default );
}else{
return $config;
}
}
public static function loadLibrary(){
static $once=true;
if($once){
$once=false;
WPref::get('library.node');
if( JOOBI_DEBUGCMS
|| ( WPref::load('PLIBRARY_NODE_DBGERR'))
  || ( WPref::load('PLIBRARY_NODE_DBGERRGUEST') && WUser::get('uid')==0 )
 ){
@ini_set('display_errors', true);
@ini_set('display_startup_errors' , true);
}else{
@ini_set('display_errors', false);
@ini_set('display_startup_errors', false);
}
WTranslation::load( null, 2 );
if( WGet::isDebug() && ! WGlobals::get('wajx')) WGet::$rLine="\r\n";
}
$phperror=WPref::load('PLIBRARY_NODE_PHPERROR');
if($phperror==1 ) error_reporting( E_ALL );
else error_reporting( 0 );
}
public static function DBType(){
if(!defined('PLIBRARY_NODE_DBTYPE')){
return JOOBI_DB_TYPE;
}else{
$dbtype=WPref::load('PLIBRARY_NODE_DBTYPE');
if(empty($dbtype )){
return JOOBI_DB_TYPE;
}else{
if( in_array( JOOBI_FRAMEWORK_TYPE, array('mobile','netcom')) && 'framework'==$dbtype ) return JOOBI_DB_TYPE;
else return $dbtype;
}
}
}
public static function isDebug(){
if( WUsers::get('uid')){
return ( JOOBI_DEBUGCMS || WPref::load('PLIBRARY_NODE_DBGERR'));
}else{
return ( JOOBI_DEBUGCMS || WPref::load('PLIBRARY_NODE_DBGERRGUEST'));}
}
}
class WLanguage {
public static function get($codelgid,$return='data',$checkAvailability=false){
if(empty($codelgid)){
static $premiumLangange=null;
if(!isset($premiumLangange)){
$languageM=WTable::get('language_node','main_userdata','lgid');
if(!$languageM->tableExist() || ! $languageM->isReady())$languageM=WTable::get('joobi_languages','main_userdata','lgid');
$languageM->whereE('premium', 1 );
$languageM->orderBy('lgid','ASC');
$codelgid=$languageM->load('lr','lgid');
if(empty($codelgid))$codelgid=1;
$premiumLangange=$codelgid;
}else{
$codelgid=$premiumLangange;
}
}
$caching=WPref::load('PLIBRARY_NODE_CACHING');
$caching=($caching > 0 )?'cache' : 'static';
$tempdata=WCache::getObject($codelgid, 'Language',$caching, true);
if(empty($tempdata))$tempdata=WCache::getObject($codelgid, 'Language','static', true);
if(empty($tempdata)){
if( is_string($codelgid)){
$lter2=substr($codelgid, 0, 2 );
$tempdata=WCache::getObject($lter2, 'Language',$caching, true);
if(empty($tempdata))$tempdata=WCache::getObject($lter2, 'Language','static', true);
if(empty($tempdata)) return null;
}else{
return null;
}}
$keyList=array('lgid','code','name','main','real','premium','localeconv','locale','publish');
if($checkAvailability && is_string($codelgid)){
$jomLGID=$tempdata->lgid;
$availableLanguageA=WApplication::availLanguages('lgid');
if(!in_array($jomLGID, $availableLanguageA )){
$langSplitA=explode('-',$codelgid );
$jomLGID=WLanguage::get($langSplitA[0], 'lgid', false);
if(empty($jomLGID) || !in_array($jomLGID, $availableLanguageA )){
$location=( IS_ADMIN?'admin' : 'site');
$langClient=WApplication::mainLanguage('lgid', false, array(), $location );
return WLanguage::get($langClient, $return, false);
}
return WLanguage::get($jomLGID, $return, false);
}
}
if( in_array($return, $keyList )){
if(isset($tempdata->$return )){
return $tempdata->$return;
}else{
return null;
}}
if(is_array($return) && !empty($return)){
$obj=new stdClass;
foreach($return as $Prop){
$obj->$Prop=$tempdata->$Prop;
}return $obj;
}
if('code4'==$return){
if( strlen($tempdata->code) < 3){
return $tempdata->code.'-'.strtoupper($tempdata->code );
}}
return $tempdata;
}
public function getSQL($lgid,$showMessage=true){
$languageM=WTable::get('language_node','main_userdata','lgid');
if(!$languageM->tableExist() || ! $languageM->isReady())$languageM=WTable::get('joobi_languages','main_userdata','lgid');
if(!$languageM->isReady()){
$tempdata=new stdClass;
$tempdata->id=1;
$tempdata->namekey='en';
return $tempdata;
}
if(!is_object($languageM)){
$tmp=null;
return $tmp;
}
if( is_numeric($lgid) || is_bool($lgid)){
$languageM->whereE('lgid',$lgid );
}else{
$languageM->whereE('code',$lgid );
}$keyList=array('lgid','code','name','main','real','premium','localeconv','locale','publish');
$tempdata=$languageM->load('o',$keyList );
if(empty($tempdata)){
$tmp=null;
return $tmp;
}
$tempdata->id=$tempdata->lgid;
$tempdata->namekey=$tempdata->code;
return $tempdata;
}
}
class WExtension {
public static function exist($id){
$exist=WExtension::get($id, 'wid', null, null, false);
return (!empty($exist))?true : false;
}
public static function get($path='',$property='wid',$type=null,$params=null,$showMessage=false){
static $extensionID=0;
static $transloaded=array();
static $prefLoaded=array();
if('trans'==$property && 'netcom'==JOOBI_FRAMEWORK_TYPE ) return 0;
if( is_string($path))$path=trim($path );
else {
if(!is_numeric($path)){
return false;
}}
if(empty($path)){
if($extensionID !=0){
$path=$extensionID;
}else{
$path=WGlobals::get('extensionID', 0, 'global','int');
if(empty($path))$path=WGlobals::get('extensionKEY','','global','string');
$extensionID=$path;
}
if(empty($path)){
if( WPref::load('PLIBRARY_NODE_DBGERR'))$showMessage=true;
$controller=WGlobals::get('controller','', null, 'string');
$pos=strpos($controller, '.');
if($pos !==false){
$controller=substr($controller, 0, $pos );
}$path=$controller;
}
}
$caching=( defined('PLIBRARY_NODE_CACHING')?PLIBRARY_NODE_CACHING : 1 );
$caching=($caching > 0 )?'cache' : 'static';
$tempdata=WCache::getObject($path, 'Extension',$caching, true, false, '', null, $showMessage );
if(!empty($tempdata->framework) && $tempdata->framework !=JOOBI_FRAMEWORK_TYPE_ID ) return false;
if(empty($tempdata)) return $tempdata;
$namekey=$tempdata->namekey;
$wid=$tempdata->wid;
if(!isset($transloaded[$wid])){
$transloaded[$wid]=true;
if($tempdata->trans ) WText::load($wid );
}
if(!isset($prefLoaded[$wid])){
$prefLoaded[$wid]=true;
if($tempdata->pref ) WPref::get($wid );
}
if(empty($property) || $property=='data'){return $tempdata;
}elseif($property=='application'){
if($tempdata->type==150){
$tempdata->level=0;
}
$namekeyArray=explode('.' , $namekey );
$extensionID=$tempdata->wid;
WGlobals::set('extensionID',$extensionID, 'global');
switch($tempdata->type){
case'150':case'1':case'2':case'78':
$path=WGlobals::get('controller');
if(empty($path)){
$path=WGlobals::get('app');
if(empty($path)){
$path=WApplication::getApp();
}
}
$controllerC=WController::get($path );
if(!empty($controllerC->trigger) && $controllerC->trigger >=10){
$triggerC=WClass::get('library.trigger');
$obj2=null;
if(!isset($controllerC->_model))$controllerC->_model=new stdClass;
$controllerC->_model->ctrid=$controllerC->ctrid;
$triggerC->actions($controllerC->_model , $obj2, null, true);
}
if(empty($controllerC)){
$controllerC=new WController();
return $controllerC->display();
}
$status=$controllerC->make();
if(!empty($controllerC->trigger) && $controllerC->trigger >=10){
$triggerC=WClass::get('library.trigger');
$obj2=null;
$controllerC->_model->ctrid=$controllerC->ctrid;
$triggerC->actions($controllerC->_model, $obj2, $status );
}
return $status;
break;
case'25':$modLayout=WExtension::module($path, $params );
if($modLayout){
$modLayout->wid=$wid;
$HTML=$modLayout->make();
return $HTML;
}break;
case'50':WExtension::plugin($path );
break;
default:$message=WMessage::get();
$message->codeE('Extension Type not well defined for '.$path ,array(),'wget');
return '';
break;
}
}elseif(is_array($property)){$newObj=new stdClass;
foreach($property as $oneProp){
if(isset($tempdata->$oneProp))$newObj->$oneProp=$tempdata->$oneProp;
}return $newObj;
}else{return (isset($tempdata->$property)?$tempdata->$property : null );
}
}
public static function module($path,&$params){
$name=explode('.',$path );
if(!defined('JOOBI_DS_THEME_JOOBI')) WView::definePath();
WLoadFile($name[0].'.module.'.$name[1].'.'.$name[1], JOOBI_DS_NODE  );
$exists=WLoadFile('node.'.$name[0].'.module.'.$name[1].'.'.$name[1], JOOBI_DS_THEME_JOOBI, true, false);
if(!$exists){
$className=ucfirst($name[0] ). '_Core'.ucfirst($name[1]). '_module';
}else{
$className=ucfirst($name[0] ). '_'.ucfirst($name[1]). '_module';
}
if( class_exists($className )){
$newClass=new $className($params );
WGlobals::set('wz_page_tile', false, 'global');
}else{
$message=WMessage::get();
$message->codeE('Class not found in the module file!  Name:'.$className );
$tmp=null;
return $tmp;
}
return $newClass;
}
public static function plugin($path,$params=null){
$name=explode('.',$path );
array_pop($name);
$exists=WLoadFile($name[0].'.plugin.'.$name[1].'.'.$name[1] , JOOBI_DS_NODE  );
$className=ucfirst($name[0] ). '_'.ucfirst($name[1]).'_plugin';
if($exists && class_exists($className )){
$instance=new $className($path);
}else{
$message=WMessage::get();
$message->codeE('Class not found in the plugin file!  Name:'.$className );
$tmp=null;
return $tmp;
}
return $instance;
}
public static function checkAuthorizedLevel($extensionO,$mainAppli=true){
static $authorized=array();
static $all=false;
if(empty($extensionO)) return '0';
if(isset($authorized[$extensionO->wid] )){
return $authorized[$extensionO->wid];
}
$namekey=$extensionO->namekey;
if(isset($extensionO->application)){
$optionL=$extensionO->application;
}else{
$locPos=strpos($extensionO->namekey, '.');
$optionL=substr($extensionO->namekey, 0, $locPos );
}
$namekeycst=strtoupper( str_replace('.','_',$namekey));
if($all){
$authorized[$extensionO->wid]=$all;
$goodLevel=$all;
}else{
$goodLevel=0;
$appsInfoC=WCLass::get('apps.info');
$data=$appsInfoC->getPossibleCode('onlyAll');
if(!empty($data)){
$subtype=(isset($data->subtype)?$data->subtype : 0 );
WGlobals::setSugar(($subtype !=21 && $subtype !=31?true : false));
$goodLevel=(isset($data->level)?$data->level : 50 );
$all=$goodLevel;
}
if(!$all){
$appsInfoC=WCLass::get('apps.info');
$data=$appsInfoC->getPossibleCode($extensionO->wid );
if(!empty($data)){
$_SESSION['extInfo'][$optionL]['licence']='working';
$goodLevel=(isset($data->level)?$data->level : 50 );
$subtype=(isset($data->subtype)?$data->subtype : 0 );
WGlobals::setSugar(($subtype !=21 && $subtype !=31?true : false));
}
if(empty($goodLevel)){
if($optionL=='js'.'to'.'re'){
WGlobals::setSugar(true);
$goodLevel=50;
}else{
$goodLevel=0;
$_SESSION['extInfo'][$optionL]['licence']='working';
}
}
}
}
if('wordpress' !=JOOBI_FRAMEWORK_TYPE && empty($goodLevel)){
$goodLevel=50;
}
WGlobals::setCandy($goodLevel, $extensionO->wid );
if($mainAppli){
WGlobals::setCandy($goodLevel );
}
$authorized[$extensionO->wid]=$goodLevel;
return $goodLevel;
}
public function getSQL($path,$showMessage=true){
if(empty($path)) return false;
if(!is_numeric($path)){
$namekey=$path;
}else{$namekey='';
}
$modelM=WTable::get('extension_node','main_library','wid');if(empty($modelM)){
return false;
}
$modelM->makeLJ('extension_userinfos','main_userdata','wid','wid');
$modelM->select( array('wid','namekey','type','core','name','destination','rolid','trans','pref','params','parent','version','lversion','created','modified','reload','framework'));
$modelM->select( array('level','license','ltype','subtype','token') ,1 );
if( is_numeric($path)){
$modelM->whereE('wid',$path );
}else{
if( strpos($namekey,'.')===false){
$modelM->where('namekey','LIKE',$namekey.'.%');
$modelM->whereIn('type',array(1,2,150));
}else{
$modelM->whereE('namekey',$namekey );
if( JOOBI_MAIN_APP.'.application'==$namekey){
$modelM->makeLJ('extension_info','main_library','wid','wid');
$modelM->select('userversion', 2 );
}}}$modelM->whereE('publish', 1 );
$modelM->whereOn('enabled','=', 1 , 1 );
$tempdata=$modelM->load('o');
if(empty($tempdata)){
if($showMessage){
$message=WMessage::get();
$extenID=( is_numeric($path))?$path : $namekey;
$mess=$path. ' The extension entry does not exists for the following name: '.$extenID .'<br>';
WMessage::log($mess, 'error_messages');
}
$tmp=null;
return $tmp;
}
$namekey=$tempdata->namekey;
$folderA=explode('.',$namekey );
$tempdata->folder=$folderA[0];
if( count($folderA)>2)$tempdata->subfolder=$folderA[1];
if(!empty($tempdata->params)){
WTools::getParams($tempdata, 'params');
}
$tempdata->id=$tempdata->wid;
return $tempdata;
}
public static function includes($path,$params=null){
static $alreadyIncluded=array();
if(isset($alreadyIncluded[$path])) return true;
$alreadyIncluded[$path]=true;
$path=strtolower($path );
$exists=WLoadFile($path.'.api' , JOOBI_DS_INC , true, false);
if(!$exists){
$tmp=null;
return $tmp;
}
$className='Inc_'.ucfirst( str_replace('.','_',$path )). '_include';
if( class_exists($className )){
if(!empty($params)){
$newClass=new $className($params);
} else
$newClass=new $className;
}else{
$message=WMessage::get();
$message->codeE('Include API not found in the class file!  Name:'.$className );
$tmp=null;
return $tmp;
}
return $newClass;
}
}
abstract class WCache {
public static $cacheC=null;
public static function get($time=604800){
if(!isset(self::$cacheC)) self::$cacheC=WClass::get('library.cache',$time );
self::$cacheC->setTime($time );
return self::$cacheC;
}
public static function getObject($id,$className,$cachingType='cache',$mandatoryStatic=false,$stringID=false,$functionName='',$params=null,$showMessage=true,$force=false){
static $libraryCacheC=null;
static $forcedCachingType=array();
if(empty($id) || empty($className)) return false;
if(empty($libraryCacheC))$libraryCacheC=WCache::get();
$key=$id.'-'.$className.'-'.$functionName;
if($force)$forcedCachingType=array(); elseif(isset($forcedCachingType[$key])){
if($forcedCachingType[$key]===false) return '';
$cachingType='static';
}
$returnedData=$libraryCacheC->getdata($id, $className, $cachingType, $mandatoryStatic, $stringID, $functionName, $params, $showMessage );
if(empty($returnedData)){
$returnedData=$libraryCacheC->getdata($id, $className, 'static',$mandatoryStatic, $stringID, $functionName, $params, $showMessage );
if(empty($returnedData))$forcedCachingType[$key]=false;
else $forcedCachingType[$key]=true;
}elseif('xzwNO__NEED__STATICwxz'==$returnedData){
$returnedData='';
}
return $returnedData;
}
}
abstract class WPref {
public static $usedA=array();
public static $prefA=array();
public static $prefOverA=array();
public static function get($id=null,$force=false,$loadConf=true,$userPref=true){
static $instance=null;
static $confType=array();
if(empty($id)) return false;
if( is_numeric($id)){
$wid=WExtension::get($id, 'namekey', null, null, false);
}else{
$wid=$id;
}
if( WGet::$ready){
$translation=WExtension::get($wid, 'trans', null, null, false);
if($translation ) WText::load($wid );
}
if(!isset($confType[$wid]) || $force){
if(!isset($instance))$instance=new myPreferences();
if(!$instance->setup($wid )){
return false;
}
if($loadConf){
$newWay=true;
$prefVAluesA=$instance->loadConfig($userPref, $force, $newWay );
if(!empty($prefVAluesA )){
foreach($prefVAluesA as $pKey=> $pVal){
if($newWay){
$prop=$newWay.'_'.$pKey;
}else{
$prop=$pKey;
}
$constantNameNamekey='P'.strtoupper($prop );
self::$prefA[$constantNameNamekey]=$pVal;
if(!defined($constantNameNamekey )) define($constantNameNamekey, $pVal );
}}
}
$confType[$wid]=true;
}else{
if(!isset($instance))$instance=new myPreferences();
$instance->setup($wid );
}
return $instance;
}
public static function override($prefName,$value=null){
$prefName=strtoupper($prefName);
if(isset($value)){self::$prefOverA[$prefName]=$value;
}}
public static function load($preferenceName){
if(empty($preferenceName) || !is_string($preferenceName)) return null;
$preferenceName=strtoupper($preferenceName);
if( substr($preferenceName, 0, 1 ) !='P')$preferenceName='P'.$preferenceName;
self::$usedA[$preferenceName]=true;
if(isset( self::$prefOverA[$preferenceName] )){
return self::$prefOverA[$preferenceName];
}
if(!isset( self::$prefA[$preferenceName] )){
$cst=substr($preferenceName, 1 ); $explodeMeA=explode('_', strtolower($cst));
if(!isset($explodeMeA[1])){
return null;
}
WPref::get($explodeMeA[0].'.'.$explodeMeA[1], true);
}
if(isset( self::$prefA[$preferenceName] )){
return self::$prefA[$preferenceName];
}else{
return null;
}
}
public static function getOne($preferenceName,$nodeId){
if(empty($preferenceName) || empty($nodeId)) return false;
$nodeName=WExtension::get($nodeId, 'namekey');
$Prefname='P'.strtoupper( str_replace('.','_',$nodeName ). '_'.$preferenceName );
if(!defined($Prefname)) WPref::get($nodeId );
if( defined($Prefname)) return constant($Prefname );
return false;
}
}
abstract class WUser extends APIUser {
public static $ready=false;
private static $_currentUser=null;
public static function &get($return='object',$uid=null,$loadFromUID=true){
static $allMembers=array();static $loadRolesB=true;
if(empty($uid) && !empty(self::$_currentUser->uid))$uid=self::$_currentUser->uid;
if((!isset(self::$_currentUser) && empty($uid)) || $uid=='reset'){$usersSessionC=WClass::get('users.session');
self::$_currentUser=$usersSessionC->checkUserSession();
$uid=(!empty(self::$_currentUser->uid))?self::$_currentUser->uid : 0;
}else{
if(!empty(self::$_currentUser->uid) && $uid==self::$_currentUser->uid){
$UserSessionInfo=WGlobals::getSession('JoobiUser');
if(!empty($UserSessionInfo) && $uid !=$UserSessionInfo->uid){
self::$_currentUser=$UserSessionInfo;
}}}
if(empty($uid)){$usersSessionC=WClass::get('users.session');
$instance=&$usersSessionC->checkUserSession();
}else{if(empty(self::$_currentUser->uid) || self::$_currentUser->uid !=$uid){
if(!isset($allMembers[$uid])){
if($loadFromUID===true)$column='uid';
elseif(!empty($loadFromUID)){
$column=$loadFromUID;
}else{
$column='';
}
$framekworkPrefBE=WPref::load('PUSERS_NODE_FRAMEWORK_BE');
if(empty($framekworkPrefBE))$framekworkPrefBE=WApplication::getFrameworkName();
$framekworkPrefFE=WPref::load('PUSERS_NODE_FRAMEWORK_FE');
if(empty($framekworkPrefFE))$framekworkPrefFE=WApplication::getFrameworkName();
$frameworkUsed=( WRoles::isAdmin('storemanager'))?$framekworkPrefBE : $framekworkPrefFE;
if(empty($frameworkUsed))$frameworkUsed=JOOBI_FRAMEWORK;
$usersAddon=WAddon::get('users.'.$frameworkUsed );
$allMembers[$uid]=$usersAddon->getUser($uid, $column );
}$instance=$allMembers[$uid];
}else{$instance=self::$_currentUser;
}}
if(!empty($instance->uid) && $loadRolesB && JOOBI_FRAMEWORK_TYPE !='mobile'){
$roleC=WRole::get();
if(isset($roleC->newRoleClassUsage)) $instance->rolids=$roleC->getUserRoles($instance->uid, $instance->rolid );
$loadRolesB=false;
}
self::$ready=true;
if($return=='object' || $return=='data') return $instance;
elseif($return=='ip') return $instance->_ip;elseif($return=='rolids'){
$roleC=WRole::get();
$rolids=$roleC->getUserRoles($instance->uid, $instance->rolid );
return $rolids;
}elseif(isset($instance->$return)) return $instance->$return;
elseif(is_array($return)){$data=new stdClass;
foreach($return as $prop){
if(isset($instance->$prop))$data->$prop=$instance->$prop;
}return $data;
}else{
$null=null;
return $null;
}
}
public static function set($property,$value){
if(empty($property)) return false;
self::$_currentUser->$property=$value;
WGlobals::setSession('JoobiUser',$property, $value );
return true;
}
public static function addUser($email,$role,$userObj=null,$importObject=null){
$usersAddC=WClass::get('users.add');
return $usersAddC->addUser($email, $role, $userObj, $importObject );
}
public static function addRole($uid,$roles){
$roleUserC=WClass::get('role.user');
return $roleUserC->insertRole($uid, $roles );
}
public static function syncUsers(){
$CMSaddon=WAddon::get('api.'.JOOBI_FRAMEWORK.'.import');
$CMSaddon->importCMSUsers(false);
}
public static function removeRole($uid,$roles){
$roleUserC=WClass::get('role.user');
return $roleUserC->deleteRole($uid, $roles );
}
public static function pref($wid=null,$force=false,$loadConf=true,$userPref=true){
return WPref::get($wid, $force, $loadConf, $userPref );
}
public static function avatar($uid){
$allAvatar=array();
if(!isset($allAvatar[$uid] )){
$filid=WUser::get('filid',$uid );
if(!empty($filid )){
$fileM=WModel::get('files');
$fileM->whereE('filid',$filid );
$myFile=$fileM->load('o',array('name','type','path','secure'));
$allAvatar[$uid]=JOOBI_URL_MEDIA . $fileM->convertPath($myFile->path, 'url'). '/'.$myFile->name.'.'.$myFile->type;
}else{
$allAvatar[$uid]=JOOBI_URL_MEDIA. 'images/user/userx.png';
}
}
return $allAvatar[$uid];
}
public static function timezone($uid=null){
static $timezone=array();
if(!isset($timezone[$uid])){$timezone[$uid]=( WUser::get('timezone',$uid )) * 60;}return (int)$timezone[$uid];
}
public static function isRegistered($uid=null){
$val=WUser::get('registered',$uid );
return ($val> 0 )?true : false;
}
public static function roles($uid=null){
$roleHelperC=WRole::get();
return $roleHelperC->getUserRoles($uid );
}
public static function getRoleUsers($rolid,$maps=array()){
$roleHelperC=WRole::get();
return $roleHelperC->getRoleUsers($rolid, $maps );
}
public static function session(){
static $instance=null;
if(!isset($instance))$instance=WClass::get('users.session');
return $instance;
}
public static function credential(){
$instance=WClass::get('users.credential');
return $instance;
}
}
abstract class WClass {
private static $_dontLoadTranslationA=array('view');
private static $_dontLoadNodeA=array('library','install','api','users');
public static function get($path,$params=null,$type='class',$showMessage=true,$base=null){
$path=strtolower($path );
$myPos=strpos($path, '.');
$myFuntion=substr($path, 0 , $myPos );
$myFile=substr($path, $myPos+1 );
if(empty($base))$base=JOOBI_DS_NODE;
$exists=WLoadFile($myFuntion.'.'.$type.'.'.$myFile, $base, true, $showMessage );
if(!$exists){
if($showMessage){
$message=WMessage::get();
$message->codeE('The '.$type.' file was not found !  Name:'.$myFuntion.'.'.$type.'.'.$myFile );
}$tmp=null;
return $tmp;
}
$className=str_replace('.','_',$path ). '_'.$type;
if(!empty($className) && class_exists($className )){
static $alreadyTranslated=array();
if(!in_array($myFuntion, self::$_dontLoadNodeA ) && !in_array($type, self::$_dontLoadTranslationA ) && !isset($alreadyTranslated[$myFuntion])){
$alreadyTranslated[$myFuntion]=true;
if(!defined('JOOBI_INSTALLING') && WUser::$ready){
$extension=WExtension::get($myFuntion .'.node','data', null, null, false);
if(!empty($extension) && !empty($extension->trans)){WText::load($extension->wid );
}
}
}
if(!empty($params)){
$newClass=new $className($params);
} else
$newClass=new $className;
}else{
$message=WMessage::get();
$message->codeE('Class not found in the class file!  Name:'.$className );
$tmp=null;
return $tmp;
}
return $newClass;
}
}
abstract class WType {
public static function get($path,$showMessage=true){
static $loaded=false;
if(!$loaded){
WLoadFile('type' , JOOBI_LIB_CORE.'class'.DS );
$loaded=true;
}
$myPos=strpos($path, '.');
$myFuntion=substr($path, 0 , $myPos );
$myFile=substr($path, $myPos+1 );
$exists=WLoadFile($myFuntion.'.type.'.$myFile , JOOBI_DS_NODE, true, $showMessage );
if(!$exists){
if($showMessage){
$message=WMessage::get();
$message->codeE('Type file in WType::get() not found for the type!  Name:'.$path );
}$tmp=null;
return $tmp;
}
$className=str_replace('.','_',$path ). '_type';
if(!empty($className) && class_exists($className )){
$myExploded=explode('.',$path );
$newClass=new $className( array_pop($myExploded ));
$newClass->node=$myExploded[0];
}else{
if($showMessage){
$message=WMessage::get();
$message->codeE('Class file not found in the type file!  Name:'.$className );
}$tmp=null;
return $tmp;
}
return $newClass;
}
}
abstract class WAddon {
public static function get($path,$params=null,$type='addon',$showMessage=true){
$path=strtolower($path );
$myPos=strpos($path, '.');
$myNode=substr($path, 0 , $myPos );
$myAddon=substr($path, $myPos+1 );
$myPos=strpos($myAddon, '.');
if($myPos!==false){
$myFile=substr($myAddon, $myPos+1 );
$myAddon=substr($myAddon, 0 , $myPos );
}else{
$myFile=$myAddon;
}
$exists=WLoadFile($myNode.'.'.$type.'.'.$myAddon.'.'.$myFile , JOOBI_DS_NODE  );
if(!$exists){
$message=WMessage::get();
$message->codeE('Class file not found for the '.$type.'!  Name:'.$path );
$tmp=null;
return $tmp;
}
$className=str_replace('.','_',$path ). '_'.$type;
if(!empty($className) && class_exists($className )){
if(!empty($params)){
$newClass=new $className($params);
}else{
$newClass=new $className;
}}else{
if($showMessage){
$message=WMessage::get();
$message->codeE( ucfirst($type).' file not found in the class file!  Name:'.$className );
}$tmp=null;
return $tmp;
}
return $newClass;
}
}
abstract class WEvent {
public static function get($path,$uid=0,$params=null){
$campaignExist=WModel::modelExist('campaign.event');
if($campaignExist){
$campaignEventsC=WClass::get('library.event');
return $campaignEventsC->checkForEvents($path, $uid, $params );
}
return false;
}
}
class WAction {
public static function get($path,$params=null,$status=false,$fct='create'){
$path=strtolower($path );
$myPos=strpos($path, '.');
$myNode=substr($path, 0 , $myPos );
$myAddon=substr($path, $myPos+1 );
$myPos=strpos($myAddon, '.');
if($myPos!==false){
$myFile=substr($myAddon, $myPos+1 );
$myAddon=substr($myAddon, 0 , $myPos );
$classPath=$path;
}else{
$myFile=$myAddon;
$classPath=$path.'.'.$myFile;
}
$basePath=(!empty($params->custom)?JOOBI_DS_USER.'custom'.DS : JOOBI_DS_NODE );
$exists=WLoadFile($myNode.'.action.'.$myAddon.'.'.$myFile, $basePath );
if(!$exists){
$message=WMessage::get();
$message->codeE('Class file not found for the Action!  Name:'.$path );
$tmp=null;
return $tmp;
}
$className=str_replace('.','_',$classPath ). '_action';
if(!empty($className) && class_exists($className )){
$newClass=new $className();
}else{
$message=WMessage::get();
$message->codeE( ucfirst($myFile).' file not found in the class file!  Name:'.$className );
$tmp=null;
return $tmp;
}
switch($fct){
case 'execute':
if( method_exists($newClass, 'execute')){
$newClass->execute($params );
}break;
case 'create':
default:
if( method_exists($newClass, 'create')){
$newClass->create($status, $params );
}break;
}
return $newClass;
}
public function getSQL($ctrid,$showMessage=true){
$libraryTriggerC=WClass::get('library.trigger');
return $libraryTriggerC->getSQL($ctrid, $showMessage );
}
}
abstract class WObject {
public static function get($name,$params=null,$showMessage=true){
return WClass::get($name, $params=null, 'object',$showMessage );
}
public static function newObject($name){
$object=WClass::get($name, null, 'object', false);
if(empty($object)){
return new stdClass;
}
$name=get_class($object ). 'Data';
if( class_exists($name )) return new $name;
else {
return new stdClass;
}
}
}
abstract class WRole {
public static $roleC=null;
public static function get($rolid=null,$return='rolid'){
if(!isset(self::$roleC)) self::$roleC=WClass::get('role.helper');
if(!isset($rolid)){
return self::$roleC;
}else{
return self::$roleC->getRole($rolid, $return );
}
}
public static function getRole($namekey,$return='rolid'){
$roleC=WRole::get();
return $roleC->getRole($namekey, $return );
}
public static function isAdmin($role=''){
if(!IS_ADMIN ) return false;
elseif(empty($role)) return true;
else {
$roleC=WRole::get();
return $roleC->hasRole($role );
}
}
public static function isNotAdmin($role=''){
if(!IS_ADMIN ) return true;
elseif(empty($role)) return false;else {
$roleC=WRole::get();
return ( ! $roleC->hasRole($role ));}
}
public static function hasRole($namekey,$uid=0,$onlyMainRole=false){
$roleC=WRole::get();
return $roleC->hasRole($namekey, $uid, $onlyMainRole );
}
}
abstract class WMail {
public static $emailC=null;
public static function get(){
if(!isset(self::$emailC)) self::$emailC=WClass::get('email.mailing');
return self::$emailC;
}
}
abstract class WNetcom extends WObj {
public static function get($protocol='ixr',$showMessage=true){
static $instance=array();
if(!isset($instance[$protocol])){
WLoadFile('netcom.parent.class');
$instance[$protocol]=WAddon::get('netcom.'. $protocol );
if(empty($instance[$protocol])){
unset($instance[$protocol] );
$mess=WMessage::get();
$PROTOCOL=$protocol;
if($showMessage)$mess->adminE('The communication protocol '. $PROTOCOL.' is not available on this website. Please install it.');
return false;
}}
$instance[$protocol]->setShowMessage($showMessage );
return $instance[$protocol];
}
public static function getCode($node,$code=0,$isError=true){
$r='JB_';
$r .=($isError?'ERROR' : 'SUCCESS');
$r .='_'.strtoupper( WExtension::get($node, 'folder'));
if(!empty($code)){
if(is_array($code) || is_object($code))$code .=serialize($code );
$code=substr($code, 0, 100 ); $r .='_'.strtoupper($code );
}return $r;
}
public static function convertCode($code){
$codeA=explode('_',$code );
$folder=$codeA[2];
$netClass=WClass::get( strtolower($codeA[2]). '.netcomcode', null, 'class', false);
if(!empty($netClass)){
if(!isset($codeA[3])){
return false;
}return $netClass->convertAnswers($codeA[3] );
}return;
}
}
abstract class WPage extends CMSAPIPage {
public static $headerA=array();
public static $validation=null;
public static function redirect($url=null,$wPageID=true,$route=true,$code=303,$extraURL=''){
if( WGlobals::get('wmjx')){
$message=WMessage::get();
$userMess=$message->getM();
if(!empty($userMess)){
WController::ajaxReturnObj('replace','#WMessage',$userMess );
}
WController::ajaxReturnObj('redirect','',$url );
WController::returnAjax();
return;
}
if( WGlobals::get('wajx')){
return;
}
$storeMessage=true;
if(!empty($extraURL)) WGlobals::set('extraURL-Joobi',$extraURL, 'global');
if(!isset($url)){
$url=WGlobals::getReturnId();
$url=WPage::routeURL($url, '','default',$SSL, $wPageID );
}elseif($route){
$url=trim($url );
if('previous' !=$url){
$pos=strpos($url, '?');
$newURL=substr($url, $pos );
$explA=explode('&',$newURL );
if(!empty($explA)){
$ctr='';
$tsk='';
foreach($explA as $exp){
$ghA=explode('=',$exp );
if('controller'==$ghA[0] && !empty($ghA[1]))$ctr=$ghA[1];
if('task'==$ghA[0] && !empty($ghA[1]))$tsk=$ghA[1];
}
}
}
$startURL=substr($url, 0, 4);
if($startURL !='http' && $startURL !='inde'){
$url=WPage::routeURL($url,'smart', false, false, $wPageID ); }else{
$storeMessage=false;
}}
if($storeMessage){
$message=WMessage::get();
if('wordpress' !=JOOBI_FRAMEWORK_TYPE){
$php_errors=ob_get_clean();
$php_errors=trim($php_errors);
if(!empty($php_errors))$message->adminE($php_errors );
}$message->store();
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
if( headers_sent()){
echo "<script>document.location.href='" . str_replace( "'", "&apos;", $url ). "';</script>\n";
}else{
switch($code){
case 303:
$text='See other';
break;
case 302:
$text='Found';
break;
case 301:
$text='Moved Permanently';
break;
default:
$text='Moved Permanently';
WMessage::log('Redirect status not found:'.$code, 'code-redirect');
break;
}header('HTTP/1.1 '.$code.' '. $text );
header('Location: '.$url );
}exit();
}
public static function cmsGetItemId($page='',$task=''){
return WPage::getPageID($page, $task );
}
public static function URI(){
if( strpos(php_sapi_name(), 'cgi') !==false && !ini_get('cgi.fix_pathinfo') && !empty($_SERVER['REQUEST_URI'])){
$uri=rtrim( dirname($_SERVER['PHP_SELF'] ), '/\\');
}else{
$uri=rtrim( dirname($_SERVER['SCRIPT_NAME'] ), '/\\');
}
return $uri;
}
public static function link($link,$itemId=true,$absoluteLink=''){
return WPage::routeURL($link, $absoluteLink, false, false, $itemId );
}
public static function route($link,$absoluteLink='',$old=''){
return WPage::routeURL($link, $absoluteLink, $old );
}
public static function linkPopUp($link,$useSEF=true){
return WPage::routeURL($link, '','popup', false, false, null, ! $useSEF );
}
public static function linkHome($link,$itemId=true){
return WPage::routeURL($link, 'home', false, false, $itemId );
}
public static function linkAdmin($link){
return WPage::routeURL($link, 'admin');
}
public static function linkAjax($link){
return WPage::linkNoSEF($link, 'ajax');
}
public static function &theme($id='main',$renderType=''){
static $once=true;
if($once){
WLoadFile('library.class.theme');
$once=false;
}
$template=&WTheme::getInstance($id, $renderType );
return $template;
}
public static function getThemePath($extensionNamekey){
$folderCat=WExtension::get($extensionNamekey, 'folder');
return $folderCat;
}
public static function browser($return='object'){
$libraryDeviceC=WClass::get('library.device');
return $libraryDeviceC->browser($return );
}
public static function newBluePrint($element){
return WPage::renderBluePrint($element, null, 'newObject');
}
public static function renderBluePrint($element,$data=null,$function='render'){
static $load=true;
if(empty($element)) return false;
if($load){
WLoadFile('theme.class.render');$load=false;
}
if(!defined('JOOBI_URL_THEME_JOOBI')) WView::definePath();
WLoadFile('blueprint.'.$element, JOOBI_DS_NODE.'output'.DS, true, false);
WLoadFile('blueprint.'.$element, JOOBI_DS_THEME_JOOBI );
$className='WRender_'.ucfirst($element ). '_class';
if( class_exists($className )){
$instance=new $className; 
$html=$instance->$function($data );
}else{
$message=WMessage::get();
$message->codeE('The file blueprint.'.$element.' does not exist at the location: '.JOOBI_DS_THEME_JOOBI );
$html='';
}
return $html;
}
public static function storeScript($script=false,$type='js',$name='default',$index='',$loadonDOMready=true,$reset=false){
static $allScriptsA=array();
if($reset){
$allScriptsA=array();
}
if($script===false){
return $allScriptsA;
}
if($type=='js' && !isset($allScriptsA['js']))$allScriptsA['js']['onDOMready']=array();
if($loadonDOMready)$loadOn='onDOMready';
else $loadOn='onThefly';
if(!empty($index)){
if(!isset($allScriptsA[$type][$loadOn][$name][$index]))$allScriptsA[$type][$loadOn][$name][$index]=$script;
}else{
$allScriptsA[$type][$loadOn][$name][]=$script;
}
return '';
}
public static function getScript(){
$scriptA=WPage::storeScript();
return $scriptA;
}
public static function resetScript(){
WPage::storeScript(false , 'js','default','' ,true ,true);
}
public static function addJSLibrary($path){
WPage::storeScript('','js',$path );
}
public static function addImage($image='',$location='theme',$cdn=''){
return WPage::fileLocation('img',$image, $location, $cdn, false);
}
public static function addJSFile($file='',$location='theme',$cdn='',$minify=true){
$path=WPage::fileLocation('js',$file, $location, $cdn, $minify );
WPage::storeScript('','js',$path );
}
public static function addCSSFile($file='',$location='theme',$cdn='',$media=null,$attributes=array()){
$path1=WPage::fileLocation('css',$file, $location, $cdn, true, false);
WPage::addStyleSheet($path1, 'text/css',$media, $attributes );
$path2=WPage::fileLocation('css',$file, $location, $cdn );
if($path1 !=$path2 ) WPage::addStyleSheet($path2, 'text/css',$media, $attributes );
}
public static function addJSScript($script,$path='default',$loadonDOMready=true){
WPage::storeScript($script , 'js',$path, '',$loadonDOMready );
}
public static function addCSSScript($script=''){
WPage::addCSS($script );
}
public static function fileLocation($type='css',$file='',$location='theme',$cdn='',$minify=true,$themeFile=true){
if('theme'==$location){
if(!defined('JOOBI_URL_THEME_JOOBI')) WView::definePath();
if( in_array( JOOBI_FRAMEWORK_TYPE, array('joomla','wordpress'))){
$hasOverwrite=WView::themeIsOverWritten();
if($themeFile && $hasOverwrite){
$useMinify=WPref::load('PLIBRARY_NODE_USEMINIFY');
$tmpFile=$file;
if($useMinify && $minify){
$tmpFile .='WHJ3UY';
$tmpFile=str_replace('.'.$type.'WHJ3UY','.min.'.$type, $tmpFile );
}
$tempalte=WPage::getTemplate('path').DS.'joobi'.DS.str_replace('/', DS, $tmpFile );
if( file_exists($tempalte )){
return WPage::getTemplate('url'). '/joobi/'.$tmpFile;
}
}}
$path=JOOBI_URL_THEME_JOOBI;
}elseif('inc'==$location){
$path=JOOBI_URL_INC;
if( is_bool($minify))$minify=true; else if('ForceFalse'==$minify)$minify=false;
}elseif('api'==$location){
$path=JOOBI_SITE_PATH . JOOBI_FOLDER.'/node/api/addon/'.JOOBI_FRAMEWORK.'/';
$minify=true; }elseif('skin'==$location){
$path=JOOBI_URL_USER.'theme/skin/';
}elseif('none'==$location){
if( WPref::load('PLIBRARY_NODE_CDNUSE') && !empty($cdn)) return $cdn;
else return $file;
}elseif('home'==$location || 'media'==$location){
if('media'==$location){
$path=JOOBI_SITE.'joobi/user/media/';
}else{
$path=JOOBI_SITE.'';
}}else{
$path=$file;
if(empty($cdn))$cdn=$location;
$minify=true; }
if( WPref::load('PLIBRARY_NODE_CDNUSE') && class_exists('WTheme') && empty(WTheme::$isCloned)){
if(!empty($cdn)) return $cdn;
if($minify){
$file .='WHJ3UY';
$file=str_replace('.'.$type.'WHJ3UY','.min.'.$type, $file );
}
if('img'==$type){
if('..'==substr($file, 0, 2 ))$file=substr($file, 2 );
$path=str_replace( JOOBI_SITE , '/',$path );
}
$pos=strpos($path, '/'.JOOBI_FOLDER.'/');
if(empty($pos))$pos=0;
$newPath=substr($path, $pos );
$cdnLocation=rtrim( WPref::load('PLIBRARY_NODE_CDNSERVER'), '/');
return $cdnLocation . $newPath . $file;
}else{
$useMinify=WPref::load('PLIBRARY_NODE_USEMINIFY');
if($useMinify && $minify){
$file .='WHJ3UY';
$file=str_replace('.'.$type.'WHJ3UY','.min.'.$type, $file );
}
}
return $path . $file;
}
public static function jsAction($task,$paramsO=null,$valueA=array()){static $xParamsA=array();
static $baseURL=false;
if(empty($task)) return false;
if(!isset($paramsO ))$paramsO=WObject::newObject('output.jsaction');
if(empty($paramsO->form))$formName=WView::form('', false, true);
else {
$formName=$paramsO->form;
}
$uniqueKey=$formName;
$uniqueKey .='|'.$task;
if(!empty($paramsO->controller)){
$uniqueKey .='|'.$paramsO->controller;
$ctrl=$paramsO->controller;
unset($paramsO->controller );
}else{
if(!empty($valueA['controller'])){
$ctrl=$valueA['controller'];
unset($valueA['controller'] );
}else{
$ctrl=WGlobals::get('controller','', null, 'task');
}
}
if(!empty($paramsO->zmap))$uniqueKey .='|'.$paramsO->zmap;
$taskJS=$ctrl.'|'.$task;
if(isset($xParamsA[$uniqueKey]) && ($paramsO->namekey===false || ($paramsO->namekey==$xParamsA[$uniqueKey]))){
$paramsO->namekey=$xParamsA[$uniqueKey];
}
if(empty($paramsO->namekey)){
if( WGet::isDebug()){
$paramsO->namekey='ActionJS_'.WGlobals::filter($uniqueKey, 'jsnamekey');
}else{
$paramsO->namekey='WZY_'.WGlobals::count('f');
}}
$namekey=$paramsO->namekey;
unset($paramsO->namekey );
if(!empty($paramsO->ajx )){
if(empty($paramsO->url )){
$paramsO->url=WPages::linkAjax('controller='.WGlobals::get('controller','', null, 'task'));
}
}
if(  !empty($paramsO->goBtn)){
static $enterSet=true;
if($enterSet){
WPage::addJSScript( "jCore.ajx='1';" . WGet::$rLine );
WPage::addJSScript( "jCore.prssEnter='$formName|$task';" . WGet::$rLine );
}$enterSet=false;
}
$secondJSON='';
if(isset($valueA['fRmjx'] ) && empty($valueA['fRmjx'])){
$valueA['fRmjx']=WGlobals::get('fRmjx');
}
WPage::includeRootScript();
$secondJSONA=array();
foreach($valueA as $k=> $v)$secondJSONA[]="$k=$v";
$secondJSON=implode('&',$secondJSONA );
$joobiRun=JOOBI_JS_APP_NAME.'.';
if(!empty($paramsO->confirm) || !empty($paramsO->select)){
if(!empty($paramsO->confirm)){
if(empty($paramsO->confirmMessage)){
$ACTION=(!empty($paramsO->confirmName)?$paramsO->confirmName : ' '.WText::t('1460670474RWIA'));
$confirmMess=str_replace(array('$ACTION'), array($ACTION),WText::t('1233626551NWXV'));
}else{
$confirmMess=$paramsO->confirmMessage;
}}else{
$confirmMess='';
}
if(!empty($paramsO->select)){
if(empty($paramsO->selectMessage)){
$ACTION=(!empty($paramsO->selectName)?$paramsO->selectName : ' '.WText::t('1460670474RWIA'));
$selectMess=str_replace(array('$ACTION'), array($ACTION),WText::t('1460670474RWIB'));
}else{
$selectMess=$paramsO->selectMessage;
}
}else{
$selectMess='';
}$joobiRun .="rjcf('$formName','$taskJS','" . $secondJSON . "','" . WTools::parseJSText($confirmMess ). "','" . WTools::parseJSText($selectMess ). "'";if(!empty($paramsO->isButton)){
$joobiRun .=',this';
}
}elseif(!empty($paramsO->validation)){
$joobiRun .="rjvl('$formName','$taskJS','" . $secondJSON . "',this";
}else{
$joobiRun .="rj('$formName','$taskJS','" . $secondJSON . "'";
}
$joobiRun .=');';
if(!empty($paramsO->autoSave)){
$timeInterval=WPref::load('PLIBRARY_NODE_TIMEINTERVAL');
if(!empty($timeInterval) && is_numeric($timeInterval)){
$time=$timeInterval * 1000;$script='setInterval(function(){ '.$joobiRun.' },'.$time.');';
WPage::addJSScript($script );
}}
return 'return '.$joobiRun;
}
public static function actionJavaScript($task,$formName=false,$JSparams=array(),$buttonParam=false,$namekey=false,$trueTask=false,$thirdJSParam=''){
static $buttons=array();
$declared=false;
if(empty($task) && ! $namekey ) return false;
if(!empty(self::$validation)){
$JSparams['validation']=true;
WPage::addJSLibrary('validation');
}
if(isset($JSparams['ajxUrl']) || !empty($JSparams['ajx'])){
$JSparams['ajxUrl']=(!empty($JSparams['ajxUrl'])?$JSparams['ajxUrl'].'&wajx=1' : 'wajx=1');
$JSparams['ajxUrl']=WPage::linkAjax($JSparams['ajxUrl'] );
}
if(!empty($JSparams['ajaxToggle'])){
$JSparams['ajx']=1;
}
if(empty($formName))$formName=WView::form('', false, true);
$uniqueKey=$formName;
$uniqueKey .='|'.$task;
if(!empty($JSparams['controller']))$uniqueKey .='|'.$JSparams['controller'];
if(!empty($JSparams['zmap']))$uniqueKey .='|'.$JSparams['zmap'];
if(isset($buttons[$uniqueKey]) && ($namekey===false || ($namekey==$buttons[$uniqueKey]))){
$namekey=$buttons[$uniqueKey];
$declared=true;
}
if(empty($namekey)){
if( WGet::isDebug()){
$namekey='ActionJS_'.WGlobals::filter($uniqueKey, 'jsnamekey');
}else{
$namekey='WZY_'.WGlobals::count('f');
}}
if(!empty($JSparams['enterSubmit'])){
static $enterSet=true;
if($enterSet){
WPage::addJSScript( "jCore.prssEnter='$namekey';" . WGet::$rLine );
}$enterSet=false;
unset($JSparams['enterSubmit'] );
}
if(!empty($JSparams['requireCheckBoxTicked'])){
unset($JSparams['requireCheckBoxTicked'] );
$JSparams['rqCk']=1;
if(!empty($JSparams['requireCheckBoxClass'])){
$JSparams['rqCkCl']=$JSparams['requireCheckBoxClass'];
unset($JSparams['requireCheckBoxClass'] );
}
}
WPage::includeRootScript();
$joobiRun=JOOBI_JS_APP_NAME.'.run(\''.$namekey.'\'';
if($buttonParam !==false){
if(empty($buttonParam) || ( is_string($buttonParam) && $buttonParam[0] !='{')){
$joobiRun .=',\''.$buttonParam.'\'';
}elseif(!empty($buttonParam) && is_object($buttonParam)){
$joobiRun .=',{';
$allParamsA=array();
foreach($buttonParam as $pKey=> $pVal){
$allParamsA[]="'".$pKey."':'".$pVal."'";
}$joobiRun .=implode(',',$allParamsA );
$joobiRun .='}';
}elseif(!empty($buttonParam)){$joobiRun .=','.$buttonParam;
}
if(!empty($thirdJSParam))$joobiRun .=','.$thirdJSParam;
}
$joobiRun .=');';
if($declared===true) return $joobiRun;
$jsTask=($trueTask)?$trueTask : $task;
$joobiButtonJS=JOOBI_JS_APP_NAME . ".buttons['" . $namekey . "']={'formName':'" . $formName . "','task':'" . $jsTask . "'";
$ACTION=(!empty($JSparams['action'])?WGlobals::filter($JSparams['action'] , 'safejs') : $ACTION=rtrim($task, 'all'));
foreach($JSparams as $key=> $value){
if( is_string($value))$value='\''.$value.'\'';
if($key=='confirm'){
if(!isset($jscript['js']['onThefly']['msg'][ 'conf_'. $namekey] )){
if(!empty($JSparams['conf_'.$task] )){
$message=WGlobals::filter($JSparams['conf_'.$task], 'safejs');
unset($JSparams['conf_'.$task] );
}else{
$message=WTools::parseJSText( str_replace(array('$ACTION'), array($ACTION),WText::t('1233626551NWXV')));
}
WPage::addJSScript( "jCore.msg['conf_" . $namekey . "']='" . $message . "';", 'msg', false);
}}
if($key=='select'){
if(!isset($jscript['js']['onThefly']['msg']['selec_'.$namekey] )){
if(!empty($JSparams['select_'.$task] )){
$message=WGlobals::filter($JSparams['select_'.$task], 'safejs');
unset($JSparams['select_'.$task] );
}else{
$message=WTools::parseJSText( str_replace(array('$ACTION'), array($ACTION),WText::t('1213107586DVLP')));
}
WPage::addJSScript( "jCore.msg['selec_" . $namekey . "']='" . $message . "';", 'msg', false);
}}
$joobiButtonJS .=",'" . $key . "':" . $value;
}
$joobiButtonJS .="};";
$buttons[$uniqueKey]=$namekey;
WPage::addJSScript($joobiButtonJS, 'default' , false);
return $joobiRun;
}
public static function declareJS(){
static $declared=false;
static $onceInclude=array();
$allScripts=WPage::getScript();
$string='';
$stringBreak='';
if(empty($allScripts['js'])) return;
foreach($allScripts['js'] as $whenTowrite=> $object){
foreach($object as $what=> $jscript ){
if(!isset($onceInclude[$what])){
switch($what){
case 'mootools':
WPage::includeMootools();
break;
case 'rootscript':WPage::includeMootools();
WPage::includeRootScript();
break;
case 'req':
case 'validation':WPage::includeMootools();
WPage::includeRootScript();
$path=WPage::fileLocation('js','js/validation.1.1.js');
WPage::addScript($path );
break;
case 'jquery':
WPage::includejQuery();
break;
}}}}
$foreachString='';
foreach($allScripts['js'] as $whenTowrite=> $object){
foreach($object as $what=> $jscript ){
if(!isset($onceInclude[$what])){
$onceInclude[$what]=true;
switch($what){
#6739462DHJ475
case 'req':
case 'validation':case 'mootools':
case 'rootscript':
case 'jquery':
break;
case 'nedit':
case 'nicedit':
$path=WPage::fileLocation('js','main/nicedit/nicEdit.js','inc');
WPage::addScript($path );
break;
case 'phpedit':
$path=WPage::fileLocation('js','editarea/edit_area_full.js','inc');
WPage::addScript($path );
break;
case 'joobibox':
WPage::includeJoobiBox();
break;
case 'default':
case 'msg':
break;
default:
WPage::addScript($what );
break;
}
}
$newJScriptA=array();
foreach($jscript as $oneScript ) if(!empty($oneScript))$newJScriptA[]=$oneScript;
if(!empty($newJScriptA)){
$foreachString .=implode((!empty($stringBreak )?$stringBreak : ' '),  $newJScriptA );
$newJScriptA=array();
}
}
if(empty($foreachString)) continue;
if($whenTowrite=='onDOMready' && !empty($object )){
$foreachString=$stringBreak . JOOBI_JS_APP_NAME.'.onDOMready(function(){'.$stringBreak . $foreachString;
$foreachString .=$stringBreak.'}'.$stringBreak.');';
}
}
$stringjCore='';
if(!$declared){
if( IS_ADMIN && 'joomla30'==JOOBI_FRAMEWORK)$stringjCore .=$stringBreak.'jCore.isAdmin=\'admin\';';
$stringjCore .=$stringBreak . "jCore.spf='" . JOOBI_SPOOF . "';";
}
$string=$stringjCore . $foreachString;
WPage::addJS($string, 'text/javascript', true);
$declared=true;
WPage::resetScript();
}
public static function validSite($url,$checkDNS=false){
$url=trim($url);
$url=rtrim($url, '/');
$url=str_replace( array('http://','https://'), '',$url );
if(( strpos($url, 'localhost')===false
 && substr($url, 0, 3 ) !='10.'
 && substr($url, 0, 8 ) !='192.168.'
 && substr($url, 0, 7 ) !='172.16.'
 && substr($url, 0, 4 ) !='127.')){
 if($checkDNS){
 $newURLA=explode('/',$url );
 $newURL=array_shift($newURLA );
 return checkdnsrr($newURL, 'A');
 } else return true;
}else{
return false;
}
}
public static function getHTTP(){
$useHTTPS=WGlobals::get('HTTPS','off','server');
if('on'==$useHTTPS ) return 'https://';
else return 'http://';
}
public static function changeEncoding($data,$input,$ouput){
if(is_array($data) || is_object($data)){
$type=(is_array($data)?'array' : 'object');
$message=WMessage::get();
$message->codeE('You are trying to convert an '.$type.' but only strings can have their encoding changed. Please correct your code');
$message->log('You are trying to convert an '.$type.' but only strings can have their encoding changed. Please correct your code');
return '';
}
WExtension::includes('lib.encoding');
if( class_exists('Inc_Lib_Encoding_include')){
return Inc_Lib_Encoding_include::changeEncoding($data, $input, $ouput );
}else{
WMessage::log('No encoding handler was found for converting from '. $input.' to '.$ouput, 'encoding_handler');
WMessage::log('The data is :'.$data , 'encoding_handler');
WMessage::log( get_declared_classes() ,'encoding_handler');
return $data;
}
}
}
class WText {
public static $vocab=array();
public static function t($string){
if(isset( self::$vocab[$string] )) return self::$vocab[$string];
return '';
}
public static function tn($string){
return $string;
}
public static function load($wid){
static $instance=null;
if(!is_numeric($wid)){
$wid=WExtension::get($wid, 'wid');
if(empty($wid)) return false;
}
$translation=WExtension::get($wid, 'trans');
if(empty($translation)) return false;
if(!isset($instance))$instance=new WTranslation();
$instance->load($wid );
}
public static function translate($text,$namekey=''){
return $text;
}
public static function translateNone($text,$namekey=''){
return $text;
}
public static function get($text){
$lgid=WUser::get('lgid');
$langCode=WLanguage::get($lgid, 'code');
if($langCode=='en') return $text;
$lnagM=WModel::get('translation.'.$langCode );
$lnagM->makeLJ('translation.en','imac');
$lnagM->whereE('text',$text, 1 );
$lnagM->select('text', 0 );
$translatedText=$lnagM->load('lr');
return $translatedText;
}
public static function lib($text){
return $text;
}
}
abstract class WCart {
static public $currency=0;
public static function get(){
static $instance=null;
if(isset($instance)) return $instance;
$instance=WObject::get('cart.cart', null, false);
if(empty($instance))$instance=false;
return $instance;
}
}
abstract class WHook {
private static $_hooksA=array();
public static function addAction($action,$functionToCall,$priority=10,$acceptedArgs=1){
self::$_hooksA[$action][$priority][]=array('fct'=> $functionToCall, 'argNb'=> $acceptedArgs );
}
public static function doAction($action,$argA){
}
}