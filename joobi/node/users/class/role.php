<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
 class Users_role_class extends WClasses {
function getUsersByRole($namekey){
$rolid=WRole::getRole($namekey );
$usersM=WModel::get('users.role');
$usersM->select('uid');
$usersM->whereE('rolid',$rolid);
$uids=$usersM->load('lra');
return $uids;
}
}