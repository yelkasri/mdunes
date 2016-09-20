<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Api_Wp4_Import_addon {
public function importCMSUsers($updateAsWell=false){
$totalUsers=0;
$MAX_USERS=10000;
if(!$updateAsWell){
$usersM=WModel::get('users','object');
if( is_object($usersM))$totalUsers=$usersM->total();
else $totalUsers=1; }
if(!$updateAsWell && empty($totalUsers)){
$query='SELECT `ID`, `display_name`, `user_login`, `user_email`, 0, 1 FROM `#__users` ';
$query2='INSERT IGNORE `#__members_node` ( `id`, `name`, `username`, `email`, `blocked`, `rolid` ) '.$query;
$usersT=WTable::get('members_node','','uid');
$usersT->load('q',$query2 );
$wpUsersT=WTable::get('users','','ID');
$wpUsersT->makeLJ('#__members_node','','ID','id', 0, 1 );
$wpUsersT->select('ID', 0 );
$wpUsersT->select('uid', 1 );
$wpUsersT->setLimit($MAX_USERS );$allNewUsersA=$wpUsersT->load('ol');
if( count($allNewUsersA ) >=$MAX_USERS){
$message=WMessage::get();
$message->userW('1341249340REAS',array('$MAX_USERS'=>$MAX_USERS,'$MAX_USERS'=>$MAX_USERS));
}
if(!empty($allNewUsersA)){
$usersUIDA=array();
foreach($allNewUsersA as $oneUser){
$usersUIDA[]=array($oneUser->ID, $oneUser->uid );
}if(!empty($usersUIDA)){
$this->_synchronizeRoles($usersUIDA );
}
}
}else{$query2='UPDATE IGNORE `#__members_node` As M LEFT JOIN `#__users` AS U ON M.`email`=U.`user_email` SET M.`id`=U.`ID` WHERE M.`id`=0;';
$usersT=WTable::get('members_node','','uid');
$usersT->load('q',$query2 );
$query2='UPDATE IGNORE `#__members_node` As M LEFT JOIN `#__users` AS U ON M.`id`=U.`ID` SET M.`name`=U.`display_name`, M.`email`=U.`user_email` WHERE M.`id` > 0;';
$usersT=WTable::get('members_node','','uid');
$usersT->load('q',$query2 );
$wpUsersT=WTable::get('users','','ID');
$wpUsersT->makeLJ('#__members_node','','ID','id', 0, 1 );
$wpUsersT->select( array('user_nicename','user_login','user_email','ID'), 0 );
$wpUsersT->isNull('id', true, 1 );
$wpUsersT->setLimit($MAX_USERS );$allNewUsersA=$wpUsersT->load('ol');
if( count($allNewUsersA ) >=$MAX_USERS){
$message=WMessage::get();
$message->userW('1341249340REAS',array('$MAX_USERS'=>$MAX_USERS,'$MAX_USERS'=>$MAX_USERS));
}
if(!empty($allNewUsersA)){
$userM=WModel::get('users');
$usersUIDA=array();
foreach($allNewUsersA as $oneUser){
$userM->resetAll();
$userM->setIgnore();
$userM->uid=null;
$userM->id=$oneUser->ID;
$userM->name=$oneUser->user_nicename;
$userM->email=$oneUser->user_email;
$userM->username=$oneUser->user_login;
$userM->blocked=0;
$userM->rolid=1;
$userM->returnId();
$userM->setEmailValidation(false);
$userM->updateFrameworkUser(false);$userM->save();
if(!empty($userM->uid)){
$usersUIDA[]=array($oneUser->ID, $userM->uid );
}}if(!empty($usersUIDA)){
$this->_synchronizeRoles($usersUIDA );
}
}
}
}
private function _getAllRoles(){
static $allRolesA=null;
if(!empty($allRolesA)){
return $allRolesA;
}
$joobiCoreRolesA=array('registered','author','editor','admin','sadmin');
$roleM=WModel::get('role');
$roleM->whereIn('namekey',$joobiCoreRolesA );
$roleM->orderBy('lft');
$allRolesA=$roleM->load('ol',array('rolid','namekey'));
return $allRolesA;
}
private function _synchronizeRoles($usersUIDA=null){
if(empty($usersUIDA)) return false;
$CMSaddon=WAddon::get('api.'.JOOBI_FRAMEWORK.'.acl');
foreach($usersUIDA as $aID){
$user=get_userdata($aID[0] );
$roles=$user->roles;
if(empty($roles)) continue;
$toAddRolidA=array();
foreach($roles as $oneR){
$rolid=$CMSaddon->getJoobiRoleFromWPRole($oneR );
if(empty($rolid)) continue;
else $toAddRolidA[]=$rolid;
}
if(!empty($toAddRolidA)){
WUser::addRole($aID[1], $toAddRolidA );
}
}
return true;
}
}