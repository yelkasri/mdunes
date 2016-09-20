<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Api_Joomla30_Import_addon {
public function importCMSUsers($updateAsWell=false){
$totalUsers=0;
if(!$updateAsWell){
$usersM=WModel::get('users','object');
if( is_object($usersM))$totalUsers=$usersM->total();
else $totalUsers=1; }
if(!$updateAsWell && empty($totalUsers)){
$query='SELECT `id`, `name`, `username`, `email`, `block`, 1, UNIX_TIMESTAMP(`registerDate`) AS `registerdate`, UNIX_TIMESTAMP(`lastvisitDate`) AS `login` FROM `#__users` ';
$query2='INSERT IGNORE `#__members_node` ( `id`, `name`, `username`, `email`, `blocked`, `rolid`, `registerdate`, `login` ) '.$query;
$usersT=WTable::get('members_node','','uid');
$usersT->load('q',$query2 );
$this->_synchronizeRoles();
}else{
$query2='UPDATE IGNORE `#__members_node` As M LEFT JOIN `#__users` AS U ON M.`email`=U.`email` SET M.`id`=U.`id` WHERE M.`id`=0;';
$usersT=WTable::get('members_node','','uid');
$usersT->load('q',$query2 );
$query2='UPDATE IGNORE `#__members_node` As M LEFT JOIN `#__users` AS U ON M.`id`=U.`id` SET M.`name`=U.`name`, M.`email`=U.`email`, M.`blocked`=U.`block` WHERE M.`id` > 0;';
$usersT=WTable::get('members_node','','uid');
$usersT->load('q',$query2 );
$MAX_USERS=10000;
$joomlaUsersM=WModel::get('joomla.users');
$joomlaUsersM->makeLJ('users','id','id');
$joomlaUsersM->select( array('name','username','email','block','id'), 0 );
$joomlaUsersM->isNull('id', true, 1 );
$joomlaUsersM->setLimit($MAX_USERS );$allNewUsersA=$joomlaUsersM->load('ol');
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
$userM->uid=0;
$userM->id=$oneUser->id;
$userM->name=$oneUser->name;
$userM->email=$oneUser->email;
$userM->username=$oneUser->username;
$userM->blocked=$oneUser->block;
$userM->rolid=1;
$userM->returnId();
$userM->setEmailValidation(false);
$userM->updateFrameworkUser(false);$userM->save();
if(!empty($userM->uid))$usersUIDA[]=$userM->uid;
}if(!empty($usersUIDA))$this->_synchronizeRoles($usersUIDA );
}
}
}
private function _getAllRoles(){
static $allRolesA=null;
if(!empty($allRolesA)){
return $allRolesA;
}
$joobiCoreRolesA=array('registered','author','editor','publisher','manager','admin','sadmin');
$roleM=WModel::get('role');
$roleM->whereIn('namekey',$joobiCoreRolesA );
$roleM->orderBy('lft');
$allRolesA=$roleM->load('ol',array('rolid','namekey'));
return $allRolesA;
}
private function _synchronizeRoles($usersUIDA=null){
$aclGroupUserM=WModel::get('joomla.aclgroupuser');
$aclGroupUserM->makeLJ('users','user_id','id' , 0, 1 );
$aclGroupUserM->makeLJ('role','group_id','j16' , 0, 2 );
if(!empty($usersUIDA))$aclGroupUserM->whereIn('uid',$usersUIDA, 1 );
$aclGroupUserM->isNull('uid', false, 1);
$aclGroupUserM->isNull('rolid', false, 2);
$aclGroupUserM->select('uid', 1);
$aclGroupUserM->select('rolid', 2);
$query=$aclGroupUserM->printQ();
$roleMembersM=WModel::get('users.role');
$roleMembersM->setIgnore();
$roleMembersM->insertSelect(array('uid','rolid'), $query );
$allRolesA=$this->_getAllRoles();
if(empty($allRolesA)) return;
$usersRoleM=WModel::get('users.role');
$usersM=WModel::get('users');
foreach($allRolesA as $oneRole){
$usersM->resetAll();
$usersRoleM->resetAll();
$usersRoleM->makeLJ('role','rolid','rolid');
$usersRoleM->whereE('namekey',$oneRole->namekey, 1 );
$usersRoleM->select('uid', 0);
$inA=$usersRoleM->printQ('load','lra');
$usersM->whereIn('uid',$inA, 0, false, 0,0,0, true, 0 );
if(!empty($usersUIDA))  $usersM->whereIn('uid',$usersUIDA );
$usersM->setVal('rolid',$oneRole->rolid );
$usersM->update();
}
}
}