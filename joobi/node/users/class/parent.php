<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Users_Parent_class extends WClasses {
public static $myMemberUID=array();
public static $myMemberID=array();
public static $myMemberEmail=array();
public static $myMemberUsername=array();
public function getUser($userId,$loadFrom=''){if(empty($userId)) return null;
$colID=(empty($loadFrom))?'id' : $loadFrom;
$key=trim($userId.'-'.$colID );
$key=(string)$key;
if(isset(self::$myMemberUID[$key])) return self::$myMemberUID[$key];if(isset(self::$myMemberID[$key])) return self::$myMemberID[$key];
if(isset(self::$myMemberEmail[$key])) return self::$myMemberEmail[$key];
if(isset(self::$myMemberUsername[$key])) return self::$myMemberUsername[$key];
$userM=WModel::get('users','object', null, false);
if(empty($userM)){
WMessage::log($userId, 'install-missing-users-model');
WMessage::log( debugB( 8246711 ), 'install-missing-users-model');
WMessage::log('install-missing-users-model','install');
return false;
}
$userM->rememberQuery(false);
$userM->select('*', 0 );
$userM->select('ip', 0, 'previousip','ip');
if( is_numeric($userId)){
$userM->whereE($colID, $userId );
}else{
$userM->whereE('email',$userId );
$userM->operator('OR');
$userM->whereE('username',$userId );
}
$userM->select('id', 0, 'id');
$theMember=$userM->load('o');
if(empty($theMember)){
$theMember=false;
self::$myMemberID[$userId.'-'.$colID]=$theMember;
}else{
self::$myMemberID[$theMember->id.'-id']=$theMember;
self::$myMemberUID[$theMember->uid.'-uid']=$theMember;
self::$myMemberEmail[$theMember->email.'-uid']=$theMember;
self::$myMemberUsername[$theMember->username.'-uid']=$theMember;
}
return $theMember;
}
public function goLogin($itemId=null,$message=''){
$usersAddon=WAddon::get('api.'.JOOBI_FRAMEWORK.'.user');
if(empty($usersAddon) || ! method_exists($usersAddon, 'goLogin')) return false;
return $usersAddon->goLogin($itemId, $message );
}
public function goRegister($itemId=null){
$usersAddon=WAddon::get('api.'. JOOBI_FRAMEWORK.'.user');
if(empty($usersAddon) || ! method_exists($usersAddon, 'goRegister')) return false;
return $usersAddon->goRegister($itemId );
}
public function goLogout($itemId=null,$message=''){
$usersAddon=WAddon::get('api.'.JOOBI_FRAMEWORK.'.user');
if(!empty($usersAddon) && method_exists($usersAddon, 'logout')){
$usersAddon->logout();
}
$logout=WPref::load('PUSERS_NODE_LOGOUT_LANDING');
if(empty($logout))$logout='home';
WPages::redirect($logout );
}
public function goProfile($uid=null){
if(empty($uid))$uid=WUser::get('uid');
$this->showUserProfile($uid, false);
}
public function checkPlugin(){
}
public function ghostAccount($email,$password,$name='',$username=null,$automaticLogin=false,$createJoobiUser=true,$sendPwd=false,$extraPrams=null){
$usersAddon=WAddon::get('api.'.JOOBI_FRAMEWORK.'.user');
if(empty($usersAddon) || ! method_exists($usersAddon, 'ghostAccount')) return false;
return $usersAddon->ghostAccount($email, $password, $name, $username, $automaticLogin, $createJoobiUser, $sendPwd, $extraPrams );
}
public function addUserRedirect(){
$usersAddon=WAddon::get('api.'. JOOBI_FRAMEWORK.'.user');
if(empty($usersAddon) || ! method_exists($usersAddon, 'addUserRedirect')) return false;
return $usersAddon->addUserRedirect();
}
public function showUserProfile($eid,$onlyLink=false){
$controller=WGlobals::get('controller');
$task=WGlobals::get('task');
if('users'==$controller && 'dashboard'==$task)$link=false;
else $link='controller=users&task=dashboard&eid='.$eid;
if($onlyLink ) return $link;
if(!empty($link)) WPages::redirect($link );
}
public function editUserRedirect($eid,$onlyLink=false){
$usersAddon=WAddon::get('api.'.JOOBI_FRAMEWORK.'.user');
if(empty($usersAddon) || ! method_exists($usersAddon, 'editUserRedirect')) return false;
return $usersAddon->editUserRedirect($eid, $onlyLink );
}
public function getPicklistElement(){
}
public function automaticLogin($username,$password,$url='',$pageId=0){
$usersAddon=WAddon::get('api.'. JOOBI_FRAMEWORK.'.user');
if(empty($usersAddon) || ! method_exists($usersAddon, 'automaticLogin')) return false;
return $usersAddon->automaticLogin($username, $password, $url, $pageId );
}
public function checkConfirmationRequired(){
return false;
}
public function getAvatar($uid){
$avatar=WUser::avatar($uid);
return $avatar;
}
}