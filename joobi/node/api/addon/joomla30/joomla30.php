<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
### Copyright (c) 2006-2016 Joobi. All rights reserved.
### license GNU GPLv3 , link joobi.info/license
define('JOOBI_FRAMEWORK_TYPE','joomla');
define('JOOBI_FRAMEWORK_TYPE_ID', 20 );
define('JOOBI_MAIN_APP','japps');
define('JOOBI_URLAPP_PAGE','option');
define('JOOBI_PAGEID_NAME','Itemid');
$app=JFactory::getApplication();
define('JOOBI_SITE', rtrim( JURI::root(), "/" ). '/');
define('JOOBI_USE_FTP',$app->getCfg('ftp_enable'));
define('IS_ADMIN', ($app->isAdmin())?'250' : false);
define('JOOBI_SITE_NAME',$app->getCfg('sitename'));
define('JOOBI_SITE_TOKEN',$app->getCfg('secret'));
define('JOOBI_DS_ADMIN', JOOBI_DS_ROOT.'administrator'.DS );
define('JOOBI_USE_SEF',$app->getCfg('sef'));
if( IS_ADMIN){
$fP=substr( JURI::base(true), 0, -13 );
}else{
$fP=JURI::base(true). '/';
}define('JOOBI_SITE_PATH',$fP );
define('JOOBI_DEBUGCMS',$app->getCfg('debug'));
define('URL_NO_FRAMEWORK','&tmpl=component');
define('JOOBI_DB_TYPE','framework');
define('JOOBI_DB_PREFIX',$app->getCfg('dbprefix'));
define('JOOBI_DB_NAME',$app->getCfg('db'));
define('JOOBI_DB_HOSTNAME',$app->getCfg('host'));
define('JOOBI_DB_USER',$app->getCfg('user'));
define('JOOBI_DB_PASS',$app->getCfg('password'));
define('JOOBI_LIST_LIMIT',$app->getCfg('list_limit'));
define('JOOBI_FORM_METHOD','post');
define('JOOBI_FORM_HASOPTION', true);
define('JOOBI_FORM_HASRETURNID', true);
define('JOOBI_FORM_NAME','adminForm');
define('JOOBI_FORM_AUTOCOMPLETE' , true);
define('JOOBI_APP_DEVICE_TYPE','bw');
define('JOOBI_APP_DEVICE_SIZE','');
define('JOOBI_SESSION_LIFETIME',$app->getCfg('lifetime'));
if( IS_ADMIN){
define('JOOBI_INDEX','index.php');
define('JOOBI_INDEX2','index.php');
}else{
define('JOOBI_INDEX','index.php');
define('JOOBI_INDEX2','index.php');
}
abstract class APIPage {
public static function setTitle($title){
static $already=false;
if($already ) return;
$app=JFactory::getApplication();
$pagetitles=$app->getCfg('sitename_pagetitles');
if( 1==$pagetitles){
$title=JOOBI_SITE_NAME.' - '.$title;
}elseif( 2==$pagetitles){
$title .=' - '.JOOBI_SITE_NAME;
}
$document=JFactory::getDocument();
$document->setTitle($title );
if( IS_ADMIN ) JToolbarHelper::title($title );
$already=true;
}
public static function setDescription($title=''){
static $onlyOnce=false;
if($onlyOnce ) return;
$onlyOnce=true;
if(empty($title)){
$params=WModel::getElementData('joomla.menu', WGlobals::get('Itemid'), 'params');
if(!empty($params)){
$pO=json_decode($params );
$property='menu-meta_description';
if(!empty($pO->$property))$title=$pO->$property;
}}
if(empty($title)) return;
$document=JFactory::getDocument();
$document->setDescription($title );
}
public static function setMetaTag($key,$value=''){
static $onlyOnce=false;
if($onlyOnce ) return;
$onlyOnce=true;
if(empty($value)){
$params=WModel::getElementData('joomla.menu', WGlobals::get('Itemid'), 'params');
if(!empty($params)){
$pO=json_decode($params );
$property='menu-meta_keywords';
if(!empty($pO->$property))$value=$pO->$property;
}}
if(empty($value)) return;
$document=JFactory::getDocument();
$document->setMetaData($key, $value );
}
public static function setGenerator($title){
$document=JFactory::getDocument();
$document->setGenerator($title );
}
public static function setLink($link,$relation,$relType='rel',$extraAttributesA=array()){
$document=JFactory::getDocument();
$document->addHeadLink($link, $relation, $relType, $extraAttributesA);
}
public static function setType($type){
$document=JFactory::getDocument();
$document->setType($type );
}
public static function setLanguage($lang='en'){
$document=JFactory::getDocument();
$document->setLanguage($lang );
}
public static function setDirection($dir='ltr'){
$document=JFactory::getDocument();
$document->setDirection($dir );
}
public static function getTemplate($return=''){
static $tmpl=null;
if(!isset($tmpl )){
$app=JFactory::getApplication();
$tmpl=$app->getTemplate();
}
if(empty($tmpl)) return $tmpl;
elseif('url'==$return){
return JOOBI_SITE_PATH.'templates/'.$tmpl;
}else{
return JOOBI_DS_ROOT.'templates'.DS.$tmpl;
}
}
public static function isRTL(){
$lang=JFactory::getLanguage();
return $lang->isRTL();
}
public static function getSpoof($alt=null){
static $id=null;
if(isset($id)) return $id;
$id=JSession::getFormToken();
return $id;
}
public static function addScript($header,$type='text/javascript'){
if( WPref::load('PLIBRARY_NODE_AJAXPAGE') && WGlobals::get('wajx')){
return;
}$document=JFactory::getDocument();
$document->addScript($header, $type );
}
public static function addStyleSheet($header,$type='text/css',$media=null,$attributes=array()){
if( WPref::load('PLIBRARY_NODE_AJAXPAGE') && WGlobals::get('wajx')){
return;
}$document=JFactory::getDocument();
$document->addStyleSheet($header, $type, $media, $attributes );
}
public static function addCSS($header,$type='text/css'){
if( WPref::load('PLIBRARY_NODE_AJAXPAGE') && WGlobals::get('wajx')){
return;
}
$document=JFactory::getDocument();
$document->addStyleDeclaration($header, $type );
}
public static function addJS($header,$type='text/javascript'){
if( WPref::load('PLIBRARY_NODE_AJAXPAGE') && WGlobals::get('wajx')){
return;
}
$document=JFactory::getDocument();
$document->addScriptDeclaration($header, $type );
}
public static function encoding(){
$document=JFactory::getDocument();
return strtoupper($document->getCharset());
}
public static function cmsRoute($link,$SSL=null){
return JRoute::_($link, false, (int)$SSL );
}
public static function cmsGetShema(){
$uri=JURI::getInstance();
return $uri->getScheme();
}
public static function frameworkToken(){
$app=JFactory::getApplication();
return $app->getCfg('secret');
}
public static function getMailInfo(){
$app=JFactory::getApplication();
$mail=new stdClass;
$mail->fromname=$app->getCfg('fromname');
$mail->mailfrom=$app->getCfg('mailfrom');
$mail->mailer=$app->getCfg('mailer');
$mail->sendmail=$app->getCfg('sendmail');
$mail->smtpauth=$app->getCfg('smtpauth');
$mail->smtpsecure=$app->getCfg('smtpsecure');
$mail->smtpport=$app->getCfg('smtpport');
$mail->smtpuser=$app->getCfg('smtpuser');
$mail->smtppass=$app->getCfg('smtppass');
$mail->smtphost=$app->getCfg('smtphost');
return $mail;
}
public static function cmsDefaultTheme(){
return 'joomla30';
}
public static function keepAlive($get=false){
static $keepAlive=false;
if($get){
if($keepAlive){
return JHtml::_('behavior.keepalive');
}}else{
jimport('joomla.html.html.behavior');
$keepAlive=true;
}}
}
abstract class APIUser {
public static function getSessionId(){
$session=JFactory::getSession();
return $session->getId();
}
public static function cmsMyUser($property=''){
$user=JFactory::getUser();
return ((empty($property))?$user : $user->$property);
}
public static function cmsMakePassword($password){
if( class_exists('JUserHelper')){
$salt=JUserHelper::genRandomPassword(32);
$crypt=JUserHelper::getCryptedPassword($password,$salt);
$password=$crypt.':'.$salt;
return $password;
}}
}
abstract class APIApplication {
public static function version($return='short'){
$version=new JVersion();
switch($return){
case'all':
case'long':
return $version->getLongVersion();
break;
case'dev':
return $version->DEV_LEVEL;
break;
case'release':
return $version->RELEASE;
break;
case'short':
default:
return $version->getShortVersion();
break;
}
}
public static function cacheFolder(){
return JOOBI_DS_ROOT.'cache';
}
public static function joomlaPlugin($group,$action,$arguments=null){
if(!is_array($arguments)){
$arguments=array($arguments);
}
JPluginHelper::importPlugin($group, null, false);
$dispatcher=JDispatcher::getInstance();
$dispatcher->trigger($action, $arguments );
}
public static function cmsMainLang($location='site'){
$userLang=APIApplication::cmsUserLang();
if(!empty($userLang )) return $userLang;
jimport('joomla.application.component');
if(!class_exists('JComponentHelper')) return 'en-GB';
$params=JComponentHelper::getParams('com_languages');
$defaultLg=$params->get($location, 'en-GB');
return $defaultLg;
}
public static function cmsUserLang($short=false){
$user=JFactory::getUser();
if( IS_ADMIN){
$lang=$user->getParam('admin_language'); }else{
$lang=$user->getParam('language'); }
if(empty($lang)){
$langO=JFactory::getLanguage();
$lang=$langO->getTag();
}
if($short)$lang=substr($lang, 0, 2 );
return $lang;
}
public static function cmsAvailLang($path=JPATH_BASE){
return JLanguage::getKnownLanguages($path);
}
public static function cmsInitPlugin($obj){
$className=get_class($obj);
$exploded=explode('_',$className);
$newName='plg'.ucfirst($exploded[1]). ucfirst($exploded[0]). '_'.strtolower($exploded[1]). '_plugin';
$code='class '.$newName.' extends JPlugin {' ;
if(!empty($obj)){
foreach($obj as $property=> $oneParam){
 $code .='public $'.$property.'='.$oneParam.';';
}}
$code .='var $_type="";';
$code .='protected $_className="'.$className.'";';
$code .='protected $_classInstance=null;';
$code .='public function __construct(&$subject,$config){parent::__construct($subject,$config);$this->_classInstance=new $this->_className;}';
$code .='public function onAfterRoute(){return $this->_classInstance->onAfterRoute();}';
$code .='public function onAfterInitialise(){return $this->_classInstance->onAfterInitialise();}';
$code .='public function onAfterRender(){return $this->_classInstance->onAfterRender();}';
$code .='public function onAfterDispatch(){return $this->_classInstance->onAfterDispatch();}';
$code .='public function onExtensionAfterInstall($installer,$eid){return $this->_classInstance->onExtensionAfterInstall($installer,$eid);}';
$code .='public function onExtensionAfterSave($data,$isNew){return $this->_classInstance->onExtensionAfterSave($data,$isNew);}';
$code .='public function onExtensionAfterUninstall($installer,$eid,$result){return $this->_classInstance->onExtensionAfterUninstall($installer,$eid,$result);}';
$code .='public function onExtensionAfterUpdate($installer,$eid){return $this->_classInstance->onExtensionAfterUpdate($installer, $eid);}';
$code .='public function onExtensionBeforeInstall($method,$type,$manifest,$eid){return $this->_classInstance->onExtensionBeforeInstall($method,$type,$manifest,$eid);}';
$code .='public function onExtensionBeforeSave($data,$isNew){return $this->_classInstance->onExtensionBeforeSave($data,$isNew);}';
$code .='public function onExtensionBeforeUninstall($eid){return $this->_classInstance->onExtensionBeforeUninstall($eid);}';
$code .='public function onExtensionBeforeUpdate($type,$manifest){return $this->_classInstance->onExtensionBeforeUpdate($type,$manifest);}';
$code .='public function onContentAfterDelete($context,$data){ $this->_classInstance->onContentAfterDelete($context,$data);}';
$code .='public function onContentAfterSave($context,&$article, $isNew){ $this->_classInstance->onContentAfterSave($context, $article,$isNew);}';
$code .='public function onContentAfterTitle($context,&$article, &$params, $limitstart){ $this->_classInstance->onContentAfterTitle($context,$article,$params,$limitstart);}';
$code .='public function onContentBeforeDelete($context,$data){ $this->_classInstance->onContentBeforeDelete($context,$data);}';
$code .='public function onContentBeforeSave($context,&$article,$isNew){ $this->_classInstance->onContentBeforeSave($context,$article,$isNew);}';
$code .='public function onContentChangeState($context,$pks,$value){ $this->_classInstance->onContentChangeState($context,$pks,$value);}';
$code .='public function onContentPrepare($context,&$article,&$params,$limitstart=""){ $this->_classInstance->onContentPrepare($context,$article,$params,$limitstart);}';
$code .='public function onContentAfterDisplay($context,&$article,&$params,$limitstart=""){$this->_classInstance->onContentAfterDisplay($context,$article,$params,$limitstart);}';
$code .='public function onContentBeforeDisplay($context, &$article, &$params, $limitstart=""){$this->_classInstance->onContentBeforeDisplay($context,$article,$params,$limitstart);}';
$code .='public function onContentSearchAreas(){return $this->_classInstance->onContentSearchAreas();}';
$code .='public function onContentSearch($text,$phrase="",$ordering="",$areas=null){return $this->_classInstance->onContentSearch($text,$phrase,$ordering,$areas);}';
$code .='public function onUserAuthenticate($credentials, $options, &$response){return $this->_classInstance->onUserAuthenticate($credentials,$options,$response);}';
$code .='public function onUserBeforeSave($user,$isnew,$new){return $this->_classInstance->onUserBeforeSave($user,$isnew,$new);}';
$code .='public function onUserAfterSave($user,$isnew,$success,$msg){return $this->_classInstance->onUserAfterSave($user,$isnew,$success,$msg);}';
$code .='public function onUserBeforeDelete($user){return $this->_classInstance->onUserBeforeDelete($user);}';
$code .='public function onUserAfterDelete($user,$succes,$msg){return $this->_classInstance->onUserAfterDelete($user,$succes,$msg);}';
$code .='public function onUserLogin($user,$options){return $this->_classInstance->onUserLogin($user,$options);}';
$code .='public function onUserLogout($user,$option=array()){return $this->_classInstance->onUserLogout($user,$option);}';
$code .='public function onUserLoginFailure($response){return $this->_classInstance->onUserLoginFailure($response);}';
$code .='public function onUserBeforeDeleteGroup($group){return $this->_classInstance->onUserBeforeDeleteGroup($group);}';
$code .='public function onUserAfterDeleteGroup($group,$bool,$msg){return $this->_classInstance->onUserAfterDeleteGroup($group,$bool,$msg);}';
$code .='public function onUserBeforeSaveGroup($group){return $this->_classInstance->onUserBeforeSaveGroup($group);}';
$code .='public function onUserAfterSaveGroup($group){return $this->_classInstance->onUserAfterSaveGroup($group);}';
$code .='public function onDisplay($a){return $this->_classInstance->onDisplay($a);}';
$code .='}';
eval($code);
return;
}
public static function extract($file,$dest){
JLoader::import('joomla.filesystem.archive');
return JArchive::extract($file, $dest );
}
public static function installThemePath(){
define('JOOBI_URL_THEME_JOOBI', JOOBI_URL_USER.'theme/admin/'.WPage::cmsDefaultTheme(). '/');
define('JOOBI_DS_THEME_JOOBI', JOOBI_DS_USER.'theme'.DS.'admin'.DS.WPage::cmsDefaultTheme(). DS );
}
public static function renderLevel($level){
return '';
}
}
abstract class CMSAPIPage extends APIPage {
static private $_popOnlyOnce=true;
public static function routeURL($link,$absoluteLink='',$indexPassed=false,$SSL=false,$itemId=true,$foption=null,$noSEF=false){
$link=trim($link);
if( substr($link, 0, 4 )==='http') return $link;
$absoluteLink=trim($absoluteLink );
$device=WGlobals::get('device', false);
if($device=='mobile' || $device=='fb')$indexPassed='popup';
if($link=='previous'){
$url=WGlobals::getReturnId();
if(!empty($url)) return WPage::routeURL($url, '','link',$SSL, false);
$referer=WGlobals::get('HTTP_REFERER','','server','string');
if(empty($referer) || strpos($referer,JOOBI_SITE)===false){
$referer=JOOBI_SITE . ($absoluteLink=='smart'?( IS_ADMIN?'administrator' : '') : '');}else{
$referer=str_replace('&amp;','&',$referer );
}return $referer;
}elseif($link=='home'){
return JOOBI_SITE;
}
if($indexPassed===false){
$isPopUp=WGlobals::get('is_popup', false, 'global');
if(($isPopUp ))$index='popup';
else $index='default';
}else{
$index=trim( strtolower($indexPassed));
}
$home=false;
if($absoluteLink=='smart'){
if( IS_ADMIN){
$absoluteLinkNewLink=JOOBI_SITE.'administrator/';
}else{
$absoluteLinkNewLink=JOOBI_SITE;
}}elseif($absoluteLink=='home'){
$absoluteLinkNewLink=JOOBI_SITE;
if($indexPassed===false)$index='default';
}elseif($absoluteLink=='admin'){
$absoluteLinkNewLink=JOOBI_SITE.'administrator/';
$itemId=false;
if($indexPassed===false)$index='default';
}elseif($absoluteLink){
$absoluteLinkNewLink=JOOBI_SITE.$absoluteLink.'/';
}else{
$absoluteLinkNewLink=$absoluteLink;
$noIndex=true;
}
if($index=='default'){
if( substr($link, 0, 5 ) !='index'){
if(!isset($currentOption) && $foption==null){
$currentOption=WApplication::name('default',$itemId, $link );
}
$fullOption=($foption !=null)?'option=com_'.$foption.'&' : $currentOption;
$link=ltrim($link,'&');
$link=$absoluteLinkNewLink . JOOBI_INDEX.'?'.$fullOption . $link;
}else{
$link=$absoluteLinkNewLink . $link;
}
}elseif($index=='popup'){
$itemId=false;
if( substr($link, 0, 5 ) !='index'){
if(!isset($currentOption) && $foption==null)$currentOption=WApplication::name('default', false, $link );
$fullOption=($foption !=null)?'option=com_'.$foption.'&' : $currentOption;
$link=$absoluteLinkNewLink . JOOBI_INDEX2.'?'. $fullOption . $link.'&isPopUp=true';
}else{
$link=$absoluteLinkNewLink . $link.'&isPopUp=true';
}}elseif($index=='link'){
$link=$absoluteLinkNewLink . (isset($noIndex)?'' : JOOBI_INDEX.'?'). $link;
}
if($device=='mobile')$link .=URL_NO_FRAMEWORK.'&device=mobile';
if(!IS_ADMIN && ! $noSEF){
if($itemId){
if($itemId===true){
if( strpos($link, JOOBI_PAGEID_NAME.'=')===false){
if(!isset($item)){
$item=WPage::getPageId();}if(empty($item))$item=1;
$link .='&'.JOOBI_PAGEID_NAME.'='. $item;
}}else{
if($itemId !='none'){
if( is_numeric($itemId)){
$link .='&'.JOOBI_PAGEID_NAME.'='. $itemId;
}else{
if(!empty($foption))$itemId=$foption;
$item=CMSAPIPage::cmsGetComponentItemId($itemId );
if(empty($item))$item=WPage::getPageId();
$link .='&'.JOOBI_PAGEID_NAME.'='. $item;}}}
}
if( WPref::load('PLIBRARY_NODE_SSLFE')){
$SSL=true;
}
$link=rtrim($link,'&');
if( substr($link, 0, strlen(JOOBI_SITE))==JOOBI_SITE){
$subLink=substr($link, strlen(JOOBI_SITE));
$url=($itemId )?CMSAPIPage::cmsRoute($subLink, $SSL ) : $subLink;
static $pathOnly=null;
if(!isset($pathOnly))$pathOnly=JURI::root(true);
if(!empty($pathOnly)){
$pathOnlyLen=strlen($pathOnly);
if( substr($url, 0, $pathOnlyLen) ==$pathOnly)$url=substr($url, $pathOnlyLen );
}
$url=ltrim($url, '/');
if( substr($url, 0, 4 ) !='http'){
$url=JOOBI_SITE . $url;
}
}else{
$url=($itemId )?CMSAPIPage::cmsRoute($link, $SSL ) : $link;
}return $url;
}else{
if(!empty($showMe )) debug( 7888229, $link );
$url=rtrim($link, '&');
return $url;
}
}
public static function createPopUpLink($url,$text,$x=550,$y=400,$className='',$idName='',$title='',$justNormalLink=false,$extras=''){
if(empty($url)) return $text;
if(!empty($title))$title=' title="'.WGlobals::filter($title, 'string'). '"';
if(!empty($idName))$idName=' id="'.$idName. '"';
if($justNormalLink){
$relPop='';
if(!empty($className))$className=' class="'.$className. '"';
}else{
if( IS_ADMIN){
if(strpos($x, '%') !==false){
$x_pr=str_replace ("%", "", $x);
if($x_pr > 100)$x_pr=100;
if($x_pr < 20)$x_pr=20;
$x=JOOBI_JS_APP_NAME.'.wdt('.$x_pr.')';
}
if(strpos($y, '%') !==false){
$y_pr=str_replace ("%", "", $y);
if($y_pr > 100)$y_pr=100;
if($y_pr < 20)$y_pr=20;
$y=JOOBI_JS_APP_NAME.'.hgt('.$y_pr.')';
}
JHtml::_('behavior.modal','a.modal');
if(!empty($className))$className=' '.$className;
$relPop=' class="modal'.$className.'" rel="{handler: \'iframe\', size:{x:'.$x.',y:'.$y.'}}"';
return '<a href="'.$url.'"'.$relPop . $title. $idName . $className . $extras.'>'.$text.'</a>';
}else{
$className=' class="'.$className.'"';
$target=self::createPopUpRelTag($x, $y );
$htnl='<a href="'.$url.'"'.$className . $idName . $title . $target . $extras.'>'.$text.'</a>';
return $htnl;
}
}
return '<a href="'.$url.'"'.$relPop . $title. $idName . $className . $extras.'>'.$text.'</a>';
}
public static function createPopUpRelTag($x=550,$y=400){
if( IS_ADMIN){
if(strpos($x, '%') !==false){
$x_pr=str_replace ("%", "", $x);
if($x_pr > 100)$x_pr=100;
if($x_pr < 20)$x_pr=20;
$x=JOOBI_JS_APP_NAME.'.wdt('.$x_pr.')';
}
if(strpos($y, '%') !==false){
$y_pr=str_replace ("%", "", $y);
if($y_pr > 100)$y_pr=100;
if($y_pr < 20)$y_pr=20;
$y=JOOBI_JS_APP_NAME.'.hgt('.$y_pr.')';
}
JHtml::_('behavior.modal','a.modal');
$relPop=' class="modal" rel="{handler: \'iframe\', size:{x:'.$x.',y:'.$y.'}}"';
return $relPop;
}
trim($x);
trim($y);
$target='';
if(!empty($x)){
if( strpos($x, '%') !==false){
$x=substr($x, 0, -1 );
$target .=' data-pwidth="'.$x.'"';
}else{
$target .=' data-width="'.$x.'"';
}}
if(!empty($y)){
if( strpos($y, '%') !==false){
$y=substr($y, 0, -1 );
$y -=5;$target .=' data-pheight="'.$y.'"';
}else{
$target .=' data-height="'.$y.'"';
}}
if(!self::$_popOnlyOnce ) return ' data-target="#wzpOpUp" data-toggle="modal"'.$target;
$js="
jQuery('a[data-toggle=\"modal\"]').on('click', function(e){
var target_modal=jQuery(e.currentTarget).data('target');
var rmtC=e.currentTarget.href;
var hPX=jQuery(e.currentTarget).data('height');
var wPX=jQuery(e.currentTarget).data('width');
var modal=jQuery(target_modal);
var modalBody=jQuery(target_modal + ' .modal-body');
modal.on('show.bs.modal', function (){
modalBody.find('iframe').attr('src',rmtC);
if( hPX==undefined){
var pHeight=jQuery(e.currentTarget).data('pheight');
if( pHeight==undefined){
hPX=500;
}else{
pHeight=(pHeight>75)?75 : pHeight;
var hSC=document.compatMode=='CSS1Compat' && !window.opera?document.documentElement.clientHeight:document.body.clientHeight;
hPX=Math.ceil((pHeight*hSC)/100);
}
}
if( wPX==undefined){
var pWidth=jQuery(e.currentTarget).data('pwidth');
if( pWidth==undefined){
wPX=500;
}else{
var wSC=document.compatMode=='CSS1Compat' && !window.opera?document.documentElement.clientWidth:document.body.clientWidth;
wPX=Math.ceil((pWidth*wSC)/100);
}
}
jQuery('.modal-dialog').width( wPX );
jQuery('.modal-body').css('max-height',hPX+'px');
var iFrameHeight=jQuery('.modal-body').children()[0];
jQuery(iFrameHeight).css('height',(hPX-36)+'px');
}).modal();
return false;
});
";
WPage::addJSScript($js );
self::$_popOnlyOnce=false;
$html='<div id="wzpOpUp" class="modal fade">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-body">
<iframe width="99.6%" height="400px" frameborder="0" src=""></iframe>
</div>
<div class="modal-footer">
<button type="button" class="btn btn-warning" data-dismiss="modal"><i class="fa fa-times-circle"></i>'.WText::t('1228820287MBVC'). '</button>
</div>
</div>
</div>
</div>';
WView::popupMemory($html );
return ' data-target="#wzpOpUp" data-toggle="modal"'.$target;
}
public static function cmsGetComponentItemId($component,$view=''){
static $resultA=array();
$key=$component.'|'.$view;
if(!isset($resultA[$key] )){
$menuM=WTable::get('menu','','id');
if(empty($view)){
$menuM->whereE('link','index.php?option=com_'. $component );
}else{
$menuM->where('link','LIKE', "$view" );
}$menuM->whereE('type','component');
$menuM->whereE('client_id','0');
$menuM->whereE('published', 1 );
$nowResult=$menuM->load('lr','id');
$resultA[$key]=(!empty($nowResult)?$nowResult : true);
}
return $resultA[$key];
}
public static function getPageId($page='',$task=''){
static $itemID=null;
if(!isset($itemID))$itemID=WGlobals::get( JOOBI_PAGEID_NAME );
$wPageID=$itemID;
if(!empty($page )){
$controller=WGlobals::get('controller');
if($controller !=$page){
$wPageID=WPage::getSpecificItemId($page, $task );
}
}
if(empty($wPageID)){
if(!empty($page))$wPageID=WPage::getSpecificItemId($page, $task );
if(empty($wPageID)){
$wPageID=WPref::load('PLIBRARY_NODE_SHOWITEMID');
}}
return $wPageID;
}
public static function getSpecificItemId($controller='',$task=''){
static $resultA=array();
if(empty($controller)) return WPage::getPageId();
$key=$controller.'|'.$task;
if(!isset($resultA[$key] )){
$useMultipleLangENG=WPref::load('PLIBRARY_NODE_MULTILANGENG');
$string='controller='.$controller;
if(!empty($task ))$string .='&task='.$task;
$menuM=WTable::get('menu','','id');
$menuM->where('link','LIKE', "%$string" );
if($useMultipleLangENG){
$code=WLanguage::get( WUsers::get('lgid'), 'code');
$menuM->where('language','LIKE', "%$code" );
}$menuM->whereE('type','component');
$menuM->whereE('client_id','0');
$menuM->whereE('published', 1 );
$nowResult=$menuM->load('lr','id');
if(empty($nowResult)){
$menuM->where('link','LIKE', "%$string%" );
if($useMultipleLangENG){
$code=WLanguage::get( WUsers::get('lgid'), 'code');
$menuM->where('language','LIKE', "%$code" );
}$menuM->whereE('type','component');
$menuM->whereE('client_id','0');
$menuM->whereE('published', 1 );
$nowResult=$menuM->load('lr','id');
}
$resultA[$key]=(!empty($nowResult)?$nowResult : true);
}
return $resultA[$key];
}
public static function cmsGetLinkBasedItemId($itemid){
if(empty($itemid) || !is_numeric($itemid)) return false;
static $linkA=array();
if(!isset($linkA[$itemid])){
$menuM=WTable::get('menu','','id');
$menuM->whereE('id',$itemid );
$result=$menuM->load('lr','link');
$linkA[$itemid]=(empty($result)?true : $result );
}
return ($linkA[$itemid]===true?0 : $linkA[$itemid] );
}
public static function refreshFrameworkMenu($wid=null,$action='',$recursive=false){
$libraryCMSMenuC=WAddon::get('api.'.JOOBI_FRAMEWORK.'.cmsmenu');
$libraryCMSMenuC->cmsLinks($wid, $action, $recursive );
return true;
}
public static function jsPreload(){
$isPopUp=WGlobals::get('is_popup', false, 'global');
if( IS_ADMIN && !$isPopUp){
$js='var submenu=document.getElementById("submenu-box");if(submenu) submenu.style.display=\'none\';';
$js.='var toolmenu=document.getElementById("toolbar-box");if(toolmenu) toolmenu.style.display=\'none\';';
WPage::addJSScript($js);
}
return true;
}
public static function includeMootools(){
}
public static function includejQuery(){
JHtml::_('jquery.framework');
}
public static function includeRootScript(){
static $includeRootscript=false;
if(!$includeRootscript){
JHtml::_('jquery.framework');
$path=WPage::fileLocation('js','js/rootscript.1.2.js','api');
WPage::addScript($path );
$path=WPage::fileLocation('js','js/themescript.1.2.js');
WPage::addScript($path );
$includeRootscript=true;
}}
public static function includeJoobiBox(){
}
public static function interpretURL($segmentsA){
$seftype=WPref::load('PMAIN_NODE_SEFTYPE');
if( 1==count($segmentsA) && 9==$seftype){
$shortLinkO=WModel::getElementData('main.sef', str_replace(':','-',$segmentsA[0] ));
if(!empty($shortLinkO)){
$vars=array();
$vars['controller']=$shortLinkO->controller;
if(!empty($shortLinkO->task))$vars['task']=$shortLinkO->task;
if(!empty($shortLinkO->eid))$vars['eid']=$shortLinkO->eid;
if(!empty($shortLinkO->lgid)){
}
return $vars;
}
}
$vars=array();
$vars['controller']=str_replace(':','-',$segmentsA[0]);
$i=1;
while(!empty($segmentsA[$i])){
WPage::parseURL($segmentsA[$i], $vars );
$i++;
}
WGlobals::set('MainURL',$vars, 'global');
return $vars;
}
public static function buildURL(&$query){
if(!empty($query[JOOBI_PAGEID_NAME]) && empty($query['controller'])){
return array();
}
$segmentsA=array();
if(!empty($query['controller'])){
$myControllerA=explode('-',$query['controller'] );
$myController=array_shift($myControllerA );
$mySEFC=WClass::get($myController. '.sef', null, 'class', false);
if(!empty($mySEFC) && method_exists($mySEFC, 'buildSEF')){
$segmentsA=$mySEFC->buildSEF($query );
return $segmentsA;
}
}
if(!empty($query['controller'])){
$segmentsA[]=$query['controller'];
unset($query['controller']);
}if(!empty($query['task'])){
$segmentsA[]=$query['task'];
unset($query['task']);
}if(!empty($query['eid'])){
$segmentsA[]=$query['eid'];
unset($query['eid']);
}if(!empty($query['type'])){
$segmentsA[]=$query['type'];
unset($query['type']);
}
return $segmentsA;
}
public static function parseURL($string,&$vars){
$alreadyDone=false;
if(!empty($vars['controller'])){
$myControllerA=explode('-',$vars['controller'] );
$myController=array_shift($myControllerA );
$mySEFC=WClass::get($myController. '.sef', null, 'class', false);
if(!empty($mySEFC)){
$alreadyDone=$mySEFC->parseSEF($vars, $string );
}
}
if(!$alreadyDone){
if( is_numeric($string)){
$vars['eid']=$string;
}else{if( substr($string, 0, 7)==JOOBI_PAGEID_NAME.':'){
$vars[JOOBI_PAGEID_NAME]=substr($string, 7 );
}else{$vars['task']=$string;
}}
}
}
public static function completeURL($url='',$popup=null,$raw=false){
return self::linkNoSEF($url, $popup, $raw );
}
public static function linkNoSEF($url='',$type='standard'){
if('standard'==$type && WGlobals::get('is_popup', true, 'global'))$type='popup';
$url=trim($url);
$raw=false;
switch($type){
case 'raw':
case 'ajax':
$raw=true;
case 'popup':
if( strpos($url, 'option=')===false){
if( strpos($url, '?')===false){
$url='option='.WApplication::name('com'). '&'.$url;
}else{
$url .='option='.WApplication::name('com');
}}
if(!empty($url) && substr($url, 0, 1 ) !='?')$url='?'.$url;
$finalURL=JOOBI_INDEX2 . ((empty($url))?'?' : $url.'&'). 'tmpl=component'. ($raw?'&type=raw' : '');
break;
case 'standard':
default:
$finalURL=JOOBI_INDEX . $url;
break;
}
return $finalURL;
}
public static function formURL($option='',$controller=''){
if(empty($option))$option=WApplication::name('short');
if(!empty($controller))$controller='&controller='.$controller;
if( substr($option, 0, 4 ) !='com_')$option='com_'.$option;
return JOOBI_SITE.'index.php?option='.$option . $controller;
}
public static function clearCache($folder=''){
static $done=false;
if($done ) return;
$extensionHelperC=WCache::get();
$extensionHelperC->resetCache('page');
$done=true;
}
}
abstract class WApplication extends APIApplication {
public static $cmsName='joomla';
public static $ID=12;
public static function getFrameworkName(){
return self::$cmsName;
}
public static function getAppLink($app=''){
if(empty($app))$app=WApplication::name('short');
return 'com_'.$app;
}
public static function getApp($useDefault=true){
static $app=null;
if(isset($app)) return $app;
$url=WGlobals::get( JOOBI_URLAPP_PAGE, '', null, 'namekey');
if($useDefault && empty($url)){
$url='com_'.JOOBI_MAIN_APP;
}
$app=strtolower( substr($url, 4 ));
return $app;
}
public static function name($short='default',$wPageID=null,$linkController=null){
$myOption=WApplication::getApp();
$joptionVal=array('jcommunity','jiptracker','jnotebook','jprojects','jlinks','jdesign','jfaq','jdiscuss','jservicesprovider','jdefender','infusionstats','jurlauncher','jcenter','japps',
'jstore','jfeedback','jcomment','jtickets','jcloner','jaffiliates','japplication','jbackup','jscheduler','jstudio','jmobile','jmarket','jauction','jlottery','jvouchers',
 'jsubscription','jtestcases','jcontacts','jcatalog','jdownloads','jdloads','jclassifieds','jdatabase','jtodo','jchecklist','jdocumentation','jdistribution','jbounceback','jdictionary','jforum','jlicense','jnewsletters','jcampaign','joomfusion');
if(!in_array($myOption, $joptionVal ) || ( !IS_ADMIN && $myOption==JOOBI_MAIN_APP )){
$myOption=JOOBI_MAIN_APP;
$tryController=true;
if(!empty($wPageID) && $wPageID !==true){
$link=CMSAPIPage::cmsGetLinkBasedItemId($wPageID );
if(!empty($link)){
$link=str_replace('&amp;','&',$link );
$pos=strpos($link, 'option=com_');
if($pos !==false){
$myOption=substr($link, $pos + 11 );
$pos=strpos($myOption, '&');
if($pos !==false){
$myOption=substr($myOption, 0, $pos );
}}
if(!in_array($myOption, $joptionVal )){
$myOption=JOOBI_MAIN_APP;
}else{
$tryController=false;
}
}
}
if($tryController && !empty($linkController)){
$linkController=str_replace('&amp;','&',$linkController );
$findControllerA=explode('&',$linkController );
if(!empty($findControllerA)){
foreach($findControllerA as $oneControl){
if( substr($oneControl, 0, 11 )=='controller='){
$conA=explode('=',$oneControl );
$myController=$conA[1];
$conAlmostA=explode('-',$myController );
if( count($conAlmostA) > 1){
$myController=$conAlmostA[0];
}else{
$conAlmostA=explode('.',$myController );
if( count($conAlmostA) > 1){
$myController=$conAlmostA[0];
}}break;
}}
if(!empty($myController)){
static $allDependsControl=array();
$myController;
$WIDController=WExtension::get($myController.'.node','wid');
if(!isset($allDependsControl[$WIDController])){
$allDependsControl[$WIDController]=JOOBI_MAIN_APP; $appsDependencyM=WModel::get('install.appsdependency');
$appsDependencyM->makeLJ('apps.userinfos','wid');
$appsDependencyM->makeLJ('apps','wid','wid', 1, 2 );
$appsDependencyM->select('namekey', 2 );
$appsDependencyM->whereE('ref_wid',$WIDController );
$appsDependencyM->orderBy('level','DESC', 1 );
$appsDependencyM->orderBy('wid','DESC', 0 );
$allDependsA=$appsDependencyM->load('lra');
if(!empty($allDependsA)){
foreach($allDependsA as $oneDepend){
$myComponentA=explode('.',$oneDepend );
$isEnabled=WApplication::isEnabled($myComponentA[0] );
if($isEnabled){
$myOption=$myComponentA[0];
if(!in_array($myOption, $joptionVal ))$myOption=JOOBI_MAIN_APP;
else $allDependsControl[$WIDController]=$myComponentA[0];
break;
}}
}
}else{
$myOption=$allDependsControl[$WIDController];
}}
}
}
}
switch($short){
case 'default':
return  'option=com_'.$myOption.'&';
break;
case 'com':
return 'com_'.$myOption;
break;
case 'short':
return $myOption;
break;
case 'application':
return $myOption.'.application';
break;
case 'wid':
return WExtension::get($myOption.'.application','wid');
break;
default:
return  'option=com_'.$myOption.'&';
break;
}
}
public static function mainLanguage($return='lgid',$force=false,$suggestedLang=array(),$location='site'){
static $lang=null;
if(empty($lang) || $force){
$langCode=array( APIApplication::cmsMainLang($location ));
if(!empty($langCode)){
$langCode[]=substr($langCode[0], 0, 2 );
$availableLanguageA=WApplication::availLanguages( array('lgid','name','code','locale'));
$foundLanguage=false;
if(!empty($availableLanguageA )){
foreach($langCode as $oneLGCode){
foreach($availableLanguageA as $availLang){
if(!empty($availLang->code) && $availLang->code==$oneLGCode){
$foundLanguage=true;
$lang=$availLang;
break;
}}if($foundLanguage ) break;
}}
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
switch($site){
case 'current':
$results[$key.$site]=WApplication::_getLanguages($map, JPATH_BASE );
break;
case 'admin':
$results[$key.$site]=WApplication::_getLanguages($map, JPATH_ROOT.DS.'administrator');
break;
case 'site':
$results[$key.$site]=WApplication::_getLanguages($map, JPATH_ROOT );
break;
case 'all':
default:
$array1=WApplication::_getLanguages($map, JPATH_ROOT );
$array2=WApplication::_getLanguages($map, JPATH_ROOT.DS.'administrator');
if(is_array($map)){
$choosenMap=array_shift($map);
foreach($array1 as $obj1){
$array3=$array2;
foreach($array3 as $key3=> $obj3){
if($obj1->$choosenMap==$obj3->$choosenMap){
unset($array2[$key3]);
break;
}}}
if(!empty($array2 )){
foreach($array2 as $obj2){
$array1[]=$obj2;
}}$results[$key.$site]=$array1;
}else{
$results[$key.$site]=array_unique( array_merge($array1, $array2 ));
}break;
}
}
return $results[$key.$site];
}
private static function _getLanguages($map,$path){
static $results=array();
$languages=APIApplication::cmsAvailLang($path );
$bool=( WPref::load('PLIBRARY_NODE_EXTLANG'))?true : false;
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
if(isset($oneLnag->$myMap))$obj->$myMap=$oneLnag->$myMap;
}$a[]=$obj;
}return $a;
}else{
}
}
public static function setWidget($object){
if(!isset($object->name)){
$message=WMessage::get();
$message->codeE('The name of the widget is not specified.');
return false;
}elseif(!isset($object->type)){
$message=WMessage::get();
$message->codeE('The type of the widget is not specified.');
return false;
}
$joobiWidget=(isset($object->joobi))?$object->joobi : true;
$cmsObject=null;
if(isset($object->publish))$cmsObject->published=$object->publish;
if(isset($object->access))$cmsObject->access=$object->access;
if(isset($object->ordering))$cmsObject->ordering=$object->ordering;
if(isset($object->core))$cmsObject->iscore=$object->core;
if(isset($object->params))$cmsObject->params=$object->params;
switch ( strtolower($object->type)){
case 'module' :
$name='module';
if(isset($object->region))$cmsObject->position=$object->region;
$cmsObject->$name='mod_'.$object->name;
$widgetM=WTable::get('modules','','id');
break;
case 'plugin' :
$name='element';
if(isset($object->location))$cmsObject->folder=$object->location;
$cmsObject->$name=$object->name;
$widgetM=WTable::get('extensions','','extension_id');
$widgetM->setVal('type','plugin');
break;
default :
$message=WMessage::get();
$message->codeE('The type specified does not exist in Joomla 3.0. :'.$object->type );
return false;
break;
}
foreach($cmsObject as $key=> $property){
if($key!=$name)$widgetM->setVal($key, $property);
}
$namekey=str_replace('.','_',$cmsObject->$name );
$widgetM->whereE($name, $namekey );
return $widgetM->update();
}
public static function createMenu($name,$menuParent,$link,$option,$client=1,$access=0,$level=0,$ordering=0,$param=null){
WLoadFile('helper', JOOBI_DS_NODE.'api'. DS.'addon' .DS . JOOBI_FRAMEWORK . DS );
$APIHelperC=new Api_Joomla30_Helper_addon;
return $APIHelperC->createMenu($name, $menuParent, $link, $option, $client, $access, $level, $ordering, $param );
}
public static function isEnabled($component,$strict=true,$addCom=true){
static $alreadyChecked=array();
if($addCom)$component='com_'.$component;
if( strpos($component, '.') !==false){
$component=str_replace('.','_',$component );
}
if(isset($alreadyChecked[$component])) return $alreadyChecked[$component];
$componentM=WModel::get('joomla.extensions');
$componentM->whereE('element',$component );
if($strict)$componentM->whereE('enabled', 1 );
$alreadyChecked[$component]=$componentM->exist();
return $alreadyChecked[$component];
}
public static function enable($extension,$value=1,$type=''){
$componentM=WModel::get('joomla.extensions');
$componentM->whereE('element',$extension );
if(!empty($type))$componentM->whereE('type',$type );
$componentM->setVal('enabled',$value );
$componentM->update();
return true;
}
public static function getComponents($option='',$column=''){
static $extensionsA=array();
$key=serialize($column);
if(!empty($extensionsA[$option][$key])) return $extensionsA[$option][$key];
$joomlaComponentsM=WModel::get('joomla.extensions');
if(empty($column)){
$joomlaComponentsM->select('*');
}elseif($column=='id'){
$joomlaComponentsM->select('extension_id', 0, 'id');
}else{
$joomlaComponentsM->select($column );
}
if(!empty($option))$joomlaComponentsM->whereE('element','com_'.$option );
$joomlaComponentsM->whereE('type','component');
$joomlaComponentsM->whereE('enabled', 1 );
$joomlaComponentsM->orderBy('ordering','asc');
$joomlaComponentsM->orderBy('name','asc');
$extensionsA[$option][$key]=$joomlaComponentsM->load('ol');
return $extensionsA[$option][$key];
}
public static function date($format=null,$time=null){
static $alreadyDoneA=array();
if(empty($time))$time=time();
if(empty($format))$format=WTools::dateFormat('date-number');
$key=$format.'-'.$time;
if(!empty($alreadyDoneA[$key] )) return $alreadyDoneA[$key];
jimport('joomla.utilities.date');
$dateO=new JDate($time );
$alreadyDoneA[$key]=$dateO->format( WGlobals::filter($format ));
return $alreadyDoneA[$key];
}
public static function dateOffset(){
$tz=new DateTimeZone(JFactory::getApplication()->getCfg('offset'));
$date=new JDate();
$date->setTimezone($tz);
$offSet=$date->getOffsetFromGMT();
return $offSet;
}
public static function stringToTime($date=null){
if(empty($date))$date=time();
jimport('joomla.utilities.date');
$dateO=new JDate($date );
return $dateO->toUnix();
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
class WApplication_joomla30 {public $input=null;
function __construct(){
$this->input=JFactory::getApplication()->input;
}
public static function getTemplate(){
$app=( IS_ADMIN )?JFactory::getApplication('JAdministrator') : JFactory::getApplication('JSite');
return $app->getTemplate();
}
function triggerEvent($a,$b){
}
public static function make($entrypoint,$params=null){
static $loadConfig=true;
if($loadConfig){
require_once( JOOBI_LIB_CORE.'define.php');
require_once( JOOBI_DS_NODE.'api'.DS.'addon'.DS.JOOBI_FRAMEWORK.DS.'api.php');
}
$processApplication=true;
$entryTypeText='';
$isPopUp=false;
if(empty($entrypoint)){
$processApplication=false;
}elseif($entrypoint=='install'){
$namekey='';
$extType=$entrypoint;
}else{
$mypath=explode( DS , strtolower($entrypoint ));
$filename=array_pop($mypath );
array_pop($mypath );
$typeExt=array_pop($mypath );
$filenameArray=explode('.' , $filename );
$filename=substr($filename, 0, -4 );
array_pop($filenameArray );
$fileElementsA=explode('_',$filenameArray[0] );
if(!empty($fileElementsA[2]) && $fileElementsA[2]=='plugin'){
$typeExt='plugin';
}$typeOfExtension=$filenameArray[count($filenameArray)-1];
$namekey=str_replace('_','.',$filename );
$entryTypeText=strtolower($typeExt);
switch($entryTypeText){
case 'modules':
$namekey=substr($namekey, 4 );
$extType='module';
WGlobals::set('is_popup', false, 'global', true);
break;
case 'plugins':
case 'plugin':
$extType='plugin';
WGlobals::set('is_popup', false, 'global', true);
break;
case 'components':
$namekey=str_replace('admin.','',$namekey );
$namekey=$namekey.'.application';
$extType='application';
if( WGlobals::get('isPopUp')){
$isPopUp=true;
}elseif(strpos( WGlobals::get('REQUEST_URI','','server'), 'isPopUp=true') !==false){
$isPopUp=true;
}elseif(strpos( WGlobals::get('HTTP_REFERER','','server'), 'isPopUp=true') !==false){
$isPopUp=true;
}elseif(strpos( WGlobals::get('SCRIPT_NAME','','server'), 'isPopUp=true') !==false){
$isPopUp=true;
}elseif(strpos( WGlobals::get('QUERY_STRING','','server'), 'isPopUp=true') !==false){
$isPopUp=true;
}elseif(strpos( WGlobals::get('REDIRECT_QUERY_STRING','','server'), 'isPopUp=true') !==false){
$isPopUp=true;
}else{
WGlobals::set('is_popup', false, 'global', true);
}
$crtrl=WGlobals::get('controller');
if(empty($crtrl)){
if( defined('JOOBI_REMOTE_ACCESS') && JOOBI_REMOTE_ACCESS){
$outputSpaceC=WClass::get('output.space');
$spaceO=$outputSpaceC->findSpace();
WGlobals::set('controller',$spaceO->controller );
$crtrl=$spaceO->controller;
}else{
WGlobals::set('controller', WApplication::getApp());
}}break;
default:
$processApplication=false;
WGlobals::set('is_popup', false, 'global', true);
break;
}$upPrefix=strtoupper($namekey );
}
if($isPopUp){
WGlobals::set('is_popup', true, 'global', true);
WGlobals::set('tmpl','component');
}
if($loadConfig){
define('JOOBI_CHARSET' , WPage::encoding());
require_once( JOOBI_DS_NODE.'api'.DS.'addon'.DS.JOOBI_FRAMEWORK.DS.'api.end.php');
$membersSesionC=WUser::session();
$membersSesionC->checkUserSession();
$loadConfig=false;
if( IS_ADMIN && !$isPopUp){
$task=WGlobals::get('task','', null, 'task');
if( strpos($task, 'edit') !==false || strpos($task, 'add') !==false || strpos($task, 'new') !==false){
WGlobals::set('hidemainmenu', 1 );
}else{
WGlobals::set('hidemainmenu', 0 );
}}}
WGet::loadLibrary();
WGlobals::set('resetForm','yes','global');
if($processApplication){
if( IS_ADMIN && $entryTypeText=='components'){
WPage::jsPreload();
}
$content=WGet::startApplication($extType, $namekey, $params );
if($entryTypeText=='components'){
define('WJOOMLA_COMPONENT', true);
}
}else{
$content='';
}
if($processApplication && $entryTypeText=='components'){
WPage::keepAlive(true);
}
$soundMusic=WGlobals::getSession('installRedirectInfo');
if( WPref::load('PLIBRARY_NODE_ENABLESOUND') && is_object($soundMusic) && !empty($soundMusic->alex) && $soundMusic->alex==WPage::linkAdmin(WGlobals::currentURL())){
$browser=WPage::browser('namekey');
$extension=($browser=='safari' || $browser=='msie')?'mp3' : 'ogg';
$URLBeep=WPref::load('PLIBRARY_NODE_CDNSERVER'). '/joobi/user/media/sounds/finish.'.$extension;
$content .='<audio autoplay="true" src="'.$URLBeep.'" preload="auto" autobuffer></audio>';
}
return $content;
}
}