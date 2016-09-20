<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Role_Usersort_picklist extends WPicklist {
function create(){
$this->operator=1;
$this->bkafter=1;
WGlobals::set('pick-member-role',$this->defaultValue[$this->did]);
$roleM=WModel::get('role'); 
$roleM->makeLJ('roletrans','rolid');
$roleM->whereLanguage(1);
$roleM->select('name', 1);
$roleM->checkAccess();
$roleM->orderBy('lft','ASC');
$roleM->select('rolid');  
$roleM->select('parent');
$roleM->where('type','!=','2');
$roleM->setLimit( 500 );
$myitems=$roleM->load('ol');
if(empty($myitems)) return true;
$parent=array();
$parent['pkey']='rolid';
$parent['parent']='parent';
$parent['name']='name';
$childOrderParent=array();
$list=WOrderingTools::getOrderedList($parent, $myitems, 1, false, $childOrderParent );
foreach($list as $itemList){
if($itemList->rolid==1)$itemList->rolid=0; 
$this->addElement($itemList->rolid, $itemList->name);
}
}}