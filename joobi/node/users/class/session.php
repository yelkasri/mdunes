<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Users_Session_class extends WClasses {
public function &checkUserSession($reset=false,$colID=''){
static $user=null;
if(!isset($user) || $reset){
if( class_exists('WUser'))$CMSid=WUser::cmsMyUser('id');
  elseif( class_exists('APIUser'))$CMSid=APIUser::cmsMyUser('id');
else $CMSid=0;
if(!isset($_SESSION['JoobiUser'] ) || $CMSid !=$_SESSION['JoobiUser']->id){
if($CMSid > 0){
$this->setUserSession($CMSid, $reset, $colID );
}else{
$this->setGuest();
}}
$user=&$_SESSION['JoobiUser'];
}
return $user;
}
public function reloadRoles(){
$uid=WUser::get('uid');
$rolid=WUser::get('rolid');
$rolids=WUser::roles($uid );
if(!empty($rolid) && !in_array($rolid, $rolids ))$rolids[]=$rolid;$_SESSION['JoobiUser']->rolids=$rolids;
WUser::get('','reset');
}
public function setUserSession($userId=null,$reset=false,$colID=''){
if(empty($userId))$userId=WUser::get('id');
$prefI=WPref::get('users.node', false, true, false);
$framekworkPrefBE=$prefI->getPref('framework_be', WApplication::getFrameworkName());
$framekworkPrefFE=$prefI->getPref('framework_fe', WApplication::getFrameworkName());
$frameworkUsed=IS_ADMIN?$framekworkPrefBE : $framekworkPrefFE;
if(empty($frameworkUsed))$frameworkUsed=JOOBI_FRAMEWORK;
$usersAddon=WAddon::get('users.'.$frameworkUsed );
$user=$usersAddon->getUser($userId, $colID, $reset );
if(empty($user)){
static $alreadySync=false;
if(!$alreadySync){
$alreadySync=true;
if( method_exists('WUser','syncUsers')){
WUser::syncUsers();
return $this->setUserSession($userId, $reset, $colID );
}}
$user=new stdClass;
$user->uid=0;
$user->id=0;
$user->fname='';
$user->lname='';
$user->mname='';
$user->visibility=231;$user->registered=-1;
$user->timezone=999;
$user->rolids=array(1);
$_SESSION['JoobiUser']=&$user;
if(!$alreadySync){
$message=WMessage::get();
$message->userE('1377734782BTNQ',array('$userId'=>$userId));
}
if(empty($user->name))$user->name='Guest';
}else{
$_SESSION['JoobiUser']=&$user;
$user->rolids=WUser::roles($user->uid );
if(!empty($user->rolid) && !in_array($user->rolid, $user->rolids ))$user->rolids[]=$user->rolid;
}
$this->completeInformation($user );
if(empty($user->firstName)){
$nameA=explode(' ',$user->name );
$user->firstName=array_shift($nameA );
$user->lastName=implode(' ',$nameA );
$user->middleName='';
}
if(!$reset && isset($user->uid)){
$extensionID=WGlobals::get('extensionID','','global','int');
if(empty($extensionID)){
$extensionID=WExtension::get('users.node','wid');
WGlobals::set('extensionID',$extensionID, 'global');
}
}
$this->_recordSession($user );
return $user;
}
public function setGuest(){
$user=new stdClass;
$user->id=0;
$user->uid=0;
$user->name='Guest'; $user->username='guest';
$user->registered=-1;
$user->visibility=231;$user->timezone=999;
$user->rolid=1;
$_SESSION['JoobiUser']=&$user;
$this->completeInformation($user );
$user->rolids=WUser::roles( 0 );
$this->_recordSession($user );
return 0;
}
function resetUser($cache=false){
unset($_SESSION['JoobiUser'] );
$this->checkUserSession(true);
}
public function completeInformation(&$user){
if(empty($user->curid)){
if( WExtension::exist('security.node') && WGlobals::checkCandy(50)){
$iptrackerLookupC=WClass::get('security.lookup');
$user->curid=$iptrackerLookupC->ipInfo( null, 'curid');
$currencyHelperC=WClass::get('currency.helper', null, 'class', false);
if(!empty($currencyHelperC) && ! $currencyHelperC->isAccepted($user->curid, true))$user->curid=0;
}
if(empty($user->curid)){
if(!defined('CURRENCY_USED')){
$currencyFormatC=WClass::get('currency.format', null, 'class', false);
if(!empty($currencyFormatC)){
if(!defined('PCURRENCY_NODE_PREMIUM')) WPref::get('currency.node', false, true, false);
$currencyFormatC->set();
$user->curid=( defined('CURRENCY_USED'))?CURRENCY_USED : 0;
}else{
$user->curid=0;
}
}else{
$user->curid=CURRENCY_USED;
}
}
}else{
$currentCURID=0;
$currencyFormatC=WClass::get('currency.format', null, 'class', false);
if( WUser::$ready && !empty($currencyFormatC)){
if(!defined('PCURRENCY_NODE_PREMIUM')) WPref::get('currency.node', false, true, false);
$currencyFormatC->set();
$currentCURID=( defined('CURRENCY_USED'))?CURRENCY_USED : 0;
}
if($currentCURID !=$user->curid){
$currencyHelperC=WClass::get('currency.helper', null, 'class', false);
$accepted=false;
if(!empty($currencyFormatC)){
$accepted=$currencyHelperC->isAccepted($user->curid, true);
}if(!$accepted)$user->curid=$currentCURID;
}
if(!defined('CURRENCY_USED')) define('CURRENCY_USED',$user->curid );
}
$user->_ip=$this->getIP();
$ipLookupC=WClass::get('security.lookup', null, 'class', false);
if( is_object($ipLookupC)){
$localization=$ipLookupC->detectIP($user->_ip );
}
if(!isset($user->timezone) || $user->timezone==999){
if(empty($localization)){
$user->timezone=WApplication::dateOffset();
$user->_tzExact=false;}else{
$user->timezone=(isset($localization->country->timezone)?$localization->country->timezone : WApplication::dateOffset());
$user->_tzExact=false;
}
}else{
$user->_tzExact=true;
}
if(empty($user->ctyid)){
if(!empty($localization->country->ctyid))$user->ctyid=$localization->country->ctyid;
}
if( WUser::$ready)$this->_defineLanguage($user );
if(empty($user->lgid))$user->lgid=WLanguage::get( APIApplication::cmsUserLang(), 'lgid', true);
if(empty($user->lgid))$user->lgid=1;
}
private function _defineLanguage(&$user){
$lgPriority=array();
$tempLGID=(!empty($user->lgid)?$user->lgid : 0 );
$user->lgid=1;
if( WUser::$ready ) WPref::get('library.node', false, true, false);
$user->lgid=$tempLGID;
$lgPriority[ 0 ]='url';
if( defined('PLIBRARY_NODE_LGIP')){
$lgPriorityFromNode=PLIBRARY_NODE_LGIP;
if(!empty($lgPriorityFromNode))$lgPriority[ PLIBRARY_NODE_LGIP ]='ip';
$lgPriorityFromNode=PLIBRARY_NODE_LGBROWSER;
if(!empty($lgPriorityFromNode))$lgPriority [ PLIBRARY_NODE_LGBROWSER ]='browser';
$lgPriorityFromNode=PLIBRARY_NODE_LGCMS;
if(!empty($lgPriorityFromNode))$lgPriority[ PLIBRARY_NODE_LGCMS ]='cms';
else {if( count($lgPriority) < 2)$lgPriority[ 1 ]='cms';
}}else{
$lgPriority[ 3 ]='ip';
$lgPriority [ 2 ]='browser';
$lgPriority[ 1 ]='cms';
}
if(!defined('PLIBRARY_NODE_MULTILANG')){
$user->lgid=1;
WPref::get('library.node', false, true, false);
}
if( IS_ADMIN && defined('PLIBRARY_NODE_ADMINENGLISH') && PLIBRARY_NODE_ADMINENGLISH){
$user->lgid=1;
return true;
}
if(!defined('PLIBRARY_NODE_MULTILANG') || ! PLIBRARY_NODE_MULTILANG){
$user->lgid=WApplication::userLanguage();
$this->_checkLanguage($user );
return true;
}
$lgPriority[99]='user';
ksort($lgPriority );
$langIds=array();
foreach($lgPriority as $type){
switch($type){
case 'url':
$code=WGlobals::get('lang');
if(!empty($code)){
$lgid=WLanguage::get($code,'lgid');
if(!empty($lgid)){
$langIds[]=$lgid;
}}break;
case 'user':
if(!empty($user->lgid)){
$langIds[]=$user->lgid;
}break;
case 'ip':
$localization=WGlobals::getSession('iptracker','localization', null );
if(!empty($localization->language)){
foreach($localization->language as $oneLang){
$langIds[]=$oneLang->lgid;
}}break;
case 'browser':
$lgs=$this->_checkClientLanguage();
if(!empty($lgs)){
$langIds=array_merge($langIds,$lgs);
}break;
case 'cms':
$language2Use=( IS_ADMIN )?'admin' : 'site';
$lgid=WApplication::mainLanguage('lgid', true, array(), $language2Use );if(!empty($lgid))$langIds[]=$lgid;
break;
default:
break;
}
}
$this->_checkLanguage($user, $langIds );
}
private function _checkLanguage(&$user,$langIds=array()){
if(empty($user->lgid)){
$user->lgid=WApplication::mainLanguage('lgid', true, $langIds );
}
if($user->lgid !=1){
$availableLang=WApplication::availLanguages('lgid');
if(empty($availableLang) || !in_array($user->lgid, $availableLang ))$user->lgid=WApplication::mainLanguage('lgid', true, $langIds );
$code=WLanguage::get($user->lgid,'code');
$code=substr($code, 0, 2 );
$modelsid=WModel::get('translation.'.$code, 'sid', null, false);
if(empty($modelsid)){
$user->lgid=1;
if( WRoles::isAdmin('manager')){
$message=WMessage::get();
$message->userE('1426609899QDEC',array('$code'=>$code));
}}
}
}
public function getIP(){
static $ip='';
if(empty($ip)){
$server=WGlobals::get('HTTP_X_FORWARDED_FOR','','server','string');
if( strlen($server)>6 ){
$ip=$server;
}else{
$server=WGlobals::get('HTTP_CLIENT_IP','','server','string');
if( strlen($server)>6 ){
$ip=$server;
}else{
$server=WGlobals::get('REMOTE_ADDR','','server','string');
if( strlen($server)>6){
$ip=$server;
}}}}
if( strpos($ip, ',') !==false){
$ipA=explode($ip,',');
$ip=trim((!empty($ipA[0]))?$ipA[0] : $ipA[1] );
}
return $this->validateIP($ip, false);
}
public function validateIP($ip,$checkLocalHost=true){
if( preg_match('/^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/',$ip )){
$parts=explode('.',$ip );
foreach($parts as $ip_parts){
if( intval($ip_parts) > 255 || intval($ip_parts) < 0 ) return false;
}
if($checkLocalHost){
if(( strpos($ip, 'localhost')===false
 && substr($ip, 0, 3 ) !='10.'
 && substr($ip, 0, 8 ) !='192.168.'
 && substr($ip, 0, 7 ) !='172.16.'
 && substr($ip, 0, 4 ) !='127.')){
 return $ip;
}else{
return false;
}
}
return $ip;
}else{
return false;
}
}
private function _recordSession($user){
$usersAddon=WAddon::get('api.'. JOOBI_FRAMEWORK.'.user');
$usersAddon->updateSession($user->uid, $user->_ip );
}
private function _checkClientLanguage(){
$languages=WGlobals::get('HTTP_ACCEPT_LANGUAGE','','server');
if(!empty($languages)){
$langcode=explode(';',$languages );
$myLang=array();
if(!empty($langcode)){
foreach($langcode as $keyc=> $valuec){
$langExpode=explode(',',$langcode[$keyc]);
if(!empty($langExpode )){
foreach($langExpode as $unoLg){
if( substr($unoLg, 0, 2) !='q=')$myLang[]=$unoLg;
}}}}}
if(empty($myLang)) return array();
$langM=WModel::get('library.languages');
$langM->whereIn('code',$myLang );
$langM->whereE('publish', 1 );
$langM->setLimit( 500 );
$langs=$langM->load('ol',array('lgid','code'));
if(!empty($langs)){
$langArray=array();
foreach($myLang as $lg){
foreach($langs as $lgObj){
if($lgObj->code==$lg){
$langArray[]=(int)$lgObj->lgid;
break;
}}}
return $langArray;
}else{
return array();
}
}
}