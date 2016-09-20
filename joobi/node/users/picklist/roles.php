<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Users_Roles_picklist extends WPicklist {
function create(){
$roleM=WModel::get('role');
$roleM->makeLJ('roletrans','rolid','rolid');
$roleM->whereE('type', 1 );
$roleM->whereLanguage(1);
$roleM->orderBy('type','asc');
$roleM->orderBy('rolid','asc');
$roleM->select('name', 1);
$roleM->setLimit( 5000 );
$allRolesA=$roleM->load('ol',array('rolid','parent'));
$parent=array();
$parent['pkey']='rolid';
$parent['parent']='parent';
$parent['name']='name';
$childOrderParent=array();
$list=WOrderingTools::getOrderedList($parent, $allRolesA, 1, false, $childOrderParent );
if( WGlobals::get('task','listing')=='listing')$defaultRole=WGlobals::get('rolid');
$defaultRoleText='';
foreach($list as $itemList){
if(empty($itemList->parent))$itemList->rolid=0;if(!empty($itemList->name))$this->addElement($itemList->rolid, $itemList->name );
if(!empty($defaultRole) && $defaultRole==$itemList->rolid)$defaultRoleText=$itemList->name;
}
if(!empty($defaultRoleText)) WGlobals::set('titleheader', str_replace( array(' ','.','L', "&nbsp;", '<sup>','</sup>'), '',$defaultRoleText));
return true;
}}