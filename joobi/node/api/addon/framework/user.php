<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Api_Framework_User_addon {
public function checkPlugin(){
}
public function goLogin($itemId=null,$message=''){
$currentURL=WView::getURI('true');
WGlobals::setSession('Joobi_Users_ComeBack','JoomlaUsers',$currentURL );
WGlobals::setSession('Joobi_Users_ComeBackCount','JoomlaUsers', 1 );
$link=JOOBI_SITE.'index.php?option=com_users&view=login';
$isPopUp=WGlobals::get('is_popup', false, 'global');
if($isPopUp){
$link .=URL_NO_FRAMEWORK;
$link .='&isPopUp=true';
$itemId=false;
}WPages::redirect($link, $itemId, false);
}
public function goRegister($itemId=null){
WPages::redirect( JOOBI_SITE.'index.php?option=com_users&view=registration',$itemId, false);
}
public function addUserRedirect(){
$option=WApplication::getApp();
WGlobals::setSession('Joobi_Users_ComeBack','JoomlaUsers',$option );
WPages::redirect('index.php?option=com_users&task=user.add');
}
public function editUserRedirect($eid,$onlyLink=false){
$option=WApplication::getApp();
WGlobals::setSession('Joobi_Users_ComeBack','JoomlaUsers',$option );
$cid=WUser::get('id',$eid, 'uid');
if(empty($cid))$this->addUserRedirect();
WPages::redirect('index.php?option=com_users&task=user.edit&id='.$cid );
}
public function ghostAccount($email,$password,$name='',$username='',$automaticLogin=false,$createJoobiUser=true,$extraPrams=null){
$config=JFactory::getConfig();
$params=JComponentHelper::getParams('com_users');
$user=new JUser;
$data=array();
if(empty($name))$name=$email;
if(empty($username))$username=$email;
$data['email']=$email;
$data['name']=$name;
$data['username']=$username;
$data['registerDate']=date('Y-m-d H:i:s');
$data['lastvisitDate']=date('Y-m-d H:i:s');
if(!empty($password)){
$data['password']=$password;
$data['password2']=$password;
}
$useractivation=$params->get('useractivation');
$data['activation']='';
$data['block']=0;
$data['groups']=array( 2 );
if(!$user->bind($data)){
$user->setError(JText::sprintf('COM_USERS_REGISTRATION_BIND_FAILED',$user->getError()));
return false;
}
JPluginHelper::importPlugin('user');
if(!$user->save()){
$user->setError(JText::sprintf('COM_USERS_REGISTRATION_SAVE_FAILED',$user->getError()));
return false;
}
if($automaticLogin)$this->automaticLogin($user->username, $user->password_clear );
return true;
}
public function automaticLogin($username,$password,$url='',$pageId=0){
$usersCredentialC=WUser::credential();
$userO=$usersCredentialC->verifyCredentialsAndLogin($username, $password );
if(empty($userO)) return false;
return $userO->uid;
}
public function logout(){
$this->destroySession();
}
public function onAfterInitialise(){
if( IS_ADMIN){
$option=WApplication::getApp();
if($option=='users'){
$task=WGlobals::get('view');
$goTo=WGlobals::getSession('Joobi_Users_ComeBack','JoomlaUsers','');
if(!empty($goTo) && ($task=='users'  )){WGlobals::setSession('Joobi_Users_ComeBack','JoomlaUsers','');
WPages::redirect('index.php?option='.$goTo.'&controller=users');
}}
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
$syncUser=WGlobals::get('syncUserFlag', false, 'global');
if($syncUser ) return false;
 if($success===false) return false;
$usersM=WModel::get('users');
 if(!$isnew){
$usersM->whereE('id',$user['id'] );
$memberO=$usersM->load('o',array('id','uid','rolid'));
 }else{
 $memberO=new stdClass;
 }
if(empty($memberO->uid)){$usersM->uid=0;$usersM->id=$user['id'];
$usersM->registerdate=strtotime($user['registerDate'] );
if(!empty($user['lastvisitDate']))$usersM->login=strtotime($user['lastvisitDate'] );
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
}}
$userSessionC=WUser::session();
$userSessionC->completeInformation($usersM );
if(!empty($usersM->uid))$uid=$usersM->uid;
$rolid=$usersM->rolid;
$usersM->returnId();
$status=$usersM->save();
if(($newMember || $isnew)){$memberO->uid=$usersM->uid;
if(!isset($CMSaddon))$CMSaddon=WAddon::get('api.'.JOOBI_FRAMEWORK.'.acl');
$CMSrolid=$CMSaddon->updateRoleFromFramework($user );
$usersM->setVal('rolid',$CMSrolid );
$usersM->whereE('uid',$memberO->uid );
$usersM->update();
}
if(!isset($CMSaddon))$CMSaddon=WAddon::get('api.'.JOOBI_FRAMEWORK.'.acl');
$roleToSunc=$CMSaddon->getEquivalentRoles();
if(!empty($roleToSunc)){
$usersSyncroleC=WClass::get('users.syncrole');
foreach($roleToSunc as $joobiRole=> $CMSRole)$usersSyncroleC->updateThisRole($joobiRole, $CMSRole );
$usersSyncroleC->process();
}
WUser::get( null, 'reset');
WGlobals::setSession('JoobiUser', null, null );
$cacheC=WCache::get();
$cacheC->resetCache();
WGlobals::set('syncUserFlag', true, 'global');
$usersM->newUser=($isnew )?true : false;
WController::trigger('users','onRoleUpdated',$usersM );
return $memberO->uid;
}
public function updateSession($uid,$ip){
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
}
public function clearSession($space='site'){
$componentM=WModel::get('library.session');
$componentM->delete();
}
public function deleteSession($sessionID=null){
}
public function destroySession(){
$_SESSION=array();
WGlobals::setSession('','','', true);
session_destroy();
if( ini_get("session.use_cookies")){
$params=session_get_cookie_params();
setcookie( session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"] );
}
}
}