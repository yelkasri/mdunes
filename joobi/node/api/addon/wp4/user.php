<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Api_Wp4_User_addon {
static private $_onlySyncOnceA=array();
public function checkPlugin(){
}
public function goLogin($pageID=null,$message=''){
$currentURL=WView::getURI(true);
WGlobals::setSession('Joobi_Users_ComeBack','JoomlaUsers',$currentURL );
WGlobals::setSession('Joobi_Users_ComeBackCount','JoomlaUsers', 1 );
$link=JOOBI_SITE.'wp-login.php';
$isPopUp=WGlobals::get('is_popup', false, 'global');
if($isPopUp){
$link .=URL_NO_FRAMEWORK;
$link .='&isPopUp=true';
$pageID=false;
}
WPages::redirect($link, $pageID, false);
}
public function goRegister($pageID=null){
WPages::redirect( JOOBI_SITE.'wp-login.php?action=register',$pageID, false);
}
public function addUserRedirect(){
$option=WApplication::getApp();
WGlobals::setSession('Joobi_Users_ComeBack','JoomlaUsers',$option );
WPage::redirect( JOOBI_SITE.'wp-admin/user-new.php');
}
public function showUserProfile($eid,$onlyLink=false){
$controller=WGlobals::get('controller');
$task=WGlobals::get('task','', null, 'task');
if('users'==$controller && 'dashboard'==$task)$link=false;
else $link='controller=users&task=dashboard&eid='.$eid;
if($onlyLink ) return $link;
if(!empty($link)) WPages::redirect($link );
}
public function editUserRedirect($eid,$onlyLink=false){
$option=WApplication::getApp();
WGlobals::setSession('Joobi_Users_ComeBack','JoomlaUsers',$option );
$cid=WUser::get('id',$eid, 'uid');
$link=JOOBI_SITE.'wp-admin/user-edit.php?user_id='.$cid.'&wp_http_referer='.WPage::linkAdmin('controller=users');
if($onlyLink ) return $link;
if(empty($cid))$this->addUserRedirect();
WPage::redirect($link );
}
public function ghostAccount($email,$password,$name='',$username='',$automaticLogin=false,$createJoobiUser=true,$sendPwd=false,$extraPrams=null){
$data=array();
if(empty($name))$name=$email;
if(empty($username))$username=$email;
$data['user_email']=$email;
$data['display_name']=$name;
$data['user_login']=$username;
$newUser=false;
if(!empty($extraPrams->id)){
$data['ID']=$extraPrams->id;
$newUser=false;
}else{
if(!empty($extraPrams->registerdate))$data['user_registered']=date('Y-m-d H:i:s',$extraPrams->registerdate );
else $data['user_registered']=date('Y-m-d H:i:s');
$newUser=true;
}
if(!empty($password)){
$data['user_pass']=$password;
}if(!isset($data['user_pass']))$data['user_pass']='';
if(!empty($extraPrams->rolid)){
$CMSaddon=WAddon::get('api.'.JOOBI_FRAMEWORK.'.acl');
$newUsertype=$CMSaddon->getCMSRole($extraPrams->rolid );
if(empty($newUsertype)){
$newUsertype=$newUsertype=get_option('default_role');
}}else{
if($newUser){
$newUsertype=$newUsertype=get_option('default_role');
}else{
if(isset($extraPrams->rolid)){
$CMSaddon=WAddon::get('api.'.JOOBI_FRAMEWORK.'.acl');
$newUsertype=$CMSaddon->getCMSRole('allusers');
}else{
$newUsertype=null;
}}}
if(isset($newUsertype))$data['role']=$newUsertype;
WGlobals::set('WP_userPass',$password, 'global');
$user_id=wp_insert_user($data );
if( is_wp_error($user_id)){
$wpUserT=WTable::get('users','','ID');
$wpUserT->whereE('user_email',$email );
$user_id=$wpUserT->load('lr','ID');
if(!$createJoobiUser ) return $user_id;
$usersM=WModel::get('users');
$usersM->email=$email;
$usersM->id=$user_id;
$usersM->name=$name;
$usersM->username=$username;
$usersM->returnId();
$usersM->updateFrameworkUser(false);$usersM->emailNewPassword($sendPwd );
$usersM->save();
if(!empty($usersM->uid )){
return true;
}
return false;
}
$url=(!empty($extraPrams->url )?$extraPrams->url : '');
$pageId=(!empty($extraPrams->pageId )?$extraPrams->pageId : WPage::getPageId());
if($automaticLogin)$this->automaticLogin($data['user_login'], $data['user_pass'], $url, $pageId );
if(!$createJoobiUser){
return $user_id;
}
return true;
}
public function automaticLogin($username,$password,$url='',$pageId=0){
$requestO=WGlobals::getEntireSuperGlobal('request');
if(isset($requestO['controller'] )) unset($requestO['controller'] );
if(isset($requestO['task'] )) unset($requestO['task'] );
$getInfo=serialize($requestO );
WGlobals::setSession('autoLogin','formInfo',$getInfo );
if(empty($url)){
$url=WGlobals::getReturnId();
}
$returnURL=base64_encode($url );
$sendURL='controller=apps-tag&task=render';
$sendURL .='&joobiRedirect=true';
$sendURL .='&log='.$username.'&pas='.$password.'&rememberme=true';
if(!empty($pageId))$sendURL .='&'.JOOBI_PAGEID_NAME.'='.$pageId;
$sendURL .='&rtrn='.$returnURL;
WPage::redirect($sendURL );
}
public function logout(){
wp_logout();
}
public function onAfterInitialise(){
return true;
}
public function syncUser($user,$isnew,$success,$msg=''){
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
$usersM->registerdate=time();
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
if(!empty($user['roles'])){
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
$userSessionC=WUser::session();
$userSessionC->completeInformation($usersM );
if(!empty($usersM->uid))$uid=$usersM->uid;
$rolid=$usersM->rolid;
$usersM->returnId();
$userSyncSendPwd=WGlobals::get('userSyncSendPwd', false, 'global');
if($userSyncSendPwd)$usersM->emailNewPassword($userSyncSendPwd );
$usersM->updateFrameworkUser(false);$status=$usersM->save();
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
if(!$isnew){
if($usersM->uid==WUser::get('uid')){
WUser::get( null, 'reset');
$usersSessionC=WUser::session();
$usersSessionC->resetUser();
}}
$cacheHandler=WCache::get();
$cacheHandler->resetCache('Preference');
WGlobals::set('syncUserFlag', true, 'global');
$usersM->newUser=($isnew )?true : false;
WController::trigger('users','onRoleUpdated',$usersM );
return $memberO->uid;
}
public function clearSession($space='site'){
$componentM=WTable::get('usermeta','','umeta_id');
$componentM->whereE('meta_key','session_tokens');$componentM->delete();
}
public function updateSession($uid,$ip){
$sessionID=WUser::getSessionId();
if(empty($sessionID)) return;
$sessionM=WTable::get('sesion_node','main_userdata','sessid');
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
}
}
public function deleteSession($sessionID=null){
if(empty($sessionID)){
$sessionID=WUser::getSessionId();
}
if(empty($sessionID)) return false;
if(is_array($sessionID)){
$componentM=WTable::get('usermeta','','umeta_id');
foreach($sessionID as $oneSess){
$componentM->whereE('meta_key','session_tokens');
$componentM->whereSearch('meta_value',$oneSess );
$componentM->delete();
}}else{
$componentM=WTable::get('usermeta','','umeta_id');
$componentM->whereE('meta_key','session_tokens');
$componentM->whereSearch('meta_value',$sessionID );
$componentM->delete();
}
}
public function destroySession(){
$sessions=WP_Session_Tokens::get_instance(0);$sessions->destroy_all();
$sessions->destroy( WUser::getSessionId());
@session_destroy();
if( ini_get("session.use_cookies")){
$params=session_get_cookie_params();
setcookie( session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"] );
}
}
public function deleteUser($object=null){
if(empty($object->id)) return false;
wp_delete_user($object->id );
return true;
}
public function blockUser($block=1,$uidA=null){
return true;
}
}