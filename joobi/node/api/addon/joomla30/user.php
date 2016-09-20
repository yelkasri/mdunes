<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Api_Joomla30_User_addon {
static private $_onlySyncOnceA=array();
public function checkPlugin(){
WApplication::enable('plg_users_user_plugin', 1, 'plugin');
}
public function goLogin($itemId=null,$message=''){
$currentURL=WView::getURI(true);
WGlobals::setSession('Joobi_Users_ComeBack','JoomlaUsers',$currentURL );
WGlobals::setSession('Joobi_Users_ComeBackCount','JoomlaUsers', 1 );
$link=JOOBI_SITE.'index.php?option=com_users&view=login';
$isPopUp=WGlobals::get('is_popup', false, 'global');
if($isPopUp){
$link .=URL_NO_FRAMEWORK;
$link .='&isPopUp=true';
$itemId=false;
}
WPages::redirect($link, $itemId, false);
}
public function goRegister($itemId=null){
WPages::redirect( JOOBI_SITE.'index.php?option=com_users&view=registration',$itemId, false);
}
public function addUserRedirect(){
$option=WApplication::getApp();
WGlobals::setSession('Joobi_Users_ComeBack','JoomlaUsers',$option );
WPages::redirect('index.php?option=com_users&task=user.add');
}
public function showUserProfile($eid,$onlyLink=false){
$cid=WUser::get('id',$eid, 'uid');
$link='index.php?option=com_users&view=profile';
if($onlyLink ) return $link;
WPages::redirect($link );
}
public function editUserRedirect($eid,$onlyLink=false){
$option=WApplication::getApp();
WGlobals::setSession('Joobi_Users_ComeBack','JoomlaUsers',$option );
$cid=WUser::get('id',$eid, 'uid');
if( IS_ADMIN){
$link='index.php?option=com_users&task=user.edit&id='.$cid;
}else{
$link='index.php?option=com_users&view=profile&layout=edit';
}
if($onlyLink ) return $link;
if(empty($cid))$this->addUserRedirect();
WPages::redirect($link );
}
public function ghostAccount($email,$password,$name='',$username='',$automaticLogin=false,$createJoobiUser=true,$sendPwd=false,$extraPrams=null){
jimport('joomla.application.component.helper');
$config=JFactory::getConfig();
$user=new JUser;
$data=array();
if(empty($name))$name=$email;
if(empty($username))$username=$email;
$data['email']=$email;
$data['name']=$name;
$data['username']=$username;
$newUser=false;
if(!empty($extraPrams->id)){
$data['id']=$extraPrams->id;
if(!empty($extraPrams->blocked))$data['block']=$extraPrams->blocked;
else $data['block']=0;
$newUser=false;
}else{
if(!empty($extraPrams->registerdate))$data['registerDate']=date('Y-m-d H:i:s',$extraPrams->registerdate );
else $data['registerDate']=date('Y-m-d H:i:s');
if(!empty($extraPrams->activation))$data['activation']=$extraPrams->activation;
else $data['activation']='';
if(isset($extraPrams->blocked))$data['block']=$extraPrams->blocked;
$newUser=true;
}
if(empty($data['id']))$data['lastvisitDate']='';
if(!empty($password)){
$data['password']=$password;
$data['password2']=$password;
}
if(isset($extraPrams->lgid)){
if(!empty($extraPrams->lgid)){
$localeLangauge=WLanguage::get($extraPrams->lgid, 'locale');
}else{
$localeLangauge=WLanguage::get( WUser::get('lgid'), 'locale');
}}
if(!empty($localeLangauge)){
$explodeLocaleA=explode(',',$localeLangauge );
$joomlaCode='';
foreach($explodeLocaleA as $oneLocale){
if( strlen($oneLocale)==5){
$joomlaCode=$oneLocale;
}}if(empty($joomlaCode)){
$joomlaCode=substr($explodeLocaleA[0], 0, 5 );
}
$joomlaCode=str_replace('_','-',$joomlaCode );
$obj=new stdClass;
$obj->language=$joomlaCode;
$data['params']=$obj;
}
if($newUser){
if(!empty($extraPrams->rolid)){
$CMSaddon=WAddon::get('api.'.JOOBI_FRAMEWORK.'.acl');
$newUsertype=$CMSaddon->getCMSRole($extraPrams->rolid );
if(empty($newUsertype)){
$newUsertype=$config->get('new_usertype');
if(empty($newUsertype))$newUsertype=2;
}
}else{
$newUsertype=$config->get('new_usertype');
if(empty($newUsertype))$newUsertype=2;
}
if(isset($newUsertype))$data['groups']=array($newUsertype );
}
if(!empty($data['id'] )){
$user->id=$data['id'];
}
if(!$user->bind($data)){
$user->setError( JText::sprintf('COM_USERS_REGISTRATION_BIND_FAILED',$user->getError()) );
return 'COM_USERS_REGISTRATION_BIND_FAILED'.' '.$user->getError();
}
JPluginHelper::importPlugin('user');
if(!$user->save()){
$joomlaUsersM=WModel::get('joomla.users');
$joomlaUsersM->whereE('email',$email );
$id=$joomlaUsersM->load('lr','id');
if(!$createJoobiUser ) return $id;
$usersM=WModel::get('users');
$usersM->email=$email;
$usersM->id=$id;
$usersM->name=$name;
$usersM->username=$username;
$usersM->returnId();
$usersM->updateFrameworkUser(false);$usersM->emailNewPassword($sendPwd );
$usersM->save();
if(!empty($usersM->uid )){
return true;
}
$user->setError( JText::sprintf('COM_USERS_REGISTRATION_SAVE_FAILED',$user->getError()) );
return 'COM_USERS_REGISTRATION_SAVE_FAILED'.' '.$user->getError();
}
if($automaticLogin)$this->automaticLogin($user->username, $user->password_clear );
if(!$createJoobiUser){
return $user->id;
}
return true;
}
public function automaticLogin($username,$password,$url='',$pageId=0){
$app=JFactory::getApplication();
$options=array();
$options['remember']=JRequest::getBool('remember', true);
$options['silent']=true;
$credentials=array();
$credentials['username']=$username;
$credentials['password']=$password;
$error=$app->login($credentials, $options );
$id=JFactory::getUser()->id;
return $id;
}
public function logout(){
$app=JFactory::getApplication();
$error=$app->logout();
}
public function onAfterInitialise(){
if( IS_ADMIN){
$option=WApplication::getApp();
if($option=='users'){
$task=WGlobals::get('view');
$goTo=WGlobals::getSession('Joobi_Users_ComeBack','JoomlaUsers','');
if($task=='users'){
$layout=WGlobals::get('layout');
WGlobals::setSession('Joobi_Users_ComeBack','JoomlaUsers','');
if(!empty($layout)){
if(!empty($goTo)){
WGlobals::setSession('Joobi_Users_ComeBack','JoomlaUsers','');
WPages::redirect('index.php?option='.$goTo.'&controller=users');
}}}
}
}else{$comeBackHere=WGlobals::getSession('Joobi_Users_ComeBack','JoomlaUsers','');
if(!empty($comeBackHere)){
$comeBackHereCount=WGlobals::getSession('Joobi_Users_ComeBackCount','JoomlaUsers', 0 );
if($comeBackHereCount > 1){
$option=WApplication::getApp();
$view=WGlobals::get('view');
$task=WGlobals::get('task','', null, 'task');
$j16Fix=WGlobals::getSession('Joobi_Users_J16Fix','JoomlaUsers', false);
if($j16Fix || ($option=='users' && $view=='profile')){
WGlobals::setSession('Joobi_Users_ComeBack','JoomlaUsers','');
WGlobals::setSession('Joobi_Users_ComeBackCount','JoomlaUsers', 0 );
WGlobals::setSession('Joobi_Users_J16Fix','JoomlaUsers', false);
WPages::redirect($comeBackHere );
}elseif($option=='users'){$comeBackHereCount++;
WGlobals::setSession('Joobi_Users_ComeBackCount','JoomlaUsers',$comeBackHereCount );
}elseif($task=='user.login'){$comeBackHereCount++;
WGlobals::setSession('Joobi_Users_J16Fix','JoomlaUsers', true);
}
}else{ $option=WApplication::getApp();
if($option=='users'){$comeBackHereCount++;
WGlobals::setSession('Joobi_Users_ComeBackCount','JoomlaUsers',$comeBackHereCount );
}else{
}
}
}
}
}
public function syncUser($user,$isnew,$success,$msg){
if(!empty( self::$_onlySyncOnceA[$user['id']] )) return false;
$syncUser=WGlobals::get('syncUserFlag', false, 'global');
if($syncUser ) return false;
 if($success===false) return false;
 self::$_onlySyncOnceA[$user['id']]=true;
$usersM=WModel::get('users');
 if(!$isnew){
$usersM->whereE('id',$user['id'] );
$memberO=$usersM->load('o',array('id','uid','rolid'));
 }else{
  $contactExist=WExtension::exist('contacts');
 if($contactExist){
 $usersM->whereE('email',$user['email'] );
$memberO=$usersM->load('o',array('id','uid','rolid'));
if(empty($memberO->id )){
if(!is_object($memberO))$memberO=new stdClass;
$memberO->id=$user['id'];
} }else{
  $memberO=new stdClass;
 } }
if(empty($memberO->uid)){$usersM->uid=0;$usersM->id=$user['id'];
if(!empty($user['registerDate']))$usersM->registerdate=strtotime($user['registerDate'] );
else $usersM->registerdate=time();
if(!empty($user['lastvisitDate']))$usersM->login=strtotime($user['lastvisitDate'] );
else $usersM->login=time();
$usersM->_uniqueUpdate=true;
$usersM->confirmed=1;
$CMSaddon=WAddon::get('api.'.JOOBI_FRAMEWORK.'.acl');
$CMSrolid=$CMSaddon->updateRoleFromFramework($user );
$usersM->rolid=$CMSrolid;
$newMember=true;
}else{
$newMember=false;
$usersM->id=$memberO->id;
$usersM->uid=$memberO->uid;
if(!empty($user['groups'])){
$CMSaddon=WAddon::get('api.'.JOOBI_FRAMEWORK.'.acl');
$CMSrolid=$CMSaddon->updateRoleFromFramework($user );
if($CMSrolid !=$memberO->rolid){
$usersM->rolid=$CMSrolid;
}
}
}
if(!empty($user['password_clear'])){
$usersEncryptC=WClass::get('users.register');
$usersM->password=$usersEncryptC->generateHashPassword($user['password_clear'] );
}
$usersM->name=$user['name'];
$usersM->username=$user['username'];
$usersM->email=$user['email'];
$usersM->blocked=(isset($user['block'])?$user['block'] : 0 );
if(!empty($user['lastvisitDate']) && $user['lastvisitDate']!='0000-00-00 00:00:00')$usersM->login=strtotime($user['lastvisitDate'] );
$j16Params=$user['params'];
if(!empty($j16Params)){
$j16Params=trim($j16Params );
$j16Params=substr($j16Params, 1, strlen($j16Params) -2 );
$j16ParamsA=explode(',',$j16Params );
if(!empty($j16ParamsA[0])){
foreach($j16ParamsA as $onej16P){
$onej16PA=explode(':',$onej16P );
$valueP=trim($onej16PA[1],'"');
if($onej16PA[0]=='"language"' && !empty($valueP))$usersM->lgid=WLanguage::get($valueP, 'lgid');
}
}
}
if(!empty($user['params'])){
$jsonParamsO=json_decode($user['params'] );
if(!empty($jsonParamsO->language)){
$jomLGID=WLanguage::get($jsonParamsO->language, 'lgid');
$availableLanguageA=WApplication::availLanguages('lgid');
if(!in_array($jomLGID, $availableLanguageA )){
$langSplitA=explode('-',$jsonParamsO->language );
$jomLGID=WLanguage::get($langSplitA[0], 'lgid');
if(!in_array($jomLGID, $availableLanguageA )){
$jomLGID=0;
}}
$usersM->lgid=$jomLGID;
}
if(!empty($jsonParamsO->timezone)){
$tzString=$jsonParamsO->timezone;
$usersTimezoneC=WClass::get('users.timezone');
$tzKey=$usersTimezoneC->getTimeZoneKey($tzString);
$usersM->timezone=$tzKey;
 } 
}
$userSessionC=WUser::session();
$userSessionC->completeInformation($usersM );
if(!empty($usersM->uid))$uid=$usersM->uid;
$rolid=$usersM->rolid;
$usersM->returnId();
$userSyncSendPwd=WGlobals::get('userSyncSendPwd', false, 'global');
if($userSyncSendPwd)$usersM->emailNewPassword($userSyncSendPwd );
$usersM->updateFrameworkUser(false);
$usersM->setEmailValidation($isnew );
$status=$usersM->save();
if(empty($CMSrolid)){
if(!isset($CMSaddon))$CMSaddon=WAddon::get('api.'.JOOBI_FRAMEWORK.'.acl');
$CMSrolid=$CMSaddon->updateRoleFromFramework($user );}
if(($newMember || $isnew)){
$memberO->uid=$usersM->uid;
$usersM->setVal('rolid',$CMSrolid );
$usersM->whereE('uid',$memberO->uid );
$usersM->update();
}
$CMSaddon->updateExtraCMSRoles($user, $CMSrolid, $memberO->uid );
if(!isset($CMSaddon))$CMSaddon=WAddon::get('api.'.JOOBI_FRAMEWORK.'.acl');
$roleToSuncA=$CMSaddon->getEquivalentRoles();
if(!empty($roleToSuncA)){
$cmsRole2SuncA=$CMSaddon->getCMSRoles( array_keys($roleToSuncA), true, false);
if(!empty($cmsRole2SuncA) && !empty($user['groups'])){
$usersSyncroleC=WClass::get('users.syncrole');
$role2UpdateA=array();
foreach($user['groups'] as $oneGroup){
foreach($cmsRole2SuncA as $oneRoleInfoO){
if($oneRoleInfoO->j16==$oneGroup){
$role2UpdateA[$oneRoleInfoO->namekey]=true;
}
}
}
if(!empty($role2UpdateA)){
$reversedRole2UpdateA=array_keys($role2UpdateA );
foreach($roleToSuncA as $joobiRole=> $CMSRole){
if( in_array($joobiRole, $reversedRole2UpdateA ))$usersSyncroleC->updateThisRole($joobiRole, $CMSRole );
}$usersSyncroleC->process();
}
}
}
WUser::get( null, 'reset');
$usersSessionC=WUser::session();
$usersSessionC->resetUser();
$cacheHandler=WCache::get();
$cacheHandler->resetCache();
WGlobals::set('syncUserFlag', true, 'global');
$usersM->newUser=($isnew )?true : false;
WController::trigger('users','onRoleUpdated',$usersM );
return $memberO->uid;
}
public function clearSession($space='site'){
$client_id=($space=='site')?0 : 1;
$componentM=WTable::get('session','','session_id');
if($space !='all')$componentM->whereE('client_id',$client_id );
$componentM->delete();
}
public function updateSession($uid,$ip){
$sessionM=WModel::get('library.session');
$sessionM->whereE('sessid', WUser::getSessionId());
$exit=$sessionM->exist();
if($exit){
$sessionM->whereE('sessid', WUser::getSessionId());
$sessionM->setVal('uid',$uid );
$sessionM->setVal('framework', WApplication::$ID );
$sessionM->setVal('modified', time());
if(!empty($ip))$sessionM->setVal('ip',$ip, 0, null, 'ip');
$sessionM->update();
}else{
$sessionM->setVal('sessid', WUser::getSessionId());
$sessionM->setVal('uid',$uid );
$sessionM->setVal('framework', WApplication::$ID );
$sessionM->setVal('created', time());
$sessionM->setVal('modified', time());
if(!empty($ip))$sessionM->setVal('ip',$ip, 0, null, 'ip');
$sessionM->insert();
}
if( WPref::load('PLIBRARY_NODE_SESSION_CLEAR') > time()){
$myTime=( JOOBI_SESSION_LIFETIME < 1 )?5 : JOOBI_SESSION_LIFETIME;
$expiredTime=time() - $myTime * 60; $sessionM->noValidate();
$sessionM->where('modified','<=' , $expiredTime );
$sessionM->whereE('framework', WApplication::$ID );
$sessionM->delete();
$libraryPref=WPref::get('library.node');
$libraryPref->updatePref('next_clear', time() + $myTime * 60 );
}
}
public function deleteSession($sessionID=null){
if(empty($sessionID)){
$sessionID=WUser::getSessionId();
}
$componentM=WTable::get('session','','session_id');
if(is_array($sessionID))$componentM->whereIn('session_id',$sessionID );
else $componentM->whereE('session_id',$sessionID );
$componentM->delete();
}
public function destroySession(){
$session=JFactory::getSession();
return $session->destroy();
}
public function deleteUser($object=null){
if(empty($object->id)) return true;
$id=$object->id;
JPluginHelper::importPlugin('user');
$dispatcher=JDispatcher::getInstance();
$table=JTable::getInstance('User','JTable',array());
$dispatcher->trigger('onUserBeforeDelete',array($table->getProperties()) );
if(!$table->delete($id)){
return false;
}else{
$user_to_delete=JFactory::getUser($id);
$notUsed=null;
$dispatcher->trigger('onUserAfterDelete',array($user_to_delete->getProperties(), true, $notUsed ));
}
}
public function blockUser($block=1,$uidA=null){
if(empty($uidA )){
$user=JFactory::getUser();
$currentID=$user->get('id', 0 );
$uidA=array($currentID );
$onlyCurrent=true;
}else{
if(!is_array($uidA))$uidA=array($uidA );
}
foreach($uidA as $uid){
if(empty($currentID))$currentID=WUser::get('id',$uid );
if(empty($currentID)) continue;
$joomlaUsersM=WModel::get('joomla.users');
$joomlaUsersM->whereE('id',$currentID );
$joomlaUsersM->setVal('block',$block );
$joomlaUsersM->update();
$sessionT=WTable::get('session');
$sessionT->whereE('userid',$currentID );
$sessionT->delete();
if(!empty($onlyCurrent )){
$app=JFactory::getApplication();
$result=$app->logout($currentID );
}
$currentID=null;
}
return true;
}
}