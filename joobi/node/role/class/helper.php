<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Role_Helper_class extends WClasses {
static private $_hasRoleCache=false;
static private $_getRolesCache=false;
public $newRoleClassUsage=true;
public function getUserRoles($uid=null,$rolid=0){
static $rolesA=array();
if(!isset($uid))$uid=WUser::get('uid');
if(!self::$_getRolesCache){
self::$_getRolesCache=true;
$rolesA=array();
}
if(empty($uid)){
if(empty($rolesA[0])){
if( WUser::$ready){
$roleM=WModel::get('role','object', null, false);
if($roleM->isReady()){
$roleM->remember('allusers', true, 'Model_role_node');
$roleM->whereE('namekey','allusers');
$allUsersRole=$roleM->load('lr','rolid');
$rolesA[0]=array($allUsersRole );
}}else{
$rolesA[0]=array( 1 );
}
}
return $rolesA[0];
}else{
if(isset($rolesA[$uid])) return $rolesA[$uid];
$rolemodelmembers=WModel::get('users.role');
$rolemodelmembers->whereE('uid',$uid );
$rolemodelmembers->setLimit( 10000 );
$rolids=$rolemodelmembers->load('lra','rolid');
if(empty($rolid)){
if( WUser::$ready){
$rolid=WUser::get('rolid',$uid );
}else{
$memberM=WModel::get('users');
$memberM->whereE('uid',$uid );
$rolid=$memberM->load('lr','rolid');
}
}
if(!empty($rolid)){
if(!empty($rolids)){
if(!in_array($rolid, $rolids ))$rolids[]=$rolid;
}else{
$rolids[]=$rolid;
}}else{
$message=WMessage::get();
$message->codeE('The User does not have any role define in the members_node table. It should not be this way.');
$rolids[]=1;
}
$rolesA[$uid]=$this->getChildRoles($rolids, true, true);
}
return $rolesA[$uid];
}
public function getChildRoles($rolid=1,$included=true,$parent=false){
static $childRolesA=array();
if(empty($rolid))$rolid=1;
if($parent){
$sign='>';
}else{
$sign='<';
}
if($included){
$sign .='=';
}
$key=$sign.'.'.serialize($rolid );
if(!isset($childRolesA[$key])){
$roleM=WModel::get('role');
$roleM->makeLJ('role','lft','lft', 0, 1, $sign );
$roleM->where('rgt',$sign,'rgt',1,0);
if( is_numeric($rolid)){
$roleM->remember('child'.$sign . $rolid, true, 'Model_role_node');
$roleM->whereE('rolid',$rolid);
}elseif( is_string($rolid)){
$roleM->remember('child'.$sign . $rolid, true, 'Model_role_node');
$roleM->whereE('namekey',$rolid);
}elseif(is_array($rolid)){
$roleM->whereIn('rolid',$rolid);
}$roleM->setDistinct();
$roleM->orderBy('lft','ASC' ,1 );
$roleM->select('rolid',1);
$roleM->setLimit( 10000 );
$childRolesA[$key]=$roleM->load('lra');
}
return $childRolesA[$key];
}
public function hasRole($namekey,$uid=0,$onlyMainRole=false){
static $resultA=array();
if(!self::$_hasRoleCache){
$resultA=array();
self::$_hasRoleCache=true;
}
if(empty($uid))$uid=WUser::get('uid');
if(!is_numeric($namekey)){
$rolid=$this->getRole($namekey);
}else{
$rolid=$namekey;
}
if(empty($uid)){
if($rolid <=1 ) return true;
else return false;
}
$key=$uid.'-'.$rolid.'-'.$onlyMainRole; if(!isset($resultA[ $key ] )){
if($onlyMainRole)$rolidsA=array( WUser::get('rolid',$uid ));
$rolidsA=$this->getUserRoles($uid );
if(empty($rolidsA)) return false;
$resultA[ $key ]=( in_array($rolid, $rolidsA ))?true : false;
}
return $resultA[ $key ];
}
public function getRoleUsers($rolid,$maps=array()){
if(empty($rolid)) return false;
$uids=array();
$roleHelperC=WRole::get();
$rolids=$roleHelperC->getChildRoles($rolid);
if(empty($rolids)){
$message=WMessage::get();
$message->codeE('The rolid "'.$rolid.'" does not exist');
return $uids;
}
$membersModel=WModel::get('users.role');
$membersModel->whereIn('rolid',$rolids);
$membersModel->setDistinct();
if(empty($maps)){
$membersModel->select('uid');
$uidsFromURolesA=$membersModel->load('lra');
}else{
$membersModel->makeLJ('users','uid','uid');
$membersModel->select($maps,1);
$uidsFromURolesA=$membersModel->load('ol');
}
$userM=WModel::get('users');
$userM->whereIn('rolid',$rolids);
if(empty($maps)){
$userM->select('uid');
$uidsFromUsersA=$userM->load('lra');
}else{
$userM->select($maps);
$uidsFromUsersA=$userM->load('ol');
}
if(!empty($uidsFromURolesA)){
if(!empty($uidsFromUsersA)){
$newArrayRolesA=array();
foreach($uidsFromURolesA as $oneUser){
if(!empty($oneUser->uid))$newArrayRolesA[$oneUser->uid]=$oneUser;
}$uidsFromUsersA2=$uidsFromUsersA;
foreach($uidsFromUsersA2 as $oneUser){
if(!empty($oneUser->uid) && !isset($newArrayRolesA[$oneUser->uid])){
$uidsFromUsersA[]=$oneUser;
}}return $uidsFromUsersA;
}else{
return $uidsFromURolesA;
}
}else{
return $uidsFromUsersA;
}
}
public function getRole($namekey,$return='rolid'){
static $rolesA=array();
if(empty($namekey)) return false;
$key=$namekey .'-'. $return;
if(!isset($rolesA[$key])){
$roleM=WModel::get('role');
$roleM->makeLJ('roletrans','rolid');
$lgid=WUser::get('lgid');
if(empty($lgid))$lgid=1;
$roleM->whereLanguage( 1, $lgid );
$roleM->remember('Role'.$lgid.'-'.$key, true, 'Model_role_node');
if(!is_numeric($namekey))$roleM->whereE('namekey',$namekey );
else $roleM->whereE('rolid',$namekey );
$rolesA[$key]=$roleM->load('o');}
if($return=='object')$returnRole=$rolesA[$key];
else $returnRole=(isset($rolesA[$key]->$return)?$rolesA[$key]->$return : null );
return $returnRole;
}
public function reloadSession($uid=null){
self::$_hasRoleCache=false;
self::$_getRolesCache=false;
$currentUser=WUser::get('uid');
if(empty($uid) || $currentUser==$uid){
$rolids=$this->getUserRoles( null );
$memberObj=WGlobals::get('JoobiUser', null, 'session');
$memberObj->rolids=$rolids;
WGlobals::set('JoobiUser',$memberObj, 'session');
}else{
}
}
public function compareRole($rolid1,$rolid2){
static $results;
if( is_numeric($rolid2) && $rolid2 < 1)$rolid2=1;
if( is_numeric($rolid1) && $rolid1 < 1)$rolid1=1;
$rolid1=strtolower($rolid1);
$rolid2=strtolower($rolid2);
if(isset($results[$rolid1][$rolid2])){
if($results[$rolid1][$rolid2]==='null') return null;
else return $results[$rolid1][$rolid2];
}
$roleModel=WModel::get('role');
$roleModel->remember('compare'.$rolid1.'-'.$rolid2, true, 'Model_role_node');
if(is_numeric($rolid1)){
$roleModel->whereE('rolid',$rolid1);
}else{
$roleModel->whereE('namekey',$rolid1);
}
if(is_numeric($rolid2)){
$roleModel->whereE('rolid',$rolid2,0,null,0,0,1);
}else{
$roleModel->whereE('namekey',$rolid2,0,null,0,0,1);
}
if($rolid1===$rolid2 ) return true;
$roleModel->setDistinct();
$roleModel->setLimit( 1000 );
$myRoles=$roleModel->load('ol',array('rolid','namekey','lft','rgt'));
$countResult=count($myRoles);
if($countResult !=2){
if($countResult==1){
if($myRoles[0]->rolid===$rolid1 && $myRoles[0]->namekey===$rolid2){
$results[$rolid1][$rolid2]=true;
$results[$rolid2][$rolid1]=true;
return true;
}
if($myRoles[0]->rolid===$rolid2 && $myRoles[0]->namekey===$rolid1){
$results[$rolid1][$rolid2]=true;
$results[$rolid2][$rolid1]=true;
return true;
}
}
$message=WMessage::get();
$message->codeE('One of the two roles is not found so we cannot compare them in the function compareRole()');
$message->codeE('rolid1: '.$rolid1.' and rolid2: '.$rolid2.', check if the role you are using is a valid role!');
$results[$rolid1][$rolid2]='null';
$results[$rolid2][$rolid1]='null';
return null;
}
foreach($myRoles as $oneRole){
if($oneRole->rolid===$rolid1 OR $oneRole->namekey===$rolid1){
$firstRole=$oneRole;
}else{
$secondRole=$oneRole;
}
}
if($firstRole->lft >=$secondRole->lft AND $firstRole->rgt <=$secondRole->rgt){
$results[$rolid1][$rolid2]=true;
$results[$rolid2][$rolid1]=false;
return true;
}
if($firstRole->lft < $secondRole->lft AND $firstRole->rgt > $secondRole->rgt){
$results[$rolid1][$rolid2]=false;
$results[$rolid2][$rolid1]=true;
return false;
}
$results[$rolid1][$rolid2]='null';
$results[$rolid2][$rolid1]='null';
return null;
}
function getMainRole($rolids=array()){
if(empty($rolids)) return 0;
if(count($rolids)==1) return reset($rolids);
$roleM=WModel::get('role');
$key=serialize($rolids );
$roleM->remember('getMain'.$key, true, 'Model_role_node');
$roleM->whereIn('rolid',$rolids);
$roleM->makeLJ('role','lft','lft',0,1,'>=');
$roleM->whereOn('rgt','>=','rgt',1,0);
$roleM->whereE('core',1,1);
$roleM->orderBy('lft','DESC',1);
$roleM->select('rgt',1);
$roleM->select('lft',1);
$highestCoreRole=$roleM->load('o');
$roleM->remember('getMain'.$key.'lft'.$highestCoreRole->lft.'rgt'.$highestCoreRole->rgt, true, 'Model_role_node');
$roleM->whereIn('rolid',$rolids );
$roleM->where('lft','>=',$highestCoreRole->lft );
$roleM->where('rgt','<=',$highestCoreRole->rgt );
$roleM->whereE('core',1 );
$roleM->orderBy('lft','DESC');
$mainRolid=$roleM->load('lr',array('rolid'));
return $mainRolid;
}
function insertRole($roleInfo=null){
if(!isset($roleInfo)) return false;
static $roleM=null;
$rolid='0';
if(!isset($roleM))$roleM=WModel::get('role');
if(!empty($roleInfo->namekey)){
$roleM->namekey=$roleInfo->namekey;
$parent=$this->getRole('registered','rolid');
$roleM->parent=(!empty($roleInfo->parent))?$roleInfo->parent: $parent;
$roleM->type=(!empty($roleInfo->type))?$roleInfo->type : 1;
$roleM->x['position']=(!empty($roleInfo->position))?$roleInfo->position : 1; 
$roleInfo->name=(!empty($roleInfo->name))?$roleInfo->name : $roleInfo->namekey;
$roleM->setChild('roletrans','name',$roleInfo->name );
$roleInfo->description=(!empty($roleInfo->description ))?$roleInfo->description : $roleInfo->namekey;
$roleM->setChild('roletrans','description',$roleInfo->description);
$roleM->returnId();
$roleM->save();
$rolid=$roleM->rolid;
}
return $rolid;
}
}