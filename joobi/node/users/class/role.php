<?php 


* @license GNU GPLv3 */

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