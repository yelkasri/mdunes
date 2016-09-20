<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
WLoadFile('users.class.parent', JOOBI_DS_NODE );
class Users_Joomla_addon extends Users_Parent_class {
public function getUser($userId,$loadFrom=''){
if(empty($userId)) return null;
$colID=(empty($loadFrom))?'id' : $loadFrom;
$key=trim($userId.'-'.$colID );
$key=(string)$key;
if(isset(self::$myMemberUID[$key])) return self::$myMemberUID[$key];if(isset(self::$myMemberID[$key])) return self::$myMemberID[$key];
if(isset(self::$myMemberEmail[$key])) return self::$myMemberEmail[$key];
if(isset(self::$myMemberUsername[$key])) return self::$myMemberUsername[$key];
$userM=WModel::get('users','object', null, false);
if(empty($userM)){
WMessage::log($userId, 'install-missing-users-model');
WMessage::log( debugB(), 'install-missing-users-model');
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
return $usersAddon->goLogin($itemId, $message );
}
public function goRegister($itemId=null){
$usersAddon=WAddon::get('api.'. JOOBI_FRAMEWORK.'.user');
return $usersAddon->goRegister($itemId );
}
public function checkPlugin(){
WApplication::enable('plg_users_user_plugin', 1, 'plugin');
}
public function ghostAccount($email,$password,$name='',$username=null,$automaticLogin=false,$createJoobiUser=true,$sendPwd=false,$extraPrams=null){
$usersAddon=WAddon::get('api.'.JOOBI_FRAMEWORK.'.user');
return $usersAddon->ghostAccount($email, $password, $name, $username, $automaticLogin, $createJoobiUser, $sendPwd, $extraPrams );
}
public function addUserRedirect(){
$usersAddon=WAddon::get('api.'. JOOBI_FRAMEWORK.'.user');
return $usersAddon->addUserRedirect();
}
public function showUserProfile($eid,$onlyLink=false){
$usersAddon=WAddon::get('api.'.JOOBI_FRAMEWORK.'.user');
return $usersAddon->showUserProfile($eid, $onlyLink );
}
public function editUserRedirect($eid,$onlyLink=false){
$usersAddon=WAddon::get('api.'.JOOBI_FRAMEWORK.'.user');
return $usersAddon->editUserRedirect($eid, $onlyLink );
}
public function getPicklistElement(){
$usersAddon=WAddon::get('users.'.JOOBI_FRAMEWORK );
return $usersAddon->getPicklistElement();
}
public function automaticLogin($username,$password,$url='',$pageId=0){
$usersAddon=WAddon::get('api.'. JOOBI_FRAMEWORK.'.user');
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