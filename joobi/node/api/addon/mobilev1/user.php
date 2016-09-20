<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Api_Mobilev1_User_addon {
public function checkPlugin(){
}
public function goLogin($itemId=null,$message=''){
}
public function goRegister($itemId=null){
}
public function addUserRedirect(){
}
public function editUserRedirect($eid,$onlyLink=false){
}
public function ghostAccount($email,$password,$name='',$username='',$automaticLogin=false,$createJoobiUser=true,$extraPrams=null){
return true;
}
public function automaticLogin($username,$password,$url='',$pageId=0){
$usersCredentialC=WUser::credential();
$userO=$usersCredentialC->verifyCredentialsAndLogin($username, $password );
if(empty($userO)) return false;
$userSessionC=WUser::session();
$sessionUser=$userSessionC->setUserSession($userO->uid, false, 'uid');
return $userO->uid;
}
public function logout(){
$this->deleteSession();
$this->destroySession();
$tools=WUser::session();
$tools->setGuest();
return;
}
public function onAfterInitialise(){
}
public function syncUser($user,$isnew,$success,$msg){
}
public function clearSession($space='site'){
$componentM=WModel::get('library.session');
$componentM->delete();
}
public function updateSession($uid,$ip){
return true;
}
public function deleteSession($sessionID=null){
$sessionM=WModel::get('library.session');
$sessionM->whereE('sessid', WUser::getSessionId());
$sessionM->delete();
}
public function destroySession(){
@session_destroy();
$_SESSION=array();
WGlobals::setSession('','','', true);
if( ini_get("session.use_cookies")){
$params=session_get_cookie_params();
setcookie( session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"] );
}
}
}