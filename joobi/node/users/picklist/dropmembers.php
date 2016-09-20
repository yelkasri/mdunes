<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Users_Dropmembers_picklist extends WPicklist {
function create(){
$sql=WModel::get('users');
$sql->makeLJ('order','uid','uid', 0, 1 );
$sql->makeLJ('roletrans','rolid','rolid', 0, 2);
$sql->select('uid', 0);
$sql->select('name', 0);
$sql->select('email', 0);
$sql->select('name', 2, 'userrole');
$sql->orderBy('rolid','DESC', 0);
$sql->orderBy('name',0);
$sql->groupBy('uid',0);
$sql->setLimit( 1000 );
$customers=$sql->load('ol');
if(!empty($customers)){
$role='';
foreach($customers as $customer)  {
if($customer->userrole !=$role )
{
 if(!empty($customer->userrole))$this->addElement($customer->uid, '--'.$customer->userrole );
else $this->addElement($customer->uid, '--Users that dont have roles');
}
$role=$customer->userrole;
$this->addElement($customer->uid , $customer->name .' ('. $customer->email .')');
}
}
}
}