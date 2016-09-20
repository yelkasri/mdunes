<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
 class Users_Syncrole_class extends WClasses {
var $userRolesA=array();var $uid=0;
var $changedRoled=false;
var $roleToCheck=array();
private $_roleToAddA=array();
function updateThisRole($joobiRole,$CMSRole){
$this->roleToCheck[$joobiRole]=$CMSRole;
}
function process(){
if(!empty($this->roleToCheck)){
foreach($this->roleToCheck as $joobiRole=> $CMSRole){
$this->_addRoleIfRequired($joobiRole, $CMSRole );
}}else{
return false;
}
if($this->changedRoled && !empty($this->_roleToAddA)){
if(empty($this->uid))$this->uid=WUser::get('uid');
if(empty($this->uid) && defined('JOOBI_INSTALLING') && JOOBI_INSTALLING){
$usersM=WModel::get('users');
$this->uid=$usersM->load('lr','uid');
if(empty($this->uid)){
if( JOOBI_FRAMEWORK_TYPE=='joomla'){
$this->uid=2;
}else{
$this->uid=1;
}}
}
foreach($this->_roleToAddA as $oneRoild){
WUser::addRole($this->uid, $oneRoild );
}
}
}
private function _addRoleIfRequired($roleToAdd,$roleRequiredToHave){
static $role=null;
if(!isset($role))$role=WRole::get();
$roleRequiredToHaveA=$role->getChildRoles($roleRequiredToHave );
if(!is_numeric($roleToAdd)){
$roleToAdd=$role->getRole($roleToAdd );
}
$this->_roleToAddA[]=$roleToAdd;
$usersM=WModel::get('users');
$usersM->select((int)$roleToAdd, 0, 'rolid','val');
$usersM->select('uid');
$usersM->whereIn('rolid',$roleRequiredToHaveA );
$selectQuery=$usersM->printQ('load','lra');
$usersRoleM=WModel::get('users.role');
$usersRoleM->insertSelect( array('rolid','uid'), $selectQuery, false, true);
$this->changedRoled=true;
return true;
}
}