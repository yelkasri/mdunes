<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
WLoadFile('category.model.node' ,JOOBI_DS_NODE );
class Role_Node_model extends Category_Node_model {
protected $_parentIdentifier='allusers';
var $_notAssign=false;
var $_childRolid=array();
var $_firstChildRolid=null;
var $_newParent=false;
var $_frameworkClass=null;
function validate(){
$this->core=0;
return parent::validate();
}
function addValidate(){
if($this->getX('position')==2){
$roleC=WRole::get();
$this->_childRolid=$roleC->getChildRoles($this->parent, false);
$modelRole=WModel::get('role');
$modelRole->select('rolid');
$modelRole->whereE('parent',$this->parent);
$modelRole->setLimit( 100000 );
$this->_firstChildRolid=$modelRole->load('lr');
}
if(empty($this->namekey)){
$mytype=WType::get('role.type');
$this->namekey=substr( WGlobals::filter($mytype->getName($this->type), 'path'). time(), 0, 30 );
}
return $this->triggerAPI('addValidate') && parent::addValidate();
}
function addExtra(){
$status=parent::addExtra() && $this->triggerAPI('addExtra');
if(!$this->_notAssign){
$roleMember=WModel::get('users.role');
$roleMember->uid=WUser::get('uid');
$roleMember->rolid=$this->rolid;
$roleMember->setIgnore();
$roleMember->insert();
}
$roles=WRole::get();
$roles->reloadSession();
return $status;
}
function extra(){
$status=parent::extra() && $this->triggerAPI('extra');
if($status && $this->getX('position')==2 && !empty($this->_childRolid)){
$RoleMoveChildM=WModel::get('role');
$RoleMoveChildM->rolid=$this->_firstChildRolid;
$RoleMoveChildM->parent=$this->rolid;
$status=$status && $RoleMoveChildM->save();
unset($RoleMoveChildM->parent );
$RoleMoveChildM->updatePlus('lft', 1);
$RoleMoveChildM->updatePlus('rgt', 1);
$RoleMoveChildM->whereIn('rolid',  $this->_childRolid );
$RoleMoveChildM->update();
$RoleMoveChildM->whereE('rolid',$this->rolid );
$nbBack=-2 * count($this->_childRolid );
$RoleMoveChildM->updatePlus('lft',$nbBack );
$RoleMoveChildM->update();
}
$tools=WUser::session();
$tools->setUserSession( null, true);
return $status && parent::extra();
}
function deleteValidate($eid=0){
if(!empty($eid)){
$RoleM=WModel::get('role');
$RoleM->whereE('rolid',$eid);
$myActualRole=$RoleM->load('o',array('parent','joomla'));
$this->_newParent=$myActualRole->parent;
}
return $this->triggerAPI('deleteValidate') && parent::deleteValidate($eid);
}
function deleteExtra($eid=0){
$status=parent::deleteExtra($eid) && $this->triggerAPI('deleteExtra');
if(!$status) return false;
if(!empty($this->_newParent) && !empty($eid)){
$foreign=WModel::get('library.foreign');
$foreign->makeLJ('library.model','dbtid','dbtid'); 
$foreign->makeLJ('library.columns','feid','dbcid'); 
$foreign->whereE('ref_dbtid',$this->getTableId());
$foreign->where('ondelete','!=',3);
$foreign->where('dbtid','!=',$this->getTableId());
$foreign->whereE('map','rolid');
$foreign->whereE('publish',1);
$foreign->whereE('publish',1,1);
$foreign->groupBy('dbtid');
$foreign->groupBy('name',2);
$foreign->select('sid',1);
$foreign->select('name',2);
$foreign->setLimit( 500 );
$results=$foreign->load('ol');
if(!empty($results )){
foreach($results as $onemodel){
$model=WModel::get($onemodel->sid ,'object');
if(empty($model->_infos->tablename )) continue;
$model->setVal($onemodel->name, $this->_newParent );
$model->whereE($onemodel->name, $eid );
$model->setIgnore();
$status=$model->update();
}
}
}
return  parent::deleteExtra($eid);
}
function triggerAPI($functionName){
if(!empty($this->type) && $this->type==2 ) return true;
if(!isset($this->_frameworkClass))$this->_frameworkClass=WAddon::get('api.'.JOOBI_FRAMEWORK.'.acl');
if( method_exists($this->_frameworkClass,$functionName)){
return $this->_frameworkClass->$functionName($this );
}
return true;
}}