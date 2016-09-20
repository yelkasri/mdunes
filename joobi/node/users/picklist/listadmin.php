<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Users_Listadmin_picklist extends WPicklist {
function create(){
$roleM=WModel::get('role');
$roleM->makeLJ('users','rolid','rolid', 0, 1 );
$roleM->select('uid', 1 );
$roleM->select('name', 1 );
$roleM->whereE('namekey','sadmin', 0 );
$roles=$roleM->load('ol');
if(!empty($roles)){
foreach($roles as $role)$this->addElement($role->uid, $role->name);
}
return true;
}}